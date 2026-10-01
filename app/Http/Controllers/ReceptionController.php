<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Reception;
use App\Models\ReceptionItem;
use App\Models\Provider;
use App\Models\Product; 
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // <-- IMPORTANTE: Agregar Auth
use Barryvdh\DomPDF\Facade\Pdf;

class ReceptionController extends Controller
{
    // Lista estricta de medicamentos controlados para poder identificarlos en recepción
    private $controlados = [
        'Alprazolam 0.5 mg comprimidos',
        'Buprenorfina 35 mcg/h parche',
        'Clonazepam 0.5 mg comprimidos',
        'Clonazepam 2 mg comprimidos',
        'Diazepam 10 mg comprimidos',
        'Diazepam 10 mg/2 ml ampolla',
        'Fenobarbital 100 mg comprimidos',
        'Lorazepam 4 mg/2ml ampolla',
        'Lorazepam 2 mg comprimido sublingual',
        'Midazolam 15 mg/3ml ampolla',
        'Midazolam 5mg/ml ampolla',
        'Metilfenidato Clorhidrato 10 mg comprimidos',
        'Morfina 10mg/ml ampolla',
        'Morfina 20 mg/ml Frasco gotas',
        'Petidina 100 mg/2ml ampolla',
        'Zolpidem 10 mg comprimidos',
        'Recetas Cheque Estupefacientes'
    ];

    public function index()
    {
        $providers = Provider::all();
        // Carga los productos maestros para el desplegable de recepción
        $products = Product::all();
        // Traemos las recepciones con su proveedor y con los items
        $receptions = Reception::with(['provider', 'items.product'])->orderBy('created_at', 'desc')->get();

        $nextCorrelativo = Reception::max('correlativo') + 1;
        $controlados = $this->controlados;

        return view('reception.index', compact('providers', 'receptions', 'products', 'nextCorrelativo', 'controlados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_recepcion' => 'required|date',
            'tipo_documento' => 'required|string',
            'numero_documento' => 'required|string',
            'origen' => 'required|string',
            'provider_id' => 'required|exists:providers,id',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer',
            'cantidad_unitaria' => 'required|array|min:1',
            'cantidad_unitaria.*' => 'required|integer|min:1',
        ]);

        // obtiene información del proveedor para registrar en inventario y trazabilidad
        $provider = Provider::findOrFail($request->provider_id);
        $rutProveedor = $provider->rut ?? 'S/R';
        $nombreProveedor = $provider->name ?? $provider->nombre ?? $provider->razon_social ?? 'Proveedor';

        $correlativo_autogenerado = Reception::max('correlativo') + 1;

        // Iniciamos transacción
        DB::beginTransaction();

        try {
            // 1. Guardar la Cabecera de la Recepción
            $reception = Reception::create([
                'correlativo' => $correlativo_autogenerado,
                'fecha_recepcion' => $request->fecha_recepcion,
                'tipo_documento' => $request->tipo_documento,
                'numero_documento' => $request->numero_documento,
                'orden_compra' => $request->orden_compra,
                'origen' => $request->origen,
                'provider_id' => $request->provider_id,
                'valor_total_recepcion' => 0,
                'firma_receptor' => $request->firma_receptor,
            ]);

            $total_neto_recepcion = 0;

            // 2. Guardar cada Producto asociado (Bucle dinámico)
            foreach ($request->product_id as $index => $prod_id) {
                // obtener el nombre del producto para inventario y trazabilidad
                $product = Product::find($prod_id);
                $nombreProducto = $product ? $product->nombre : 'Producto #' . $prod_id;

                $cantidad = $request->cantidad_unitaria[$index];
                $valor_u = $request->valor_unitario[$index] ?? 0;
                $subtotal_item = $cantidad * $valor_u;
                $loteRecibido = $request->lote[$index] ?? 'S/L';
                $fechaVence = $request->fecha_vencimiento[$index] ?? null;

                // Asignar categoría automáticamente si es un medicamento controlado
                $categoriaProducto = in_array($nombreProducto, $this->controlados) 
                    ? 'Controlado' 
                    : ($request->tipo_producto[$index] ?? 'General');

                $total_neto_recepcion += $subtotal_item;

                // Guardar en reception_items
                ReceptionItem::create([
                    'reception_id' => $reception->id,
                    'product_id' => $prod_id,
                    'tipo_producto' => $categoriaProducto,
                    'lote' => $loteRecibido,
                    'fecha_vencimiento' => $fechaVence,
                    'cantidad' => $cantidad,
                    'valor_unitario' => $valor_u,
                    'valor_total_item' => $subtotal_item * 1.19,
                ]);

                // 3. Lógica Inteligente de Inventario (Acumular o Crear Fila Nueva)
                $inventarioExistente = Inventory::where('producto', $nombreProducto)
                    ->where(function($q) use ($loteRecibido) {
                        if ($loteRecibido == 'S/L') {
                            $q->whereNull('lote')->orWhere('lote', 'S/L')->orWhere('lote', '');
                        } else {
                            $q->where('lote', $loteRecibido);
                        }
                    })->first();

                if ($inventarioExistente) {
                    // Si el lote ya existe, sumamos la cantidad al stock físico actual y registramos el usuario
                    $inventarioExistente->increment('cantidad', $cantidad);
                    $inventarioExistente->update([
                        'valor_neto_unitario' => $valor_u,
                        'user_id' => Auth::id() // <-- Registramos quién hizo la actualización
                    ]);
                } else {
                    // Si es nuevo, creamos el registro y le asignamos el user_id
                    Inventory::create([
                        'user_id' => Auth::id(), // <-- Registramos quién lo ingresó
                        'correlativo_interno' => $correlativo_autogenerado,
                        'fecha_recepcion' => $request->fecha_recepcion,
                        'tipo_documento' => $request->tipo_documento,
                        'numero_documento' => $request->numero_documento,
                        'orden_compra' => $request->orden_compra,
                        'origen_mercaderia' => $request->origen,
                        'rut_proveedor' => $rutProveedor,
                        'razon_social' => $nombreProveedor,
                        'producto' => $nombreProducto,
                        'categoria' => $categoriaProducto,
                        'estado' => 'Cuarentena', // Se asume Cuarentena al ingresar
                        'lote' => $loteRecibido,
                        'fecha_vencimiento' => $fechaVence,
                        'cantidad' => $cantidad,
                        'valor_neto_unitario' => $valor_u,
                        'pasillo' => 'P.A',
                        'estante' => 'P.A',
                    ]);
                }

                // tabla trazabilidad (stock_movements) para cada producto recibido
                StockMovement::create([
                    'producto' => $nombreProducto,
                    'lote' => $loteRecibido,
                    'fecha_vencimiento' => $fechaVence,
                    'tipo_movimiento' => 'Recepción',
                    'cantidad' => $cantidad, // Positivo porque entra a bodega
                    'fecha_hora' => now(),
                    'origen_destino' => 'Desde: ' . $nombreProveedor,
                    'documento_respaldo' => $request->tipo_documento . ' ' . $request->numero_documento,
                    'estado_lote' => 'Cuarentena'
                ]);
            }

            // 4. Actualizar el Gran Total de la Recepción (IVA incluido)
            $reception->update(['valor_total_recepcion' => $total_neto_recepcion * 1.19]);

            // Confirmar transacción
            DB::commit();

            if ($request->action == 'save_and_download') {
                return redirect()->route('reception.pdf', $reception->id);
            }

            return redirect()->route('reception.index')->with('success', 'Recepción guardada exitosamente y registrada en trazabilidad. N°: ' . $correlativo_autogenerado);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ocurrió un error: ' . $e->getMessage());
        }
    }

    public function downloadPdf(Reception $reception)
    {
        $reception->load(['provider', 'items.product']);
        $pdf = Pdf::loadView('reception.pdf', compact('reception'));
        return $pdf->download("recepcion_{$reception->numero_documento}.pdf");
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Reception;
use App\Models\ReceptionItem;
use App\Models\Provider;
use App\Models\Product; // Asegúrate de importar tu modelo de Inventario/Producto
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceptionController extends Controller
{
    public function index()
    {
        $providers = Provider::all();
        // Carga los productos reales de tu tabla de inventario
        $products = Product::all();
        // Traemos las recepciones con su proveedor y con los items (que a su vez traen el producto)
        $receptions = Reception::with(['provider', 'items.product'])->orderBy('created_at', 'desc')->get();

        $nextCorrelativo = Reception::max('correlativo') + 1;

        return view('reception.index', compact('providers', 'receptions', 'products', 'nextCorrelativo'));
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

        // OBTENER INFORMACIÓN DEL PROVEEDOR
        $provider = Provider::findOrFail($request->provider_id);
        $rutProveedor = $provider->rut ?? 'S/R';
        $nombreProveedor = $provider->name ?? $provider->nombre ?? $provider->razon_social ?? 'Proveedor';

        // 1. Guardar la Cabecera de la Recepción
        $correlativo_autogenerado = Reception::max('correlativo') + 1;

        $reception = Reception::create([
            'correlativo' => $correlativo_autogenerado,
            'fecha_recepcion' => $request->fecha_recepcion,
            'tipo_documento' => $request->tipo_documento,
            'numero_documento' => $request->numero_documento,
            'orden_compra' => $request->orden_compra,
            'origen' => $request->origen,
            'provider_id' => $request->provider_id,
            'valor_total_recepcion' => 0,
            'firma_proveedor' => $request->firma_proveedor,
            'firma_receptor' => $request->firma_receptor,
        ]);

        $total_neto_recepcion = 0;

        // 2. Guardar cada Producto asociado (Bucle dinámico)
        foreach ($request->product_id as $index => $prod_id) {
            // OBTENER INFORMACIÓN DEL PRODUCTO
            $product = Product::find($prod_id);
            $nombreProducto = $product ? $product->nombre : 'Producto #' . $prod_id;

            $cantidad = $request->cantidad_unitaria[$index];
            $valor_u = $request->valor_unitario[$index] ?? 0;
            $subtotal_item = $cantidad * $valor_u;

            $total_neto_recepcion += $subtotal_item;

            // Guardar en reception_items
            ReceptionItem::create([
                'reception_id' => $reception->id,
                'product_id' => $prod_id,
                'tipo_producto' => $request->tipo_producto[$index] ?? null,
                'lote' => $request->lote[$index] ?? null,
                'fecha_vencimiento' => $request->fecha_vencimiento[$index] ?? null,
                'cantidad' => $cantidad,
                'valor_unitario' => $valor_u,
                'valor_total_item' => $subtotal_item * 1.19,
            ]);

            // Guardar el movimiento en la tabla inventories
            Inventory::create([
                'correlativo_interno' => $correlativo_autogenerado,
                'fecha_recepcion' => $request->fecha_recepcion,
                'tipo_documento' => $request->tipo_documento,
                'numero_documento' => $request->numero_documento,
                'orden_compra' => $request->orden_compra,
                'origen_mercaderia' => $request->origen,
                'rut_proveedor' => $rutProveedor,
                'razon_social' => $nombreProveedor,
                'producto' => $nombreProducto,
                'categoria' => $request->tipo_producto[$index] ?? 'General',
                'lote' => $request->lote[$index] ?? null,
                'fecha_vencimiento' => $request->fecha_vencimiento[$index] ?? null,
                'cantidad' => $cantidad,
                'valor_neto_unitario' => $valor_u,
            ]);
        }

        // 3. Actualizar el Gran Total de la Recepción (IVA incluido)
        $reception->update(['valor_total_recepcion' => $total_neto_recepcion * 1.19]);

        if ($request->action == 'save_and_download') {
            return redirect()->route('reception.pdf', $reception->id);
        }

        return redirect()->route('reception.index')->with('success', 'Recepción e Ingreso a Inventario guardados exitosamente. N°: ' . $correlativo_autogenerado);
    }

    public function downloadPdf(Reception $reception)
    {
        $reception->load(['provider', 'items.product']);
        $pdf = Pdf::loadView('reception.pdf', compact('reception'));
        return $pdf->download("recepcion_{$reception->numero_documento}.pdf");
    }
}

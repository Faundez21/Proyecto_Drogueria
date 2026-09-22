<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\DispatchItem;
use App\Models\Inventory;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DispatchController extends Controller
{
    public function index()
    {
        // 1. Generar Correlativo Automático No Editable
        $lastDispatch = Dispatch::orderBy('id', 'desc')->first();
        $nextId = $lastDispatch ? $lastDispatch->id + 1 : 1;
        $correlativo = 'GD-' . date('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        // 2. Traer el historial de despachos para la Bandeja
        $dispatches = Dispatch::with('items')->orderBy('created_at', 'desc')->get();

        // 3. Traer el Inventario Disponible ordenado por Vencimiento (Regla FEFO)
        // Usamos la columna 'cantidad' directamente como está en tu base de datos
        $inventarioDisponible = Inventory::where('cantidad', '>', 0)
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        return view('despacho.index', compact('correlativo', 'dispatches', 'inventarioDisponible'));
    }

    public function store(Request $request)
    {
        // Validamos que la solicitud traiga productos
        if (!$request->has('productos') || count($request->productos) === 0) {
            return redirect()->back()->with('error', 'Debe agregar al menos un producto al despacho.');
        }

        // Iniciamos transacción para proteger los datos
        DB::beginTransaction();
        try {
            // 1. Guardar Cabecera del Despacho
            $dispatch = Dispatch::create([
                'correlativo' => $request->correlativo_despacho,
                'fecha_despacho' => $request->fecha_despacho,
                'estado_liberacion' => $request->estado_liberacion,
                'establecimiento_destino' => $request->establecimiento_destino,
                'direccion_destino' => $request->direccion_destino,
                'condiciones_transporte' => $request->condiciones_transporte,
            ]);

            // 2. Procesar cada producto, descontar inventario y trazar movimiento
            foreach ($request->productos as $index => $nombreProducto) {
                $lote = !empty($request->lotes[$index]) ? $request->lotes[$index] : 'S/L';
                $cantidadSolicitada = (int) $request->cantidades[$index];
                $fechaVence = !empty($request->vencimientos[$index]) ? $request->vencimientos[$index] : null;

                // Crear el ítem del despacho (para historial de la guía)
                DispatchItem::create([
                    'dispatch_id' => $dispatch->id,
                    'producto' => $nombreProducto,
                    'lote' => $lote,
                    'fecha_vencimiento' => $fechaVence,
                    'programa' => $request->programas[$index] ?? 'General',
                    'cantidad' => $cantidadSolicitada,
                ]);

                // Buscar el producto exacto en el Inventario para descontar
                $inventory = Inventory::where('producto', $nombreProducto)
                    ->where(function($q) use ($lote) {
                        if($lote == 'S/L') {
                            $q->whereNull('lote')->orWhere('lote', 'S/L')->orWhere('lote', '');
                        } else {
                            $q->where('lote', $lote);
                        }
                    })->first();

                if ($inventory) {
                    if ($inventory->cantidad < $cantidadSolicitada) {
                        throw new \Exception("Stock insuficiente para '{$nombreProducto}'. Stock actual disponible: {$inventory->cantidad}");
                    }
                    
                    // REBAJAMOS EL STOCK FÍSICO
                    $inventory->decrement('cantidad', $cantidadSolicitada);

                    // --- NUEVO: REGISTRAR EN LA TABLA DE TRAZABILIDAD ---
                    StockMovement::create([
                        'producto' => $nombreProducto,
                        'lote' => $lote,
                        'fecha_vencimiento' => $fechaVence,
                        'tipo_movimiento' => 'Despacho',
                        'cantidad' => -$cantidadSolicitada, // En negativo porque sale de bodega
                        'fecha_hora' => now(),
                        'origen_destino' => 'Hacia: ' . $request->establecimiento_destino,
                        'documento_respaldo' => 'Guía N° ' . $request->correlativo_despacho,
                        'estado_lote' => $inventory->estado ?? 'Vigente'
                    ]);

                } else {
                    throw new \Exception("No se encontró el lote '{$lote}' del producto '{$nombreProducto}' en la bodega.");
                }
            }

            // Confirmar y guardar todos los cambios en la base de datos
            DB::commit(); 
            return redirect()->back()->with('success', 'Despacho N° ' . $dispatch->correlativo . ' generado y stock descontado con éxito.');

        } catch (\Exception $e) {
            // Anular todo si hay error
            DB::rollBack(); 
            return redirect()->back()->with('error', 'Error en el despacho: ' . $e->getMessage());
        }
    }

    public function pdf($id)
    {
        $despacho = Dispatch::with('items')->findOrFail($id);
        $pdf = Pdf::loadView('despacho.pdf', compact('despacho'));
        return $pdf->stream('Guia_Despacho_'.$despacho->correlativo.'.pdf');
    }
}
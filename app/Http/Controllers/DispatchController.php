<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\DispatchItem;
use App\Models\Inventory;
use App\Models\EstablishmentItem;
use App\Models\EstablishmentRequest;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DispatchController extends Controller
{
    // lista de farmacos que estan estrictamente prohibidos en el despacho normal
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
        $lastDispatch = Dispatch::orderBy('id', 'desc')->first();
        $nextId = $lastDispatch ? $lastDispatch->id + 1 : 1;
        $correlativo = 'GD-' . date('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $dispatches = Dispatch::with('items')->orderBy('created_at', 'desc')->get();

        // traemos el inventario disponible EXCLUYENDO (whereNotIn) los farmacos controlados
        $inventarioDisponible = Inventory::whereNotIn('producto', $this->controlados)
            ->where('cantidad', '>', 0)
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        //traemos las solicitudes por establecimientos
        $solicitudes = EstablishmentRequest::with('items.product')
            ->where('estado', 'Pendiente')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dispatch.index', compact(
            'correlativo',
            'dispatches',
            'inventarioDisponible',
            'solicitudes'
        ));

    }
    public function solicitudes()
    {
        $solicitudes = EstablishmentRequest::with('items.product')
            ->where('estado', 'Pendiente')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($solicitudes);
    }

    public function store(Request $request)
    {
        if (!$request->has('productos') || count($request->productos) === 0) {
            return redirect()->back()->with('error', 'Debe agregar al menos un producto al despacho.');
        }

        $destino = $request->establecimiento_destino;
        if ($request->clasificacion === 'Externo') {
            $destino = $request->tipo_externo_despacho . ' - ' . $request->establecimiento_destino;
        }

        DB::beginTransaction();
        try {
            $dispatch = Dispatch::create([
                'correlativo' => $request->correlativo_despacho,
                'fecha_despacho' => $request->fecha_despacho,
                'clasificacion' => $request->clasificacion,
                'estado_liberacion' => $request->estado_liberacion,
                'establecimiento_destino' => $destino,
                'direccion_destino' => $request->direccion_destino,
                'condiciones_transporte' => $request->condiciones_transporte,
                'firma_despacha' => $request->firma_despacha,
                'firma_recibe' => $request->firma_recibe,
            ]);

            foreach ($request->productos as $index => $nombreProducto) {
                // validacion de seguridad para evitar inyeccion de controlados
                if (in_array($nombreProducto, $this->controlados)) {
                    throw new \Exception("El producto '{$nombreProducto}' es controlado y no puede procesarse por esta via.");
                }

                $lote = !empty($request->lotes[$index]) ? $request->lotes[$index] : 'S/L';
                $cantidadSolicitada = (int) $request->cantidades[$index];
                $fechaVence = !empty($request->vencimientos[$index]) ? $request->vencimientos[$index] : null;

                DispatchItem::create([
                    'dispatch_id' => $dispatch->id,
                    'producto' => $nombreProducto,
                    'lote' => $lote,
                    'fecha_vencimiento' => $fechaVence,
                    'programa' => $request->programas[$index] ?? 'General',
                    'cantidad' => $cantidadSolicitada,
                ]);

                $inventory = Inventory::where('producto', $nombreProducto)
                    ->where(function ($q) use ($lote) {
                        if ($lote == 'S/L') {
                            $q->whereNull('lote')->orWhere('lote', 'S/L')->orWhere('lote', '');
                        } else {
                            $q->where('lote', $lote);
                        }
                    })->first();

                if ($inventory) {
                    if ($inventory->cantidad < $cantidadSolicitada) {
                        throw new \Exception("Stock insuficiente para '{$nombreProducto}'. Stock actual disponible: {$inventory->cantidad}");
                    }

                    $inventory->decrement('cantidad', $cantidadSolicitada);

                    StockMovement::create([
                        'producto' => $nombreProducto,
                        'lote' => $lote,
                        'fecha_vencimiento' => $fechaVence,
                        'tipo_movimiento' => 'Despacho',
                        'cantidad' => -$cantidadSolicitada,
                        'fecha_hora' => now(),
                        'origen_destino' => 'Hacia: ' . $destino,
                        'documento_respaldo' => 'Guia N° ' . $request->correlativo_despacho,
                        'estado_lote' => $inventory->estado ?? 'Vigente'
                    ]);

                } else {
                    throw new \Exception("No se encontro el lote '{$lote}' del producto '{$nombreProducto}' en la bodega.");
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Despacho N° ' . $dispatch->correlativo . ' generado y stock descontado con exito.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error en el despacho: ' . $e->getMessage());
        }
    }

    public function pdf($id)
    {
        $despacho = Dispatch::with('items')->findOrFail($id);
        $pdf = Pdf::loadView('dispatch.pdf', compact('despacho'));
        return $pdf->stream('Guia_Despacho_' . $despacho->correlativo . '.pdf');
    }
}

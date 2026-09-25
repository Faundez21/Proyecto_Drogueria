<?php

namespace App\Http\Controllers;

use App\Models\ControlledDispatch;
use App\Models\ControlledDispatchItem;
use App\Models\Inventory;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ControlledDispatchController extends Controller
{
    // lista estricta de medicamentos controlados permitidos
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
        // generar correlativo automatico para controlados (gdc)
        $lastDispatch = ControlledDispatch::orderBy('id', 'desc')->first();
        $nextId = $lastDispatch ? $lastDispatch->id + 1 : 1;
        $correlativo = 'GDC-' . date('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        // traer el historial de despachos controlados
        $dispatches = ControlledDispatch::with('items')->orderBy('created_at', 'desc')->get();

        // traer inventario disponible filtrando solo por la lista de controlados
        $inventarioDisponible = Inventory::whereIn('producto', $this->controlados)
            ->where('cantidad', '>', 0)
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        return view('despacho_controlado.index', compact('correlativo', 'dispatches', 'inventarioDisponible'));
    }

    public function store(Request $request)
    {
        if (!$request->has('productos') || count($request->productos) === 0) {
            return redirect()->back()->with('error', 'Debe agregar al menos un producto controlado al despacho.');
        }

        $destino = $request->establecimiento_destino;
        if ($request->clasificacion === 'Externo') {
            $destino = $request->tipo_externo_despacho . ' - ' . $request->establecimiento_destino;
        }

        DB::beginTransaction();
        try {
            // guardar cabecera del despacho controlado
            $dispatch = ControlledDispatch::create([
                'correlativo' => $request->correlativo_despacho,
                'fecha_despacho' => $request->fecha_despacho,
                'clasificacion' => $request->clasificacion,
                'estado_liberacion' => $request->estado_liberacion,
                'establecimiento_destino' => $destino,
                'direccion_destino' => $request->direccion_destino,
                'condiciones_transporte' => $request->condiciones_transporte,
                'series_recetas' => $request->series_recetas, // nuevo campo para talonarios
                'firma_despacha' => $request->firma_despacha,
                'firma_recibe' => $request->firma_recibe,
            ]);

            // procesar cada producto controlado
            foreach ($request->productos as $index => $nombreProducto) {
                // validacion de seguridad por si intentan inyectar un producto no permitido
                if(!in_array($nombreProducto, $this->controlados)) {
                    throw new \Exception("El producto '{$nombreProducto}' no es un farmaco controlado valido.");
                }

                $lote = !empty($request->lotes[$index]) ? $request->lotes[$index] : 'S/L';
                $cantidadSolicitada = (int) $request->cantidades[$index];
                $fechaVence = !empty($request->vencimientos[$index]) ? $request->vencimientos[$index] : null;

                ControlledDispatchItem::create([
                    'controlled_dispatch_id' => $dispatch->id,
                    'producto' => $nombreProducto,
                    'lote' => $lote,
                    'fecha_vencimiento' => $fechaVence,
                    'programa' => $request->programas[$index] ?? 'General',
                    'cantidad' => $cantidadSolicitada,
                ]);

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
                        throw new \Exception("Stock insuficiente para '{$nombreProducto}'.");
                    }
                    
                    $inventory->decrement('cantidad', $cantidadSolicitada);

                    // registrar en trazabilidad normal para mantener el kardex global sincronizado
                    StockMovement::create([
                        'producto' => $nombreProducto,
                        'lote' => $lote,
                        'fecha_vencimiento' => $fechaVence,
                        'tipo_movimiento' => 'Despacho Controlado',
                        'cantidad' => -$cantidadSolicitada,
                        'fecha_hora' => now(),
                        'origen_destino' => 'Hacia: ' . $destino,
                        'documento_respaldo' => 'Guia Controlada N° ' . $request->correlativo_despacho,
                        'estado_lote' => $inventory->estado ?? 'Vigente'
                    ]);

                } else {
                    throw new \Exception("No se encontro el lote en la bodega.");
                }
            }

            DB::commit(); 
            return redirect()->back()->with('success', 'Despacho Controlado N° ' . $dispatch->correlativo . ' generado con exito.');

        } catch (\Exception $e) {
            DB::rollBack(); 
            return redirect()->back()->with('error', 'Error en el despacho: ' . $e->getMessage());
        }
    }

    public function pdf($id)
    {
        $despacho = ControlledDispatch::with('items')->findOrFail($id);
        $pdf = Pdf::loadView('despacho_controlado.pdf', compact('despacho'));
        return $pdf->stream('Guia_Controlados_'.$despacho->correlativo.'.pdf');
    }
}
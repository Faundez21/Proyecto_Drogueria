<?php

namespace App\Http\Controllers;

use App\Models\ControlledDispatch;
use App\Models\Inventory;
use App\Services\InventoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ControlledDispatchController extends Controller
{
    public function index()
    {
        $lastDispatch = ControlledDispatch::orderBy('id', 'desc')->first();
        $nextId = $lastDispatch ? $lastDispatch->id + 1 : 1;
        $correlativo = 'GDC-' . date('Y') . '-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);

        $dispatches = ControlledDispatch::with('items')->orderBy('created_at', 'desc')->get();

        $inventarioDisponible = Inventory::whereIn('producto', InventoryService::controlados())
            ->where('estado', 'Disponible')
            ->where('cantidad', '>', 0)
            ->orderByRaw('CASE WHEN fecha_vencimiento IS NULL THEN 1 ELSE 0 END ASC, fecha_vencimiento ASC, id ASC')
            ->get();

        return view('controlled_dispatch.index', compact('correlativo', 'dispatches', 'inventarioDisponible'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_despacho' => 'required|date',
            'clasificacion' => 'required|in:Interno,Externo',
            'estado_liberacion' => 'required|string|max:255',
            'establecimiento_destino' => 'required|string|max:255',
            'tipo_externo_despacho' => 'nullable|string|max:255',
            'direccion_destino' => 'nullable|string|max:255',
            'condiciones_transporte' => 'nullable|string|max:500',
            'series_recetas' => 'nullable|string',
            'firma_recibe' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*' => 'required|string|max:255',
            'lotes' => 'nullable|array',
            'lotes.*' => 'nullable|string|max:255',
            'vencimientos' => 'nullable|array',
            'vencimientos.*' => 'nullable|date',
            'programas' => 'nullable|array',
            'programas.*' => 'nullable|string|max:255',
            'cantidades' => 'required|array|min:1',
            'cantidades.*' => 'required|integer|min:1',
        ]);

        $destino = $validated['establecimiento_destino'];
        if ($validated['clasificacion'] === 'Externo') {
            $destino = ($validated['tipo_externo_despacho'] ? $validated['tipo_externo_despacho'] . ' - ' : '') . $validated['establecimiento_destino'];
        }

        DB::beginTransaction();

        try {
            $nextId = (int) ControlledDispatch::lockForUpdate()->max('id') + 1;
            $correlativo = 'GDC-' . date('Y') . '-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);

            $dispatch = ControlledDispatch::create([
                'correlativo' => $correlativo,
                'fecha_despacho' => $validated['fecha_despacho'],
                'clasificacion' => $validated['clasificacion'],
                'estado_liberacion' => $validated['estado_liberacion'],
                'establecimiento_destino' => $destino,
                'direccion_destino' => $validated['direccion_destino'],
                'condiciones_transporte' => $validated['condiciones_transporte'],
                'series_recetas' => $validated['series_recetas'],
                'firma_recibe' => $validated['firma_recibe'],
            ]);

            foreach ($validated['productos'] as $index => $nombreProducto) {
                if (! InventoryService::esControlado($nombreProducto)) {
                    throw new \RuntimeException("El producto '{$nombreProducto}' no es un fármaco controlado válido en este sistema.");
                }

                $asignaciones = InventoryService::consumir(
                    $nombreProducto,
                    (int) $validated['cantidades'][$index],
                    $validated['lotes'][$index] ?? null
                );

                foreach ($asignaciones as $asignacion) {
                    $batch = $asignacion['inventory'];

                    ControlledDispatchItem::create([
                        'controlled_dispatch_id' => $dispatch->id,
                        'inventory_id' => $batch->id,
                        'producto' => $nombreProducto,
                        'lote' => $batch->lote ?? 'S/L',
                        'fecha_vencimiento' => $batch->fecha_vencimiento,
                        'programa' => $validated['programas'][$index] ?? 'General',
                        'cantidad' => $asignacion['cantidad'],
                    ]);

                    InventoryService::registrarMovimiento($batch, [
                        'user_id' => Auth::id(),
                        'tipo_movimiento' => 'Despacho Controlado',
                        'cantidad' => -$asignacion['cantidad'],
                        'fecha_hora' => now(),
                        'origen_destino' => 'Hacia: ' . $destino,
                        'documento_respaldo' => 'Guia Controlada N° ' . $correlativo,
                    ]);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', "Despacho Controlado N° {$correlativo} generado con FEFO y stock descontado con éxito.");
        } catch (\RuntimeException $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error en despacho controlado: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back()->with('error', 'Ocurrió un error al generar el despacho controlado. Intente nuevamente.');
        }
    }

    public function pdf($id)
    {
        $despacho = ControlledDispatch::with('items')->findOrFail($id);
        $pdf = Pdf::loadView('controlled_dispatch.pdf', compact('despacho'));

        return $pdf->stream('Guia_Controlados_' . $despacho->correlativo . '.pdf');
    }
}

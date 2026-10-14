<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\DispatchItem;
use App\Models\EstablishmentRequest;
use App\Models\Inventory;
use App\Models\Product;
use App\Services\InventoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DispatchController extends Controller
{
    public function index()
    {
        $lastDispatch = Dispatch::orderBy('id', 'desc')->first();
        $nextId = $lastDispatch ? $lastDispatch->id + 1 : 1;
        $correlativo = 'GD-' . date('Y') . '-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);

        $dispatches = Dispatch::with('items')->orderBy('created_at', 'desc')->get();

        $inventarioDisponible = Inventory::whereNotIn('producto', InventoryService::controlados())
            ->where('estado', 'Disponible')
            ->where('cantidad', '>', 0)
            ->orderByRaw('CASE WHEN fecha_vencimiento IS NULL THEN 1 ELSE 0 END ASC, fecha_vencimiento ASC, id ASC')
            ->get();

        $solicitudes = EstablishmentRequest::with('items.product')
            ->whereIn('estado', ['Pendiente', 'Parcial'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dispatch.index', compact('correlativo', 'dispatches', 'inventarioDisponible', 'solicitudes'));
    }

    public function solicitudes()
    {
        $solicitudes = EstablishmentRequest::with('items.product')
            ->whereIn('estado', ['Pendiente', 'Parcial'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($solicitudes);
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
            'firma_despacha' => 'nullable|string',
            'firma_recibe' => 'nullable|string',
            'establishment_request_id' => 'nullable|integer|exists:establishment_requests,id',
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
            $correlativo = $this->generarCorrelativo();

            $dispatch = Dispatch::create([
                'correlativo' => $correlativo,
                'fecha_despacho' => $validated['fecha_despacho'],
                'clasificacion' => $validated['clasificacion'],
                'estado_liberacion' => $validated['estado_liberacion'],
                'establecimiento_destino' => $destino,
                'direccion_destino' => $validated['direccion_destino'],
                'condiciones_transporte' => $validated['condiciones_transporte'],
                'firma_despacha' => $validated['firma_despacha'],
                'firma_recibe' => $validated['firma_recibe'],
            ]);

            foreach ($validated['productos'] as $index => $nombreProducto) {
                if (InventoryService::esControlado($nombreProducto)) {
                    throw new \RuntimeException("El producto '{$nombreProducto}' es controlado y no puede procesarse por esta vía.");
                }

                $asignaciones = InventoryService::consumir(
                    $nombreProducto,
                    (int) $validated['cantidades'][$index],
                    $validated['lotes'][$index] ?? null
                );

                foreach ($asignaciones as $asignacion) {
                    $batch = $asignacion['inventory'];

                    DispatchItem::create([
                        'dispatch_id' => $dispatch->id,
                        'inventory_id' => $batch->id,
                        'producto' => $nombreProducto,
                        'lote' => $batch->lote ?? 'S/L',
                        'fecha_vencimiento' => $batch->fecha_vencimiento,
                        'programa' => $validated['programas'][$index] ?? 'General',
                        'cantidad' => $asignacion['cantidad'],
                    ]);

                    InventoryService::registrarMovimiento($batch, [
                        'user_id' => Auth::id(),
                        'tipo_movimiento' => 'Despacho',
                        'cantidad' => -$asignacion['cantidad'],
                        'fecha_hora' => now(),
                        'origen_destino' => 'Hacia: ' . $destino,
                        'documento_respaldo' => 'Guia N° ' . $correlativo,
                    ]);
                }
            }

            if (! empty($validated['establishment_request_id'])) {
                $this->descontarDeSolicitud($validated['establishment_request_id'], $validated['productos'], $validated['cantidades']);
            }

            DB::commit();

            return redirect()->back()->with('success', "Despacho N° {$correlativo} generado con FEFO y stock descontado con éxito.");
        } catch (\RuntimeException $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error en despacho: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back()->with('error', 'Ocurrió un error al generar el despacho. Intente nuevamente.');
        }
    }

    private function generarCorrelativo(): string
    {
        $nextId = (int) Dispatch::lockForUpdate()->max('id') + 1;

        return 'GD-' . date('Y') . '-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
    }

    private function descontarDeSolicitud(int $solicitudId, array $productos, array $cantidades): void
    {
        $solicitud = EstablishmentRequest::with('items')->find($solicitudId);

        if (! $solicitud || ! in_array($solicitud->estado, ['Pendiente', 'Parcial'])) {
            return;
        }

        $entregadoPorProducto = [];
        foreach ($productos as $i => $nombre) {
            $productId = Product::where('nombre', $nombre)->value('id');
            if ($productId) {
                $entregadoPorProducto[$productId] = ($entregadoPorProducto[$productId] ?? 0) + (int) $cantidades[$i];
            }
        }

        $completa = true;

        foreach ($solicitud->items as $item) {
            $aporte = $entregadoPorProducto[$item->product_id] ?? 0;

            if ($aporte <= 0) {
                if ($item->cantidad_despachada < $item->cantidad_solicitada) {
                    $completa = false;
                }
                continue;
            }

            $nuevoDespachado = min($item->cantidad_solicitada, $item->cantidad_despachada + $aporte);
            $item->update(['cantidad_despachada' => $nuevoDespachado]);

            if ($nuevoDespachado < $item->cantidad_solicitada) {
                $completa = false;
            }
        }

        $solicitud->update(['estado' => $completa ? 'Despachado' : 'Parcial']);
    }

    public function pdf($id)
    {
        $despacho = Dispatch::with('items')->findOrFail($id);
        $pdf = Pdf::loadView('dispatch.pdf', compact('despacho'));

        return $pdf->stream('Guia_Despacho_' . $despacho->correlativo . '.pdf');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ControlledDispatch;
use App\Models\ControlledDispatchItem;
use App\Models\EstablishmentRequest;
use App\Models\Inventory;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\SolicitudService;
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

        $controlados = InventoryService::controlados();

        $inventarioDisponible = Inventory::whereIn('producto', $controlados)
            ->where('estado', 'Disponible')
            ->where('cantidad', '>', 0)
            ->orderByRaw('CASE WHEN fecha_vencimiento IS NULL THEN 1 ELSE 0 END ASC, fecha_vencimiento ASC, id ASC')
            ->get();

        $solicitudes = EstablishmentRequest::with(['items.product', 'assignedUser'])
            ->whereHas('items.product', function ($q) use ($controlados) {
                $q->whereIn('nombre', $controlados);
            })
            ->orderByRaw("CASE WHEN estado IN ('Asignada', 'Pendiente', 'Parcial') THEN 0 ELSE 1 END ASC")
            ->orderBy('created_at', 'desc')
            ->get();

        $consumoMapa = SolicitudService::mapaConsumoYStock();
        foreach ($solicitudes as $sol) {
            $est = trim((string) $sol->establecimiento);
            foreach ($sol->items as $item) {
                if ($item->consumo_anterior === null) {
                    $item->consumo_anterior = (int) ($consumoMapa[$est][$item->product_id]['consumo_anterior'] ?? 0);
                }
            }
        }

        $usuariosAsignables = User::where('status', 'activo')
            ->get()
            ->filter(fn ($u) => $u->hasRole('Administrador') || $u->can('ver.despacho_controlado'))
            ->sortBy('name')
            ->values();
        $misSolicitudesAsignadas = $solicitudes
            ->where('assigned_user_id', Auth::id())
            ->whereIn('estado', ['Pendiente', 'Asignada', 'Parcial'])
            ->values();

        $establecimientos = InventoryService::establecimientos();
        $programasLista = InventoryService::programasLista();

        return view('controlled_dispatch.index', compact(
            'correlativo',
            'dispatches',
            'inventarioDisponible',
            'solicitudes',
            'usuariosAsignables',
            'misSolicitudesAsignadas',
            'controlados',
            'establecimientos',
            'programasLista'
        ));
    }

    /**
     * Emite una guía de despacho de estupefacientes y psicotrópicos.
     *
     * Funciona igual que el despacho normal, pero con tres restricciones que
     * vienen de la normativa: solo acepta productos reconocidos como
     * controlados, la solicitud tiene que venir de un CESFAM y el destino debe
     * ser ese mismo CESFAM. Además permite anotar las series de las recetas
     * cheque que respaldan la entrega.
     *
     * Sus guías llevan correlativo propio, GDC-AAAA-XXXX, separado del de las
     * guías normales.
     */
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
            'firma_despacha' => 'nullable|string',
            'firma_recibe' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*' => 'required|string|max:1000',
            'lotes' => 'nullable|array',
            'lotes.*' => 'nullable|string|max:255',
            'vencimientos' => 'nullable|array',
            'vencimientos.*' => 'nullable|date',
            'programas' => 'nullable|array',
            'programas.*' => 'nullable|string|max:500',
            'cantidades' => 'required|array|min:1',
            'cantidades.*' => 'required|integer|min:1',
            'establishment_request_id' => 'nullable|integer|exists:establishment_requests,id',
            'action' => 'nullable|string|in:save,save_only,save_and_download',
        ]);


        // Validaciones de destino, solicitud y firmas
        $errorDespacho = null;
        $solicitudVinculada = ! empty($validated['establishment_request_id'])
            ? EstablishmentRequest::find($validated['establishment_request_id'])
            : null;
        $estRegistrado = InventoryService::establecimientoRegistrado($validated['establecimiento_destino']);

        if (! $solicitudVinculada) {
            $errorDespacho = 'Los fármacos controlados solo se pueden despachar a partir de una solicitud de un CESFAM. Selecciona la solicitud en «Ver Solicitudes».';
        } elseif (! $estRegistrado || ($estRegistrado['tipo'] ?? '') !== 'CESFAM') {
            $errorDespacho = "Los fármacos controlados solo se pueden despachar a un CESFAM. «{$validated['establecimiento_destino']}» no es un CESFAM de la red.";
        } elseif (! in_array($solicitudVinculada->estado, ['Pendiente', 'Asignada', 'Parcial'], true)) {
            $errorDespacho = "La solicitud N° {$solicitudVinculada->id} ya está en estado «{$solicitudVinculada->estado}» y no admite más despachos.";
        } elseif (mb_strtolower(trim($solicitudVinculada->establecimiento)) !== mb_strtolower(trim($validated['establecimiento_destino']))) {
            $errorDespacho = "La solicitud N° {$solicitudVinculada->id} pertenece a «{$solicitudVinculada->establecimiento}», no a «{$validated['establecimiento_destino']}».";
        } elseif ($exceso = SolicitudService::excesoSobrePendiente($solicitudVinculada, $validated['productos'], $validated['cantidades'])) {
            $errorDespacho = $exceso;
        } elseif (empty($validated['firma_despacha'])) {
            $errorDespacho = 'Falta la firma de quien despacha (bodega).';
        } elseif (empty($validated['firma_recibe']) && empty($solicitudVinculada->firma_solicitante)) {
            $errorDespacho = 'Falta la firma de quien recibe el despacho.';
        }

        if ($errorDespacho) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $errorDespacho], 422);
            }

            return redirect()->back()->withInput()->with('error', $errorDespacho);
        }

        // Los controlados siempre van a un CESFAM de la red (despacho interno)
        $validated['clasificacion'] = 'Interno';
        $destino = $estRegistrado['nombre'];

        DB::beginTransaction();

        try {
            $firmaRecibe = $validated['firma_recibe'] ?? null;

            if (! $firmaRecibe && ! empty($validated['establishment_request_id'])) {
                $firmaRecibe = EstablishmentRequest::where('id', $validated['establishment_request_id'])
                    ->value('firma_solicitante');
            }

            $nextId = (int) ControlledDispatch::lockForUpdate()->max('id') + 1;
            $correlativo = 'GDC-' . date('Y') . '-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);

            $dispatch = ControlledDispatch::create([
                'establishment_request_id' => $validated['establishment_request_id'] ?? null,
                'user_id' => Auth::id(),
                'correlativo' => $correlativo,
                'fecha_despacho' => $validated['fecha_despacho'],
                'clasificacion' => $validated['clasificacion'],
                'estado_liberacion' => $validated['estado_liberacion'],
                'establecimiento_destino' => $destino,
                'direccion_destino' => $validated['direccion_destino'] ?? null,
                'condiciones_transporte' => $validated['condiciones_transporte'] ?? null,
                'series_recetas' => $validated['series_recetas'] ?? null,
                'firma_despacha' => $validated['firma_despacha'] ?? null,
                'firma_recibe' => $firmaRecibe,
            ]);

            $itemsControlados = [];
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
                        'product_id' => $batch->product_id ?? InventoryService::productoId($nombreProducto),
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

                    $itemsControlados[] = [
                        'producto' => $nombreProducto,
                        'lote' => $batch->lote ?? 'S/L',
                        'cantidad' => (int) $asignacion['cantidad'],
                        'vencimiento' => $batch->fecha_vencimiento ? date('d/m/Y', strtotime($batch->fecha_vencimiento)) : 'N/A',
                        'programa' => $validated['programas'][$index] ?? 'General',
                    ];
                }
            }

            $normalesPendientesCount = 0;
            $solicitudOrigenId = null;

            if (! empty($validated['establishment_request_id'] ?? null)) {
                $solicitudOrigenId = (int) $validated['establishment_request_id'];
                SolicitudService::registrarDespacho(
                    $solicitudOrigenId,
                    $validated['productos'],
                    $validated['cantidades']
                );

                $reqModel = EstablishmentRequest::with('items.product')->find($solicitudOrigenId);
                if ($reqModel) {
                    $normalesPendientesCount = $reqModel->items->filter(function ($it) {
                        $nom = $it->product->nombre ?? '';
                        $falta = ($it->cantidad_solicitada - $it->cantidad_despachada);
                        return ! InventoryService::esControlado($nom) && $falta > 0;
                    })->count();
                }
            }

            DB::commit();

            $controlledDetail = [
                'correlativo' => $correlativo,
                'destino' => $destino,
                'clasificacion' => $validated['clasificacion'],
                'fecha' => !empty($validated['fecha_despacho']) ? date('d/m/Y', strtotime($validated['fecha_despacho'])) : date('d/m/Y'),
                'series_recetas' => $validated['series_recetas'] ?? null,
                'total_items' => count($itemsControlados),
                'total_unidades' => array_sum(array_column($itemsControlados, 'cantidad')),
                'items' => $itemsControlados,
                'pdf_url' => route('controlled_dispatch.pdf', $dispatch->id),
                'auto_download' => ($validated['action'] ?? '') === 'save_and_download',
                'tiene_normales_pendientes' => $normalesPendientesCount > 0,
                'cant_normales_pendientes' => $normalesPendientesCount,
                'solicitud_id' => $solicitudOrigenId,
                'url_despacho_normal' => $solicitudOrigenId ? route('dispatch.index', ['solicitud_id' => $solicitudOrigenId]) : null,
            ];

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Despacho Controlado N° {$correlativo} generado con FEFO y stock descontado con éxito.",
                    'detail' => $controlledDetail,
                ]);
            }

            return redirect()->back()
                ->with('success', "Despacho Controlado N° {$correlativo} generado con FEFO y stock descontado con éxito.")
                ->with('controlled_dispatch_success_detail', $controlledDetail);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Por favor verifique los datos ingresados en el formulario.',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (\RuntimeException $e) {
            DB::rollBack();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error en despacho controlado: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al generar el despacho controlado. Intente nuevamente.',
                ], 500);
            }

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

<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\DispatchItem;
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

class DispatchController extends Controller
{
    public function index()
    {
        $lastDispatch = Dispatch::orderBy('id', 'desc')->first();
        $nextId = $lastDispatch ? $lastDispatch->id + 1 : 1;
        $correlativo = 'GD-' . date('Y') . '-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);

        $dispatches = Dispatch::with('items')->orderBy('created_at', 'desc')->get();

        $controlados = InventoryService::controlados();

        $inventarioDisponible = Inventory::whereNotIn('producto', $controlados)
            ->where('estado', 'Disponible')
            ->where('cantidad', '>', 0)
            ->orderByRaw('CASE WHEN fecha_vencimiento IS NULL THEN 1 ELSE 0 END ASC, fecha_vencimiento ASC, id ASC')
            ->get();

        $solicitudes = EstablishmentRequest::with(['items.product', 'assignedUser'])
            ->whereHas('items.product', function ($q) use ($controlados) {
                $q->whereNotIn('nombre', $controlados);
            })
            ->orderByRaw("CASE WHEN estado IN ('Asignada', 'Pendiente', 'Parcial') THEN 0 ELSE 1 END ASC")
            ->orderBy('created_at', 'desc')
            ->get();

        // Asegurar que cada item tenga su consumo_anterior calculado incluso si fue creado antes
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
            ->filter(fn ($u) => $u->hasRole('Administrador') || $u->can('ver.despacho'))
            ->sortBy('name')
            ->values();
        $misSolicitudesAsignadas = $solicitudes
            ->where('assigned_user_id', Auth::id())
            ->whereIn('estado', ['Pendiente', 'Asignada', 'Parcial'])
            ->values();

        $establecimientos = InventoryService::establecimientos();
        $programasLista = InventoryService::programasLista();
        $consumoDeclarado = SolicitudService::consumoDeclaradoPorNombre();

        return view('dispatch.index', compact(
            'consumoDeclarado',
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

    public function solicitudes()
    {
        $solicitudes = EstablishmentRequest::with(['items.product', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($solicitudes);
    }

    /**
     * Emite una guía de despacho y descuenta el stock.
     *
     * Antes de tocar el inventario se revisa que el destino sea un
     * establecimiento de la red, que la solicitud vinculada corresponda a ese
     * mismo destino, que no se despache más de lo que queda pendiente en ella
     * y que estén las dos firmas.
     *
     * El descuento lo hace InventoryService::consumir dentro de una
     * transacción: si un producto falla, no se guarda nada. Cuando no se
     * indica un lote, se despacha primero el que vence antes (FEFO) y, si no
     * alcanza, se reparte entre varios lotes siguiendo ese mismo orden.
     *
     * Al final se genera la guía con correlativo anual GD-AAAA-XXXX, se
     * registra el movimiento en el Kardex y se recalcula el estado de la
     * solicitud de origen.
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
            'firma_despacha' => 'nullable|string',
            'firma_recibe' => 'nullable|string',
            'establishment_request_id' => 'nullable|integer|exists:establishment_requests,id',
            'motivo_traspaso' => 'nullable|string|max:500',
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
            'action' => 'nullable|string|in:save,save_only,save_and_download',
        ]);


        // Validaciones de destino, solicitud y firmas
        $errorDespacho = null;
        $solicitudVinculada = ! empty($validated['establishment_request_id'])
            ? EstablishmentRequest::find($validated['establishment_request_id'])
            : null;
        $estRegistrado = InventoryService::establecimientoRegistrado($validated['establecimiento_destino']);

        // traspaso sin solicitud: despacho externo a un establecimiento de la red
        // Exige un motivo y permite emitir la guía sin la firma de quien recibe;
        // en ese caso queda "Pendiente de recepción" hasta que se firme desde la bandeja.
        $esTraspasoInterno = $validated['clasificacion'] === 'Externo' && ! $solicitudVinculada;
        $motivoTraspaso = trim((string) ($validated['motivo_traspaso'] ?? ''));

        if ($validated['clasificacion'] === 'Externo' && ! $estRegistrado) {
            $errorDespacho = "El destino «{$validated['establecimiento_destino']}» no es un establecimiento de la red. Selecciónelo de la lista.";
        } elseif ($solicitudVinculada) {
            if (! in_array($solicitudVinculada->estado, ['Pendiente', 'Asignada', 'Parcial'], true)) {
                $errorDespacho = "La solicitud N° {$solicitudVinculada->id} ya está en estado «{$solicitudVinculada->estado}» y no admite más despachos.";
            } elseif (mb_strtolower(trim($solicitudVinculada->establecimiento)) !== mb_strtolower(trim($validated['establecimiento_destino']))) {
                $errorDespacho = "La solicitud N° {$solicitudVinculada->id} pertenece a «{$solicitudVinculada->establecimiento}», no a «{$validated['establecimiento_destino']}».";
            } else {
                $errorDespacho = SolicitudService::excesoSobrePendiente($solicitudVinculada, $validated['productos'], $validated['cantidades']);
            }
        }

        if (! $errorDespacho && $esTraspasoInterno && $motivoTraspaso === '') {
            $errorDespacho = 'Indique el motivo del traspaso sin solicitud.';
        }
        if (! $errorDespacho && empty($validated['firma_despacha'])) {
            $errorDespacho = 'Falta la firma de quien despacha (bodega).';
        }
        if (! $errorDespacho && ! $esTraspasoInterno && empty($validated['firma_recibe']) && empty($solicitudVinculada?->firma_solicitante)) {
            $errorDespacho = 'Falta la firma de quien recibe el despacho.';
        }

        if ($errorDespacho) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $errorDespacho], 422);
            }

            return redirect()->back()->withInput()->with('error', $errorDespacho);
        }

        $destino = $estRegistrado['nombre'] ?? $validated['establecimiento_destino'];
        if ($validated['clasificacion'] === 'Interno') {
            $destino = (! empty($validated['tipo_externo_despacho']) ? $validated['tipo_externo_despacho'] . ' - ' : '') . $validated['establecimiento_destino'];
        }

        DB::beginTransaction();

        try {
            $firmaRecibe = $validated['firma_recibe'] ?? null;

            if (! $firmaRecibe && ! empty($validated['establishment_request_id'])) {
                $firmaRecibe = EstablishmentRequest::where('id', $validated['establishment_request_id'])
                    ->value('firma_solicitante');
            }

            $correlativo = $this->generarCorrelativo();

            $dispatch = Dispatch::create([
                'establishment_request_id' => $validated['establishment_request_id'] ?? null,
                'user_id' => Auth::id(),
                'correlativo' => $correlativo,
                'fecha_despacho' => $validated['fecha_despacho'],
                'clasificacion' => $validated['clasificacion'],
                'estado_liberacion' => $validated['estado_liberacion'],
                'establecimiento_destino' => $destino,
                'direccion_destino' => $validated['direccion_destino'] ?? null,
                'condiciones_transporte' => $validated['condiciones_transporte'] ?? null,
                'firma_despacha' => $validated['firma_despacha'] ?? null,
                'firma_recibe' => $firmaRecibe,
                'motivo_traspaso' => $esTraspasoInterno ? $motivoTraspaso : null,
                'estado_recepcion' => $firmaRecibe ? 'Recibido' : 'Pendiente',
                'fecha_recepcion' => $firmaRecibe ? now() : null,
            ]);

            $itemsDespachados = [];
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
                        'product_id' => $batch->product_id ?? InventoryService::productoId($nombreProducto),
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
                        'documento_respaldo' => 'Guía N° ' . $correlativo,
                    ]);

                    $itemsDespachados[] = [
                        'producto' => $nombreProducto,
                        'lote' => $batch->lote ?? 'S/L',
                        'cantidad' => (int) $asignacion['cantidad'],
                        'vencimiento' => $batch->fecha_vencimiento ? date('d/m/Y', strtotime($batch->fecha_vencimiento)) : 'N/A',
                        'programa' => $validated['programas'][$index] ?? 'General',
                    ];
                }
            }

            $controladosPendientesCount = 0;
            $solicitudOrigenId = null;

            if (! empty($validated['establishment_request_id'] ?? null)) {
                $solicitudOrigenId = (int) $validated['establishment_request_id'];
                SolicitudService::registrarDespacho(
                    $solicitudOrigenId,
                    $validated['productos'],
                    $validated['cantidades']
                );

                // Comprobar si la solicitud aún tiene fármacos controlados con cantidad pendiente
                $reqModel = EstablishmentRequest::with('items.product')->find($solicitudOrigenId);
                if ($reqModel) {
                    $controladosPendientesCount = $reqModel->items->filter(function ($it) {
                        $nom = $it->product->nombre ?? '';
                        $falta = ($it->cantidad_solicitada - $it->cantidad_despachada);
                        return InventoryService::esControlado($nom) && $falta > 0;
                    })->count();
                }
            }

            DB::commit();

            $dispatchDetail = [
                'correlativo' => $correlativo,
                'destino' => $destino,
                'clasificacion' => $validated['clasificacion'],
                'fecha' => !empty($validated['fecha_despacho']) ? date('d/m/Y', strtotime($validated['fecha_despacho'])) : date('d/m/Y'),
                'total_items' => count($itemsDespachados),
                'total_unidades' => array_sum(array_column($itemsDespachados, 'cantidad')),
                'items' => $itemsDespachados,
                'pdf_url' => route('dispatch.pdf', $dispatch->id),
                'auto_download' => ($validated['action'] ?? '') === 'save_and_download',
                'tiene_controlados_pendientes' => $controladosPendientesCount > 0,
                'cant_controlados_pendientes' => $controladosPendientesCount,
                'solicitud_id' => $solicitudOrigenId,
                'es_traspaso' => $esTraspasoInterno,
                'pendiente_recepcion' => ! $firmaRecibe,
                'url_despacho_controlado' => $solicitudOrigenId ? route('controlled_dispatch.index', ['solicitud_id' => $solicitudOrigenId]) : null,
            ];

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Despacho N° {$correlativo} generado con FEFO y stock descontado con éxito.",
                    'detail' => $dispatchDetail,
                ]);
            }

            return redirect()->back()
                ->with('success', "Despacho N° {$correlativo} generado con FEFO y stock descontado con éxito.")
                ->with('dispatch_success_detail', $dispatchDetail);
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
            Log::error('Error en despacho: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al generar el despacho. Intente nuevamente.',
                ], 500);
            }

            return redirect()->back()->with('error', 'Ocurrió un error al generar el despacho. Intente nuevamente.');
        }
    }

    private function generarCorrelativo(): string
    {
        $nextId = (int) Dispatch::lockForUpdate()->max('id') + 1;

        return 'GD-' . date('Y') . '-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
    }

    public function pdf($id)
    {
        $despacho = Dispatch::with('items')->findOrFail($id);
        $pdf = Pdf::loadView('dispatch.pdf', compact('despacho'));

        return $pdf->stream('Guia_Despacho_' . $despacho->correlativo . '.pdf');
    }

    /**
     * Registra la firma de quien recibe una guía que quedó "Pendiente de
     * recepción" (traspaso interno emitido sin la firma del receptor).
     */
    public function registrarRecepcion(Request $request, string $id)
    {
        $validated = $request->validate([
            'firma_recibe' => 'required|string',
        ], [
            'firma_recibe.required' => 'Falta la firma de quien recibe.',
        ]);

        $dispatch = Dispatch::findOrFail($id);

        if ($dispatch->estado_recepcion !== 'Pendiente') {
            return response()->json([
                'success' => false,
                'message' => "La guía {$dispatch->correlativo} ya tiene registrada su recepción.",
            ], 422);
        }

        $dispatch->update([
            'firma_recibe' => $validated['firma_recibe'],
            'estado_recepcion' => 'Recibido',
            'fecha_recepcion' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Recepción de la guía {$dispatch->correlativo} registrada.",
        ]);
    }
}

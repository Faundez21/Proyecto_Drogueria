<?php

namespace App\Http\Controllers;

use App\Models\EstablishmentItem;
use App\Models\EstablishmentRequest;
use App\Models\Product;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\EstablishmentStockService;
use App\Services\SolicitudService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EstablishmentRequestController extends Controller
{
    public function index()
    {
        InventoryService::sincronizarProductosDesdeInventario();
        $controlados = InventoryService::controlados();
        $metadatos = InventoryService::mapaMetadatosProductos();

        // solo medicamentos e insumos (sin estupefacientes)
        $products = Product::whereNotIn('nombre', $controlados)
            ->orderBy('nombre')
            ->get()
            ->filter(function ($prod) use ($metadatos) {
                $key = mb_strtolower(trim($prod->nombre));
                $cat = $metadatos['categoriasPorId'][(int) $prod->id] ?? ($metadatos['categoriasPorNombre'][$key] ?? null);

                return InventoryService::clasificarGrupoProducto($prod->nombre, $cat) !== 'Estupefacientes';
            })
            ->values();

        foreach ($products as $prod) {
            $key = mb_strtolower(trim($prod->nombre));
            $cat = $metadatos['categoriasPorId'][(int) $prod->id] ?? ($metadatos['categoriasPorNombre'][$key] ?? null);
            $prog = $metadatos['programasPorId'][(int) $prod->id] ?? ($metadatos['programasPorNombre'][$key] ?? '');
            $prod->categoria_sugerida = $cat ?: InventoryService::clasificarGrupoProducto($prod->nombre, $cat);
            $prod->programa_sugerido = $prog;
        }

        // solo establecimientos que no son cesfam
        $establecimientos = array_values(array_filter(
            InventoryService::establecimientos(),
            fn (array $est) => ($est['tipo'] ?? '') !== 'CESFAM'
        ));

        $nombresEstablecimientos = array_column($establecimientos, 'nombre');
        $nombresHistoricos = [
            'CECOSF Centinela',
            'CECOSF CENTINELA',
            'CECOSF Villa Centinela',
            'CECOSF VILLA CENTINELA',
            'CECOSF Los Lobos - La Gloria',
            'CECOSF LOS LOBOS - LA GLORIA',
            'CECOSF Los Lobos-La Gloria',
            'CECOSF LOS LOBOS-LA GLORIA',
            'Posta Tumbe',
            'POSTA TUMBE',
            'Posta Rural Tumbes',
            'POSTA RURAL TUMBES',
            'CECOSF Libertad Gaete',
            'CECOSF LIBERTAD GAETE',
            'CECOSF Libertad-Gaete',
            'CECOSF LIBERTAD-GAETE',
        ];
        $nombresEstablecimientosQuery = array_values(array_unique(array_merge($nombresEstablecimientos, $nombresHistoricos)));

        // inventario propio de los establecimientos registrado en las solicitudes
        $inventarioSolicitudes = EstablishmentItem::with(['request', 'product'])
            ->whereHas('request', fn ($q) => $q->whereIn('establecimiento', $nombresEstablecimientosQuery))
            ->whereHas('product', fn ($q) => $q->whereNotIn('nombre', $controlados))
            ->orderByDesc('id')
            ->get();

        // stock actual real de cada establecimiento
        $saldosEstablecimientos = EstablishmentStockService::saldosPorProducto();

        $productosLista = $products->pluck('nombre')
            ->merge($inventarioSolicitudes->map(fn ($item) => $item->product->nombre ?? null))
            ->filter()
            ->unique()
            ->sort(SORT_NATURAL)
            ->values();

        $programasLista = InventoryService::programasLista($inventarioSolicitudes->pluck('programa'));
        $categoriasLista = InventoryService::categoriasLista(false);
        $productosPorPrograma = InventoryService::productosPorPrograma();

        $consumoMapa = SolicitudService::mapaConsumoYStock($nombresEstablecimientosQuery);

        // solicitudes pendientes exclusivas para establecimientos (no-cesfam, medicamentos e insumos no controlados)
        $solicitudesPendientesRaw = EstablishmentRequest::with(['items.product', 'assignedUser'])
            ->whereIn('establecimiento', $nombresEstablecimientosQuery)
            ->whereIn('estado', ['Pendiente', 'Asignada', 'Parcial'])
            ->orderBy('created_at', 'desc')
            ->get();

        $solicitudesPendientesEstablecimientos = collect();
        $itemsPendientesEstablecimientos = collect();

        foreach ($solicitudesPendientesRaw as $sol) {
            $itemsValidos = collect();
            foreach ($sol->items as $it) {
                $nombreProd = $it->product->nombre ?? '';
                if (in_array($nombreProd, $controlados)) {
                    continue;
                }
                $pendiente = max(0, $it->cantidad_solicitada - $it->cantidad_despachada);
                if ($pendiente <= 0) {
                    continue;
                }
                $it->setAttribute('cantidad_pendiente', $pendiente);
                $it->setAttribute('solicitud', $sol);
                $itemsValidos->push($it);
                $itemsPendientesEstablecimientos->push($it);
            }
            if ($itemsValidos->isNotEmpty()) {
                $sol->setRelation('itemsPendientes', $itemsValidos);
                $sol->setAttribute('total_unidades_pendientes', (int) $itemsValidos->sum('cantidad_pendiente'));
                $solicitudesPendientesEstablecimientos->push($sol);
            }
        }

        $centrosPendientesEstablecimientos = collect(array_column($establecimientos, 'nombre'))
            ->merge($solicitudesPendientesEstablecimientos->pluck('establecimiento'))
            ->filter()
            ->unique()
            ->sort(SORT_NATURAL)
            ->values();
        $programasPendientesEstablecimientos = $itemsPendientesEstablecimientos->pluck('programa')->filter()->unique()->sort()->values();
        $productosPendientesEstablecimientos = $itemsPendientesEstablecimientos->map(fn ($it) => $it->product->nombre ?? null)->filter()->unique()->sort(SORT_NATURAL)->values();
        $pendientesCount = $solicitudesPendientesEstablecimientos->count();

        return view('requests.index', compact(
            'products',
            'controlados',
            'establecimientos',
            'inventarioSolicitudes',
            'saldosEstablecimientos',
            'productosLista',
            'programasLista',
            'categoriasLista',
            'productosPorPrograma',
            'consumoMapa',
            'solicitudesPendientesEstablecimientos',
            'centrosPendientesEstablecimientos',
            'itemsPendientesEstablecimientos',
            'programasPendientesEstablecimientos',
            'productosPendientesEstablecimientos',
            'pendientesCount'
        ));
    }

    public function cesfam()
    {
        InventoryService::sincronizarProductosDesdeInventario();
        $controlados = InventoryService::controlados();
        $metadatos = InventoryService::mapaMetadatosProductos();

        // medicamentos, insumos y estupefacientes
        $products = Product::orderBy('nombre')->get();

        foreach ($products as $prod) {
            $key = mb_strtolower(trim($prod->nombre));
            $cat = $metadatos['categoriasPorId'][(int) $prod->id] ?? ($metadatos['categoriasPorNombre'][$key] ?? null);
            $prog = $metadatos['programasPorId'][(int) $prod->id] ?? ($metadatos['programasPorNombre'][$key] ?? '');
            $prod->categoria_sugerida = $cat ?: InventoryService::clasificarGrupoProducto($prod->nombre, $cat);
            $prod->programa_sugerido = $prog;
        }

        // solo establecimientos tipo cesfam
        $establecimientos = array_values(array_filter(
            InventoryService::establecimientos(),
            fn (array $est) => ($est['tipo'] ?? '') === 'CESFAM'
        ));

        $nombresCesfam = array_column($establecimientos, 'nombre');
        $nombresCesfamHistoricos = [
            'CESFAM Los Cerros',
            'CESFAM LOS CERROS',
            'CESFAM Paulina Avendaño',
            'CESFAM PAULINA AVENDAÑO',
            'CESFAM Paulina Avendaño Pereda',
            'CESFAM PAULINA AVENDAÑO PEREDA',
            'CESFAM San Vicente',
            'CESFAM SAN VICENTE',
            'CESFAM Leocan Portus',
            'CESFAM LEOCAN PORTUS',
            'CESFAM Alcalde Leocán Portus Govinden',
            'CESFAM ALCALDE LEOCÁN PORTUS GOVINDEN',
        ];
        $nombresCesfamQuery = array_values(array_unique(array_merge($nombresCesfam, $nombresCesfamHistoricos)));

        // inventario propio de los cesfam registrado en las solicitudes
        $inventarioSolicitudes = EstablishmentItem::with(['request', 'product'])
            ->whereHas('request', fn ($q) => $q->whereIn('establecimiento', $nombresCesfamQuery))
            ->orderByDesc('id')
            ->get();

        // stock actual real de cada establecimiento
        $saldosEstablecimientos = EstablishmentStockService::saldosPorProducto();

        $productosLista = $products->pluck('nombre')
            ->merge($inventarioSolicitudes->map(fn ($item) => $item->product->nombre ?? null))
            ->filter()
            ->unique()
            ->sort(SORT_NATURAL)
            ->values();

        $programasLista = InventoryService::programasLista($inventarioSolicitudes->pluck('programa'));
        $categoriasLista = InventoryService::categoriasLista(true);
        $productosPorPrograma = InventoryService::productosPorPrograma();

        $consumoMapa = SolicitudService::mapaConsumoYStock($nombresCesfamQuery);

        // solicitudes pendientes exclusivas para cesfam (medicamentos, insumos y farmacos controlados)
        $solicitudesPendientesRaw = EstablishmentRequest::with(['items.product', 'assignedUser'])
            ->whereIn('establecimiento', $nombresCesfamQuery)
            ->whereIn('estado', ['Pendiente', 'Asignada', 'Parcial'])
            ->orderBy('created_at', 'desc')
            ->get();

        $solicitudesPendientesCesfam = collect();
        $itemsPendientesCesfam = collect();

        foreach ($solicitudesPendientesRaw as $sol) {
            $itemsValidos = collect();
            foreach ($sol->items as $it) {
                $pendiente = max(0, $it->cantidad_solicitada - $it->cantidad_despachada);
                if ($pendiente <= 0) {
                    continue;
                }
                $it->setAttribute('cantidad_pendiente', $pendiente);
                $it->setAttribute('solicitud', $sol);
                $itemsValidos->push($it);
                $itemsPendientesCesfam->push($it);
            }
            if ($itemsValidos->isNotEmpty()) {
                $sol->setRelation('itemsPendientes', $itemsValidos);
                $sol->setAttribute('total_unidades_pendientes', (int) $itemsValidos->sum('cantidad_pendiente'));
                $solicitudesPendientesCesfam->push($sol);
            }
        }

        $centrosPendientesCesfam = collect(array_column($establecimientos, 'nombre'))
            ->merge($solicitudesPendientesCesfam->pluck('establecimiento'))
            ->filter()
            ->unique()
            ->sort(SORT_NATURAL)
            ->values();
        $programasPendientesCesfam = $itemsPendientesCesfam->pluck('programa')->filter()->unique()->sort()->values();
        $productosPendientesCesfam = $itemsPendientesCesfam->map(fn ($it) => $it->product->nombre ?? null)->filter()->unique()->sort(SORT_NATURAL)->values();
        $pendientesCount = $solicitudesPendientesCesfam->count();

        return view('requests.cesfam', compact(
            'products',
            'controlados',
            'establecimientos',
            'inventarioSolicitudes',
            'saldosEstablecimientos',
            'productosLista',
            'programasLista',
            'categoriasLista',
            'productosPorPrograma',
            'consumoMapa',
            'solicitudesPendientesCesfam',
            'centrosPendientesCesfam',
            'itemsPendientesCesfam',
            'programasPendientesCesfam',
            'productosPendientesCesfam',
            'pendientesCount'
        ));
    }

    public function store(Request $request)
    {
        $nombresEstablecimientos = array_column(InventoryService::establecimientos(), 'nombre');

        $validated = $request->validate([
            'tipo_vista' => 'nullable|string|in:establecimientos,cesfam',
            'establecimiento' => ['required', 'string', Rule::in($nombresEstablecimientos)],
            'fecha_solicitud' => 'required|date',
            'responsable' => 'required|string',
            'cargo' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'firma_recibe' => 'required|string',

            'producto' => 'required|array',
            'producto.*' => 'required|integer|exists:products,id',

            'programa' => 'nullable|array',
            'programa.*' => 'nullable|string',

            'consumo_anterior' => 'required|array',
            'consumo_anterior.*' => 'required|integer|min:0',

            'stock_actual' => 'required|array',
            'stock_actual.*' => 'required|integer|min:0',

            'cantidad_solicitada' => 'required|array',
            'cantidad_solicitada.*' => 'required|integer|min:1',
        ], [
            'firma_recibe.required' => 'Debe firmar la solicitud antes de enviarla.',
            'consumo_anterior.*.required' => 'Indique el consumo mensual de cada producto.',
        ]);

        $esCesfam = InventoryService::esCesfam($validated['establecimiento']);

        $rechazar = function (string $mensaje, int $codigo = 422) use ($request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'message' => $mensaje], $codigo);
            }

            return back()->with('error', $mensaje)->withInput();
        };

        // el permiso se revisa segun el tipo real del establecimiento, no segun la pantalla de origen
        $permisoNecesario = $esCesfam ? 'ver.solicitudes_cesfam' : 'ver.solicitudes';
        if (! $request->user()?->can($permisoNecesario)) {
            return $rechazar('No tiene permiso para registrar solicitudes de este establecimiento.', 403);
        }

        // cada fila debe traer producto, consumo, stock y cantidad
        $filas = count($validated['producto']);
        foreach (['consumo_anterior', 'stock_actual', 'cantidad_solicitada'] as $lista) {
            if (count($validated[$lista] ?? []) !== $filas) {
                return $rechazar('Faltan datos en alguno de los productos. Revise cada fila.');
            }
        }

        // solo los cesfam pueden solicitar estupefacientes
        foreach ($validated['producto'] as $productId) {
            $nombre = Product::find($productId)->nombre ?? '';

            if (InventoryService::esControlado($nombre) && (! $esCesfam || ($validated['tipo_vista'] ?? '') === 'establecimientos')) {
                return $rechazar("El establecimiento '{$validated['establecimiento']}' solo puede solicitar medicamentos e insumos no estupefacientes.");
            }
        }

        $establishmentRequest = EstablishmentRequest::create([
            'establecimiento' => $validated['establecimiento'],
            'fecha_solicitud' => $validated['fecha_solicitud'],
            'numero_solicitud' => '#00000',
            'responsable' => $validated['responsable'],
            'cargo' => $validated['cargo'] ?? null,
            'observaciones' => $validated['observaciones'] ?? null,
            'firma_solicitante' => $validated['firma_recibe'] ?? null,
            'estado' => 'Pendiente',
        ]);

        $establishmentRequest->update([
            'numero_solicitud' => '#' . str_pad($establishmentRequest->id, 5, '0', STR_PAD_LEFT),
        ]);

        $itemsResumen = [];
        foreach ($validated['producto'] as $index => $productId) {
            // consumo mensual informado por el establecimiento
            $consumoVal = (int) $validated['consumo_anterior'][$index];

            $product = Product::find($productId);
            $nombreProd = $product ? $product->nombre : "Producto #{$productId}";

            $establishmentRequest->items()->create([
                'product_id' => $productId,
                'programa' => $validated['programa'][$index] ?? null,
                'consumo_anterior' => (int) $consumoVal,
                'stock_actual' => (int) $validated['stock_actual'][$index],
                'cantidad_solicitada' => (int) $validated['cantidad_solicitada'][$index],
                'cantidad_despachada' => 0,
            ]);

            // el stock declarado pasa a ser el saldo real del establecimiento
            EstablishmentStockService::declarar(
                $validated['establecimiento'],
                (int) $productId,
                $nombreProd,
                (int) $validated['stock_actual'][$index],
                $establishmentRequest->id
            );

            $itemsResumen[] = [
                'producto' => $nombreProd,
                'programa' => !empty($validated['programa'][$index]) ? $validated['programa'][$index] : 'General',
                'consumo_anterior' => (int) $consumoVal,
                'stock_actual' => (int) $validated['stock_actual'][$index],
                'cantidad_solicitada' => (int) $validated['cantidad_solicitada'][$index],
            ];
        }

        $rutaRetorno = ($validated['tipo_vista'] ?? '') === 'cesfam' || $esCesfam
            ? 'requests.cesfam'
            : 'requests.index';

        $solicitudDetail = [
            'numero_solicitud' => $establishmentRequest->numero_solicitud,
            'establecimiento' => $validated['establecimiento'],
            'tipo' => $esCesfam ? 'CESFAM' : 'Establecimiento de Salud',
            'fecha_solicitud' => date('d/m/Y', strtotime($validated['fecha_solicitud'])),
            'responsable' => $validated['responsable'],
            'cargo' => $validated['cargo'] ?? 'N/A',
            'observaciones' => $validated['observaciones'] ?? '',
            'total_items' => count($itemsResumen),
            'total_unidades' => array_sum(array_column($itemsResumen, 'cantidad_solicitada')),
            'items' => $itemsResumen,
        ];

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => "Solicitud {$establishmentRequest->numero_solicitud} registrada exitosamente para {$validated['establecimiento']}.",
                'detail' => $solicitudDetail,
            ]);
        }

        return redirect()
            ->route($rutaRetorno)
            ->with('success', "Solicitud {$establishmentRequest->numero_solicitud} registrada exitosamente para {$validated['establecimiento']}.")
            ->with('solicitud_success_detail', $solicitudDetail);
    }

    // permite al administrador asignar o desasignar una solicitud a un trabajador desde despacho, recalculando automaticamente el estado de la solicitud
    public function assign(Request $request, EstablishmentRequest $establishmentRequest)
    {
        $validated = $request->validate([
            'assigned_user_id' => 'nullable|integer|exists:users,id',
        ]);

        $userId = $validated['assigned_user_id'] ?? null;

        if ($userId) {
            $candidateUser = User::find($userId);
            if (!$candidateUser || (!$candidateUser->hasRole('Administrador') && !$candidateUser->can('ver.despacho') && !$candidateUser->can('ver.despacho_controlado'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario seleccionado no cuenta con permisos para operar en los módulos de Despacho.',
                ], 422);
            }
        }

        $establishmentRequest->update([
            'assigned_user_id' => $userId,
            'assigned_at' => $userId ? now() : null,
        ]);

        $nuevoEstado = SolicitudService::actualizarEstado($establishmentRequest->id);
        $establishmentRequest->load('assignedUser');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'id' => $establishmentRequest->id,
                'numero_solicitud' => $establishmentRequest->numero_solicitud,
                'estado' => $nuevoEstado,
                'assigned_user_id' => $establishmentRequest->assigned_user_id,
                'assigned_user_name' => $establishmentRequest->assignedUser
                    ? trim($establishmentRequest->assignedUser->name . ' ' . ($establishmentRequest->assignedUser->last_name ?? ''))
                    : null,
                'assigned_at' => $establishmentRequest->assigned_at?->format('d/m/Y H:i'),
                'message' => $userId
                    ? "Solicitud {$establishmentRequest->numero_solicitud} asignada a " . trim($establishmentRequest->assignedUser->name . ' ' . ($establishmentRequest->assignedUser->last_name ?? '')) . '.'
                    : "Solicitud {$establishmentRequest->numero_solicitud} quedó sin asignar.",
            ]);
        }

        return back()->with(
            'success',
            $userId
                ? "Solicitud {$establishmentRequest->numero_solicitud} asignada correctamente."
                : "Asignación removida de la solicitud {$establishmentRequest->numero_solicitud}."
        );
    }

    // genera el informe pdf de solicitudes pendientes exclusivo para establecimientos de salud (solo administrador)
    public function pendingEstablecimientosPdf(Request $request)
    {
        if (! auth()->user() || ! auth()->user()->hasRole('Administrador')) {
            abort(403, 'Acceso restringido para Administradores.');
        }

        $controlados = InventoryService::controlados();
        $establecimientos = array_values(array_filter(
            InventoryService::establecimientos(),
            fn (array $est) => ($est['tipo'] ?? '') !== 'CESFAM'
        ));
        $nombresEstablecimientos = array_column($establecimientos, 'nombre');
        $nombresHistoricos = [
            'CECOSF Centinela', 'CECOSF CENTINELA', 'CECOSF Villa Centinela', 'CECOSF VILLA CENTINELA',
            'CECOSF Los Lobos - La Gloria', 'CECOSF LOS LOBOS - LA GLORIA', 'CECOSF Los Lobos-La Gloria', 'CECOSF LOS LOBOS-LA GLORIA',
            'Posta Tumbe', 'POSTA TUMBE', 'Posta Rural Tumbes', 'POSTA RURAL TUMBES',
            'CECOSF Libertad Gaete', 'CECOSF LIBERTAD GAETE', 'CECOSF Libertad-Gaete', 'CECOSF LIBERTAD-GAETE',
        ];
        $nombresQuery = array_values(array_unique(array_merge($nombresEstablecimientos, $nombresHistoricos)));

        $solicitudId = $request->input('solicitud_id') ?: $request->input('request_id');

        // descarga de una sola solicitud
        if ($solicitudId) {
            $solicitud = EstablishmentRequest::with(['items.product', 'assignedUser'])
                ->whereIn('establecimiento', $nombresQuery)
                ->findOrFail($solicitudId);

            foreach ($solicitud->items as $it) {
                $it->setAttribute('cantidad_pendiente', max(0, $it->cantidad_solicitada - $it->cantidad_despachada));
            }

            $pdf = Pdf::loadView('requests.pdf.single_solicitud', [
                'solicitud' => $solicitud,
                'items' => $solicitud->items,
                'esCesfam' => false,
            ]);

            $correlativoSeguro = preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) ($solicitud->numero_solicitud ?: $solicitud->id));
            $nombreArchivo = 'Solicitud_' . $correlativoSeguro . '_' . date('Ymd_His') . '.pdf';

            if ($request->boolean('download')) {
                return $pdf->download($nombreArchivo);
            }

            return $pdf->stream($nombreArchivo);
        }

        // listado consolidado
        $centroFiltro = trim((string) ($request->input('centro') ?: $request->input('establecimiento')));
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $programaFiltro = trim((string) $request->input('programa'));
        $productoFiltro = trim((string) $request->input('producto'));

        $solicitudes = EstablishmentRequest::with(['items.product', 'assignedUser'])
            ->whereIn('establecimiento', $nombresQuery)
            ->whereIn('estado', ['Pendiente', 'Asignada', 'Parcial'])
            ->when($centroFiltro !== '', fn ($q) => $q->where('establecimiento', $centroFiltro))
            ->when($fechaDesde, fn ($q) => $q->whereDate('fecha_solicitud', '>=', $fechaDesde))
            ->when($fechaHasta, fn ($q) => $q->whereDate('fecha_solicitud', '<=', $fechaHasta))
            ->orderBy('created_at', 'desc')
            ->get();

        $itemsPendientes = collect();
        foreach ($solicitudes as $sol) {
            foreach ($sol->items as $it) {
                $nombreProd = $it->product->nombre ?? '';
                if (in_array($nombreProd, $controlados)) {
                    continue;
                }

                $pendiente = max(0, $it->cantidad_solicitada - $it->cantidad_despachada);
                if ($pendiente <= 0) {
                    continue;
                }

                if ($programaFiltro !== '' && mb_strtolower(trim($it->programa ?? '')) !== mb_strtolower($programaFiltro)) {
                    continue;
                }

                if ($productoFiltro !== '' && mb_strtolower(trim($nombreProd)) !== mb_strtolower($productoFiltro)) {
                    continue;
                }

                $it->setAttribute('cantidad_pendiente', $pendiente);
                $it->setAttribute('solicitud', $sol);
                $itemsPendientes->push($it);
            }
        }

        $totalSolicitudes = $itemsPendientes->pluck('establishment_request_id')->unique()->count();
        $totalLineas = $itemsPendientes->count();
        $totalUnidades = (int) $itemsPendientes->sum('cantidad_pendiente');

        $pdf = Pdf::loadView('requests.pdf.pending_establecimientos', [
            'itemsPendientes' => $itemsPendientes,
            'totalSolicitudes' => $totalSolicitudes,
            'totalLineas' => $totalLineas,
            'totalUnidades' => $totalUnidades,
            'centroFiltro' => $centroFiltro,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'programaFiltro' => $programaFiltro,
            'productoFiltro' => $productoFiltro,
            'fechaEmision' => now(),
        ]);

        $nombreArchivo = 'Solicitudes_Pendientes_Establecimientos_' . date('Ymd_His') . '.pdf';

        if ($request->boolean('download')) {
            return $pdf->download($nombreArchivo);
        }

        return $pdf->stream($nombreArchivo);
    }

    // genera el informe pdf de solicitudes pendientes exclusivo para cesfam (solo administrador)
    public function pendingCesfamPdf(Request $request)
    {
        if (! auth()->user() || ! auth()->user()->hasRole('Administrador')) {
            abort(403, 'Acceso restringido para Administradores.');
        }

        $establecimientos = array_values(array_filter(
            InventoryService::establecimientos(),
            fn (array $est) => ($est['tipo'] ?? '') === 'CESFAM'
        ));
        $nombresCesfam = array_column($establecimientos, 'nombre');
        $nombresCesfamHistoricos = [
            'CESFAM Los Cerros', 'CESFAM LOS CERROS', 'CESFAM Paulina Avendaño', 'CESFAM PAULINA AVENDAÑO',
            'CESFAM Paulina Avendaño Pereda', 'CESFAM PAULINA AVENDAÑO PEREDA', 'CESFAM San Vicente', 'CESFAM SAN VICENTE',
            'CESFAM Leocan Portus', 'CESFAM LEOCAN PORTUS', 'CESFAM Alcalde Leocán Portus Govinden', 'CESFAM ALCALDE LEOCÁN PORTUS GOVINDEN',
        ];
        $nombresQuery = array_values(array_unique(array_merge($nombresCesfam, $nombresCesfamHistoricos)));

        $solicitudId = $request->input('solicitud_id') ?: $request->input('request_id');

        // si se solicita la descarga o visualizacion de una solicitud individual
        if ($solicitudId) {
            $solicitud = EstablishmentRequest::with(['items.product', 'assignedUser'])
                ->whereIn('establecimiento', $nombresQuery)
                ->findOrFail($solicitudId);

            foreach ($solicitud->items as $it) {
                $it->setAttribute('cantidad_pendiente', max(0, $it->cantidad_solicitada - $it->cantidad_despachada));
            }

            $pdf = Pdf::loadView('requests.pdf.single_solicitud', [
                'solicitud' => $solicitud,
                'items' => $solicitud->items,
                'esCesfam' => true,
            ]);

            $correlativoSeguro = preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) ($solicitud->numero_solicitud ?: $solicitud->id));
            $nombreArchivo = 'Solicitud_' . $correlativoSeguro . '_' . date('Ymd_His') . '.pdf';

            if ($request->boolean('download')) {
                return $pdf->download($nombreArchivo);
            }

            return $pdf->stream($nombreArchivo);
        }

        // si es el listado consolidado / filtrado
        $centroFiltro = trim((string) ($request->input('centro') ?: $request->input('establecimiento')));
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $programaFiltro = trim((string) $request->input('programa'));
        $productoFiltro = trim((string) $request->input('producto'));

        $solicitudes = EstablishmentRequest::with(['items.product', 'assignedUser'])
            ->whereIn('establecimiento', $nombresQuery)
            ->whereIn('estado', ['Pendiente', 'Asignada', 'Parcial'])
            ->when($centroFiltro !== '', fn ($q) => $q->where('establecimiento', $centroFiltro))
            ->when($fechaDesde, fn ($q) => $q->whereDate('fecha_solicitud', '>=', $fechaDesde))
            ->when($fechaHasta, fn ($q) => $q->whereDate('fecha_solicitud', '<=', $fechaHasta))
            ->orderBy('created_at', 'desc')
            ->get();

        $itemsPendientes = collect();
        foreach ($solicitudes as $sol) {
            foreach ($sol->items as $it) {
                $nombreProd = $it->product->nombre ?? '';

                $pendiente = max(0, $it->cantidad_solicitada - $it->cantidad_despachada);
                if ($pendiente <= 0) {
                    continue;
                }

                if ($programaFiltro !== '' && mb_strtolower(trim($it->programa ?? '')) !== mb_strtolower($programaFiltro)) {
                    continue;
                }

                if ($productoFiltro !== '' && mb_strtolower(trim($nombreProd)) !== mb_strtolower($productoFiltro)) {
                    continue;
                }

                $it->setAttribute('cantidad_pendiente', $pendiente);
                $it->setAttribute('solicitud', $sol);
                $itemsPendientes->push($it);
            }
        }

        $totalSolicitudes = $itemsPendientes->pluck('establishment_request_id')->unique()->count();
        $totalLineas = $itemsPendientes->count();
        $totalUnidades = (int) $itemsPendientes->sum('cantidad_pendiente');

        $pdf = Pdf::loadView('requests.pdf.pending_cesfam', [
            'itemsPendientes' => $itemsPendientes,
            'totalSolicitudes' => $totalSolicitudes,
            'totalLineas' => $totalLineas,
            'totalUnidades' => $totalUnidades,
            'centroFiltro' => $centroFiltro,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'programaFiltro' => $programaFiltro,
            'productoFiltro' => $productoFiltro,
            'fechaEmision' => now(),
        ]);

        $nombreArchivo = 'Solicitudes_Pendientes_CESFAM_' . date('Ymd_His') . '.pdf';

        if ($request->boolean('download')) {
            return $pdf->download($nombreArchivo);
        }

        return $pdf->stream($nombreArchivo);
    }
}

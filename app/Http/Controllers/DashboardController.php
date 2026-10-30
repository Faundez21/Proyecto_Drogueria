<?php

namespace App\Http\Controllers;

use App\Models\ControlledDispatch;
use App\Models\Dispatch;
use App\Models\EstablishmentItem;
use App\Models\EstablishmentRequest;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Reception;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. RECIBIR TODOS LOS FILTROS (Bodega + Establecimientos y CESFAM)
        $periodo = $request->get('periodo', 'mes');
        $categoria = $request->input('categoria');
        $programa = $request->input('programa');
        $tipo_establecimiento = $request->input('tipo_establecimiento'); // '', 'cesfam', 'establecimiento'
        $establecimiento = $request->input('establecimiento');
        $estado_solicitud = $request->input('estado_solicitud');
        $fecha_desde = $request->input('fecha_desde');
        $fecha_hasta = $request->input('fecha_hasta');

        $fechaInicio = null;
        $fechaFin = Carbon::now()->endOfDay();

        // 2. CONFIGURAR RANGO DE FECHAS
        if ($periodo === 'custom' && $fecha_desde && $fecha_hasta) {
            $fechaInicio = Carbon::parse($fecha_desde)->startOfDay();
            $fechaFin = Carbon::parse($fecha_hasta)->endOfDay();
        } elseif ($fecha_desde && $fecha_hasta && ! $request->has('periodo')) {
            $fechaInicio = Carbon::parse($fecha_desde)->startOfDay();
            $fechaFin = Carbon::parse($fecha_hasta)->endOfDay();
            $periodo = 'custom';
        } else {
            $fecha_desde = null;
            $fecha_hasta = null;
            if ($periodo === 'hoy') {
                $fechaInicio = Carbon::today();
                $fechaFin = Carbon::today()->endOfDay();
            } elseif ($periodo === 'semana') {
                $fechaInicio = Carbon::now()->startOfWeek();
                $fechaFin = Carbon::now()->endOfWeek();
            } elseif ($periodo === 'anio') {
                $fechaInicio = Carbon::now()->startOfYear();
                $fechaFin = Carbon::now()->endOfYear();
            } elseif ($periodo === 'historico') {
                $fechaInicio = null;
                $fechaFin = null;
            } else {
                $fechaInicio = Carbon::now()->startOfMonth();
                $fechaFin = Carbon::now()->endOfMonth();
                $periodo = 'mes';
            }
        }

        // Catálogo de Establecimientos y CESFAM
        $catalogoEstablecimientos = InventoryService::establecimientos();
        $nombresCesfam = array_values(array_column(
            array_filter($catalogoEstablecimientos, fn ($e) => ($e['tipo'] ?? '') === 'CESFAM'),
            'nombre'
        ));
        $nombresNoCesfam = array_values(array_column(
            array_filter($catalogoEstablecimientos, fn ($e) => ($e['tipo'] ?? '') !== 'CESFAM'),
            'nombre'
        ));

        // Nombres de establecimientos filtrados según los selectores de Establecimiento / Tipo
        $establecimientosFiltro = null;
        if ($establecimiento) {
            $establecimientosFiltro = [$establecimiento];
        } elseif ($tipo_establecimiento === 'cesfam') {
            $establecimientosFiltro = $nombresCesfam;
        } elseif ($tipo_establecimiento === 'establecimiento') {
            $establecimientosFiltro = $nombresNoCesfam;
        }

        // 3. QUERY BASE DE INVENTARIO (Para KPIs que dependen de los filtros de producto)
        $invQuery = Inventory::query();
        if ($categoria) {
            $invQuery->where('categoria', $categoria);
        }
        if ($programa) {
            $invQuery->where('programa', $programa);
        }

        // KPIs PRINCIPALES (Stock actual filtrado y disponible en Bodega Central)
        $valorInventario = (clone $invQuery)
            ->where('estado', 'Disponible')
            ->where('cantidad', '>', 0)
            ->select(DB::raw('SUM(cantidad * valor_neto_unitario) as total'))
            ->first()->total ?? 0;

        $unidadesTotales = (clone $invQuery)
            ->where('estado', 'Disponible')
            ->where('cantidad', '>', 0)
            ->sum('cantidad');
        $productosCatalogo = Product::count();

        // IDs de productos que coinciden con los filtros (null = sin filtro de producto)
        $productosFiltradosIds = null;
        if ($categoria || $programa) {
            $productosFiltradosIds = (clone $invQuery)
                ->pluck('product_id')
                ->unique()
                ->filter()
                ->values()
                ->all();
        }

        // 4. MOVIMIENTOS FILTRADOS POR PERÍODO, PRODUCTOS Y ESTABLECIMIENTO/CESFAM
        $dispatchQuery = Dispatch::query();
        $controlledDispatchQuery = ControlledDispatch::query();
        $receptionQuery = Reception::query();

        if ($fechaInicio && $fechaFin) {
            $dispatchQuery->whereBetween('fecha_despacho', [$fechaInicio->toDateString(), $fechaFin->toDateString()]);
            $controlledDispatchQuery->whereBetween('fecha_despacho', [$fechaInicio->toDateString(), $fechaFin->toDateString()]);
            $receptionQuery->whereBetween('fecha_recepcion', [$fechaInicio->toDateString(), $fechaFin->toDateString()]);
        }

        if ($establecimientosFiltro !== null) {
            $dispatchQuery->whereIn('establecimiento_destino', $establecimientosFiltro);
            $controlledDispatchQuery->whereIn('establecimiento_destino', $establecimientosFiltro);
        }

        if ($productosFiltradosIds !== null || $programa) {
            $dispatchQuery->whereHas('items', function ($q) use ($productosFiltradosIds, $programa) {
                if ($productosFiltradosIds !== null) {
                    $q->whereIn('product_id', $productosFiltradosIds);
                }
                if ($programa) {
                    $q->where('programa', $programa);
                }
            });
            $controlledDispatchQuery->whereHas('items', function ($q) use ($productosFiltradosIds, $programa) {
                if ($productosFiltradosIds !== null) {
                    $q->whereIn('product_id', $productosFiltradosIds);
                }
                if ($programa) {
                    $q->where('programa', $programa);
                }
            });
            if ($productosFiltradosIds !== null) {
                $receptionQuery->whereHas('items', function ($q) use ($productosFiltradosIds) {
                    $q->whereIn('product_id', $productosFiltradosIds);
                });
            }
        }

        $despachosPeriodo = $dispatchQuery->count() + $controlledDispatchQuery->count();
        $recepcionesPeriodo = $receptionQuery->count();

        // Totales históricos (sin límite de fecha, conservando filtros de producto y establecimiento)
        $dispatchTotalQuery = Dispatch::query();
        $controlledTotalQuery = ControlledDispatch::query();
        $receptionTotalQuery = Reception::query();

        if ($establecimientosFiltro !== null) {
            $dispatchTotalQuery->whereIn('establecimiento_destino', $establecimientosFiltro);
            $controlledTotalQuery->whereIn('establecimiento_destino', $establecimientosFiltro);
        }

        if ($productosFiltradosIds !== null) {
            $dispatchTotalQuery->whereHas('items', fn ($q) => $q->whereIn('product_id', $productosFiltradosIds));
            $controlledTotalQuery->whereHas('items', fn ($q) => $q->whereIn('product_id', $productosFiltradosIds));
            $receptionTotalQuery->whereHas('items', fn ($q) => $q->whereIn('product_id', $productosFiltradosIds));
        }

        $totalDespachos = $dispatchTotalQuery->count() + $controlledTotalQuery->count();
        $totalRecepciones = $receptionTotalQuery->count();

        // 5. KPIs Y ESTADÍSTICAS DE ESTABLECIMIENTOS Y CESFAM
        $reqBaseQuery = EstablishmentRequest::with(['items.product', 'assignedUser']);

        if ($fechaInicio && $fechaFin) {
            $reqBaseQuery->whereBetween('fecha_solicitud', [$fechaInicio->toDateString(), $fechaFin->toDateString()]);
        }
        if ($establecimientosFiltro !== null) {
            $reqBaseQuery->whereIn('establecimiento', $establecimientosFiltro);
        }
        if ($estado_solicitud) {
            $reqBaseQuery->where('estado', $estado_solicitud);
        }
        if ($programa || $productosFiltradosIds !== null) {
            $reqBaseQuery->whereHas('items', function ($q) use ($programa, $productosFiltradosIds) {
                if ($programa) {
                    $q->where('programa', $programa);
                }
                if ($productosFiltradosIds !== null) {
                    $q->whereIn('product_id', $productosFiltradosIds);
                }
            });
        }

        $solicitudesFiltradas = $reqBaseQuery->orderBy('created_at', 'desc')->get();

        // Separar métricas CESFAM vs Establecimientos (No-CESFAM)
        $solicitudesCesfam = $solicitudesFiltradas->filter(fn ($s) => in_array($s->establecimiento, $nombresCesfam));
        $solicitudesEstablecimientos = $solicitudesFiltradas->filter(fn ($s) => ! in_array($s->establecimiento, $nombresCesfam));

        $solicitudesCesfamCount = $solicitudesCesfam->count();
        $cesfamActivasCount = $solicitudesCesfam->whereIn('estado', ['Pendiente', 'Asignada', 'Parcial'])->count();
        $cesfamDespachadasCount = $solicitudesCesfam->where('estado', 'Despachado')->count();

        $solicitudesEstablecimientosCount = $solicitudesEstablecimientos->count();
        $estabActivasCount = $solicitudesEstablecimientos->whereIn('estado', ['Pendiente', 'Asignada', 'Parcial'])->count();
        $estabDespachadasCount = $solicitudesEstablecimientos->where('estado', 'Despachado')->count();

        $solicitudesPorDespacharCount = $solicitudesFiltradas->whereIn('estado', ['Pendiente', 'Asignada', 'Parcial'])->count();
        $solicitudesAsignadasCount = $solicitudesFiltradas->whereIn('estado', ['Pendiente', 'Asignada', 'Parcial'])->whereNotNull('assigned_user_id')->count();
        $solicitudesSinAsignarCount = $solicitudesPorDespacharCount - $solicitudesAsignadasCount;

        // Unidades despachadas a CESFAM vs Establecimientos en el período
        $unidadesNormalesPorDest = DB::table('dispatches')
            ->join('dispatch_items', 'dispatches.id', '=', 'dispatch_items.dispatch_id')
            ->when($fechaInicio && $fechaFin, fn ($q) => $q->whereBetween('dispatches.fecha_despacho', [$fechaInicio->toDateString(), $fechaFin->toDateString()]))
            ->when($establecimientosFiltro !== null, fn ($q) => $q->whereIn('dispatches.establecimiento_destino', $establecimientosFiltro))
            ->when($programa, fn ($q) => $q->where('dispatch_items.programa', $programa))
            ->when($productosFiltradosIds !== null, fn ($q) => $q->whereIn('dispatch_items.product_id', $productosFiltradosIds))
            ->select('dispatches.establecimiento_destino as destino', DB::raw('SUM(dispatch_items.cantidad) as total_unidades'))
            ->groupBy('dispatches.establecimiento_destino')
            ->pluck('total_unidades', 'destino');

        $unidadesCtrlPorDest = DB::table('controlled_dispatches')
            ->join('controlled_dispatch_items', 'controlled_dispatches.id', '=', 'controlled_dispatch_items.controlled_dispatch_id')
            ->when($fechaInicio && $fechaFin, fn ($q) => $q->whereBetween('controlled_dispatches.fecha_despacho', [$fechaInicio->toDateString(), $fechaFin->toDateString()]))
            ->when($establecimientosFiltro !== null, fn ($q) => $q->whereIn('controlled_dispatches.establecimiento_destino', $establecimientosFiltro))
            ->when($programa, fn ($q) => $q->where('controlled_dispatch_items.programa', $programa))
            ->when($productosFiltradosIds !== null, fn ($q) => $q->whereIn('controlled_dispatch_items.product_id', $productosFiltradosIds))
            ->select('controlled_dispatches.establecimiento_destino as destino', DB::raw('SUM(controlled_dispatch_items.cantidad) as total_unidades'))
            ->groupBy('controlled_dispatches.establecimiento_destino')
            ->pluck('total_unidades', 'destino');

        $unidadesDespachadasCesfam = 0;
        foreach ($nombresCesfam as $nomCesfam) {
            $unidadesDespachadasCesfam += (int) ($unidadesNormalesPorDest[$nomCesfam] ?? 0) + (int) ($unidadesCtrlPorDest[$nomCesfam] ?? 0);
        }

        $unidadesDespachadasEstablecimientos = 0;
        foreach ($nombresNoCesfam as $nomEst) {
            $unidadesDespachadasEstablecimientos += (int) ($unidadesNormalesPorDest[$nomEst] ?? 0) + (int) ($unidadesCtrlPorDest[$nomEst] ?? 0);
        }

        // Totales de stock declarado, consumo anterior y cumplimiento sobre las solicitudes filtradas
        $stockDeclaradoRed = 0;
        $totalUnidadesSolicitadas = 0;
        $totalUnidadesEntregadasSolicitudes = 0;

        foreach ($solicitudesFiltradas as $sol) {
            foreach ($sol->items as $it) {
                if ($programa && $it->programa !== $programa) {
                    continue;
                }
                if ($productosFiltradosIds !== null && ! in_array($it->product_id, $productosFiltradosIds)) {
                    continue;
                }
                $stockDeclaradoRed += (int) ($it->stock_actual ?? 0);
                $totalUnidadesSolicitadas += (int) ($it->cantidad_solicitada ?? 0);
                $totalUnidadesEntregadasSolicitudes += (int) ($it->cantidad_despachada ?? 0);
            }
        }

        $porcentajeCumplimientoRed = $totalUnidadesSolicitadas > 0
            ? round(($totalUnidadesEntregadasSolicitudes / $totalUnidadesSolicitadas) * 100, 1)
            : 0;

        // Resumen detallado por cada Establecimiento / CESFAM para la tabla comparativa del Dashboard
        $resumenEstablecimientos = collect($catalogoEstablecimientos)
            ->filter(function ($est) use ($establecimientosFiltro) {
                return $establecimientosFiltro === null || in_array($est['nombre'], $establecimientosFiltro);
            })
            ->map(function ($est) use ($solicitudesFiltradas, $unidadesNormalesPorDest, $unidadesCtrlPorDest, $programa, $productosFiltradosIds) {
                $nombre = $est['nombre'];
                $esCesfam = ($est['tipo'] ?? '') === 'CESFAM';
                $solsEst = $solicitudesFiltradas->where('establecimiento', $nombre);

                $solicitado = 0;
                $despachadoSol = 0;
                $stockActual = 0;
                $consumoAnt = 0;

                foreach ($solsEst as $s) {
                    foreach ($s->items as $it) {
                        if ($programa && $it->programa !== $programa) {
                            continue;
                        }
                        if ($productosFiltradosIds !== null && ! in_array($it->product_id, $productosFiltradosIds)) {
                            continue;
                        }
                        $solicitado += (int) ($it->cantidad_solicitada ?? 0);
                        $despachadoSol += (int) ($it->cantidad_despachada ?? 0);
                        $stockActual += (int) ($it->stock_actual ?? 0);
                        $consumoAnt += (int) ($it->consumo_anterior ?? 0);
                    }
                }

                $unidadesGuias = (int) ($unidadesNormalesPorDest[$nombre] ?? 0) + (int) ($unidadesCtrlPorDest[$nombre] ?? 0);
                $ultimaFecha = $solsEst->max('fecha_solicitud');

                return (object) [
                    'nombre' => $nombre,
                    'tipo' => $est['tipo'] ?? 'Establecimiento',
                    'es_cesfam' => $esCesfam,
                    'total_solicitudes' => $solsEst->count(),
                    'activas' => $solsEst->whereIn('estado', ['Pendiente', 'Asignada', 'Parcial'])->count(),
                    'despachadas' => $solsEst->where('estado', 'Despachado')->count(),
                    'stock_declarado' => $stockActual,
                    'consumo_anterior' => $consumoAnt,
                    'unidades_solicitadas' => $solicitado,
                    'unidades_despachadas' => max($despachadoSol, $unidadesGuias),
                    'cumplimiento' => $solicitado > 0 ? min(100, round(($despachadoSol / $solicitado) * 100)) : ($unidadesGuias > 0 ? 100 : 0),
                    'ultima_solicitud' => $ultimaFecha,
                ];
            })
            ->sortByDesc(fn ($r) => ($r->total_solicitudes * 1000) + $r->unidades_despachadas)
            ->values();

        // Solicitudes asignadas al usuario logueado (para que las vea apenas inicie sesión)
        $misSolicitudesAsignadas = EstablishmentRequest::with(['items.product', 'assignedUser'])
            ->where('assigned_user_id', Auth::id())
            ->whereIn('estado', ['Pendiente', 'Asignada', 'Parcial'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 6. ALERTAS (Vencimientos y Cuarentenas - Respetando Filtros Base)
        $proximosVencer = (clone $invQuery)
            ->where('estado', 'Disponible')
            ->where('cantidad', '>', 0)
            ->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<=', Carbon::now()->addDays(30))
            ->count();

        $cantidadCuarentena = (clone $invQuery)->where('estado', 'Cuarentena')
            ->where('cantidad', '>', 0)
            ->count();

        $lotesCuarentenaDb = (clone $invQuery)->where('estado', 'Cuarentena')
            ->where('cantidad', '>', 0)
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get();

        // 7. TRAZABILIDAD RECIENTE (Últimos 6 movimientos combinados)
        $recentMovements = collect();

        // Recepciones (si no se está filtrando por un establecimiento de destino específico)
        if ($establecimientosFiltro === null) {
            $recBuilder = DB::table('receptions')
                ->join('reception_items', 'receptions.id', '=', 'reception_items.reception_id')
                ->leftJoin('providers', 'receptions.provider_id', '=', 'providers.id')
                ->leftJoin('products', 'reception_items.product_id', '=', 'products.id');

            if ($fechaInicio && $fechaFin) {
                $recBuilder->whereBetween('receptions.fecha_recepcion', [$fechaInicio->toDateString(), $fechaFin->toDateString()]);
            }
            if ($productosFiltradosIds !== null) {
                $recBuilder->whereIn('reception_items.product_id', $productosFiltradosIds);
            }

            $lastReceptions = $recBuilder->select(
                'products.nombre as producto',
                'reception_items.lote',
                'reception_items.cantidad',
                'receptions.created_at as fecha',
                'providers.name as origen',
                DB::raw("'Recepción' as tipo")
            )->orderBy('receptions.created_at', 'desc')->take(6)->get();

            foreach ($lastReceptions as $rec) {
                $recentMovements->push((object) [
                    'tipo_movimiento' => 'Recepción',
                    'producto' => $rec->producto ?? 'S/N',
                    'lote' => $rec->lote ?? 'S/L',
                    'cantidad' => $rec->cantidad,
                    'fecha_hora' => $rec->fecha,
                    'origen_destino' => 'De: ' . ($rec->origen ?? 'Proveedor'),
                ]);
            }
        }

        // Despachos normales
        $dispBuilder = DB::table('dispatches')
            ->join('dispatch_items', 'dispatches.id', '=', 'dispatch_items.dispatch_id');

        if ($fechaInicio && $fechaFin) {
            $dispBuilder->whereBetween('dispatches.fecha_despacho', [$fechaInicio->toDateString(), $fechaFin->toDateString()]);
        }
        if ($establecimientosFiltro !== null) {
            $dispBuilder->whereIn('dispatches.establecimiento_destino', $establecimientosFiltro);
        }
        if ($productosFiltradosIds !== null) {
            $dispBuilder->whereIn('dispatch_items.product_id', $productosFiltradosIds);
        }

        $lastDispatches = $dispBuilder->select(
            'dispatch_items.producto',
            'dispatch_items.lote',
            'dispatch_items.cantidad',
            'dispatches.created_at as fecha',
            'dispatches.establecimiento_destino as destino'
        )->orderBy('dispatches.created_at', 'desc')->take(6)->get();

        foreach ($lastDispatches as $disp) {
            $recentMovements->push((object) [
                'tipo_movimiento' => 'Despacho',
                'producto' => $disp->producto ?? 'S/N',
                'lote' => $disp->lote ?? 'S/L',
                'cantidad' => $disp->cantidad,
                'fecha_hora' => $disp->fecha,
                'origen_destino' => 'A: ' . ($disp->destino ?? 'Destino'),
            ]);
        }

        // Despachos controlados
        $ctrlDispBuilder = DB::table('controlled_dispatches')
            ->join('controlled_dispatch_items', 'controlled_dispatches.id', '=', 'controlled_dispatch_items.controlled_dispatch_id');

        if ($fechaInicio && $fechaFin) {
            $ctrlDispBuilder->whereBetween('controlled_dispatches.fecha_despacho', [$fechaInicio->toDateString(), $fechaFin->toDateString()]);
        }
        if ($establecimientosFiltro !== null) {
            $ctrlDispBuilder->whereIn('controlled_dispatches.establecimiento_destino', $establecimientosFiltro);
        }
        if ($productosFiltradosIds !== null) {
            $ctrlDispBuilder->whereIn('controlled_dispatch_items.product_id', $productosFiltradosIds);
        }

        $lastCtrlDispatches = $ctrlDispBuilder->select(
            'controlled_dispatch_items.producto',
            'controlled_dispatch_items.lote',
            'controlled_dispatch_items.cantidad',
            'controlled_dispatches.created_at as fecha',
            'controlled_dispatches.establecimiento_destino as destino'
        )->orderBy('controlled_dispatches.created_at', 'desc')->take(6)->get();

        foreach ($lastCtrlDispatches as $cdisp) {
            $recentMovements->push((object) [
                'tipo_movimiento' => 'Despacho Controlado',
                'producto' => $cdisp->producto ?? 'S/N',
                'lote' => $cdisp->lote ?? 'S/L',
                'cantidad' => $cdisp->cantidad,
                'fecha_hora' => $cdisp->fecha,
                'origen_destino' => 'A: ' . ($cdisp->destino ?? 'CESFAM'),
            ]);
        }

        $recentMovements = $recentMovements->sortByDesc('fecha_hora')->take(6)->values();

        // 8. ÚLTIMO LOTE INGRESADO A BODEGA (Para panel de rotulación QR)
        $latestInventory = (clone $invQuery)->where('cantidad', '>', 0)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();

        if (! $latestInventory) {
            $latestInventory = Inventory::where('cantidad', '>', 0)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->first();
        }

        // 9. LISTAS PARA DROPDOWNS
        $categoriasLista = Inventory::whereNotNull('categoria')->where('categoria', '!=', '')->distinct()->orderBy('categoria')->pluck('categoria');
        $programasLista = Inventory::whereNotNull('programa')->where('programa', '!=', '')->distinct()->orderBy('programa')->pluck('programa')
            ->merge(['Morbilidad General', 'Cardiovascular', 'IRA/ERA'])
            ->unique()
            ->sort()
            ->values();

        return view('dashboard.index', compact(
            'valorInventario',
            'unidadesTotales',
            'despachosPeriodo',
            'totalDespachos',
            'recepcionesPeriodo',
            'totalRecepciones',
            'productosCatalogo',
            'proximosVencer',
            'cantidadCuarentena',
            'lotesCuarentenaDb',
            'recentMovements',
            'latestInventory',
            'periodo',
            'categoria',
            'programa',
            'tipo_establecimiento',
            'establecimiento',
            'estado_solicitud',
            'fecha_desde',
            'fecha_hasta',
            'categoriasLista',
            'programasLista',
            'catalogoEstablecimientos',
            'nombresCesfam',
            'nombresNoCesfam',
            'solicitudesCesfamCount',
            'cesfamActivasCount',
            'cesfamDespachadasCount',
            'solicitudesEstablecimientosCount',
            'estabActivasCount',
            'estabDespachadasCount',
            'solicitudesPorDespacharCount',
            'solicitudesAsignadasCount',
            'solicitudesSinAsignarCount',
            'unidadesDespachadasCesfam',
            'unidadesDespachadasEstablecimientos',
            'stockDeclaradoRed',
            'totalUnidadesSolicitadas',
            'totalUnidadesEntregadasSolicitudes',
            'porcentajeCumplimientoRed',
            'resumenEstablecimientos',
            'misSolicitudesAsignadas'
        ));
    }
}
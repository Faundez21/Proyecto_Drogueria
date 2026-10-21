<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Reception;
use App\Models\Dispatch;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. RECIBIR TODOS LOS FILTROS
        $periodo = $request->get('periodo', 'mes');
        $categoria = $request->input('categoria');
        $programa = $request->input('programa');
        $fecha_desde = $request->input('fecha_desde');
        $fecha_hasta = $request->input('fecha_hasta');

        $fechaInicio = null;
        $fechaFin = Carbon::now();

        // 2. CONFIGURAR RANGO DE FECHAS (Prioriza Custom sobre Periodo Rápido)
        if ($fecha_desde && $fecha_hasta) {
            $fechaInicio = Carbon::parse($fecha_desde)->startOfDay();
            $fechaFin = Carbon::parse($fecha_hasta)->endOfDay();
            $periodo = 'custom';
        } else {
            if ($periodo == 'hoy') {
                $fechaInicio = Carbon::today();
                $fechaFin = Carbon::today()->endOfDay();
            } elseif ($periodo == 'semana') {
                $fechaInicio = Carbon::now()->startOfWeek();
                $fechaFin = Carbon::now()->endOfWeek();
            } elseif ($periodo == 'anio') {
                $fechaInicio = Carbon::now()->startOfYear();
                $fechaFin = Carbon::now()->endOfYear();
            } else {
                $fechaInicio = Carbon::now()->startOfMonth();
                $fechaFin = Carbon::now()->endOfMonth();
                $periodo = 'mes';
            }
        }

        // 3. QUERY BASE DE INVENTARIO (Para KPIs que dependen de los filtros de producto)
        $invQuery = Inventory::query();
        if ($categoria) {
            $invQuery->where('categoria', $categoria);
        }
        if ($programa) {
            $invQuery->where('programa', $programa);
        }

        // KPIs PRINCIPALES (Stock actual filtrado)
        $valorInventario = (clone $invQuery)->where('cantidad', '>', 0)
            ->select(DB::raw('SUM(cantidad * valor_neto_unitario) as total'))
            ->first()->total ?? 0;

        $unidadesTotales = (clone $invQuery)->where('cantidad', '>', 0)->sum('cantidad');
        $productosCatalogo = Product::count(); // General, no se filtra

        // IDs de productos que coinciden con los filtros (null = sin filtro de producto)
        $productosFiltradosIds = null;
        if ($categoria || $programa) {
            $productosFiltradosIds = (clone $invQuery)
                ->pluck('product_id')
                ->unique()
                ->filter()
                ->values()
                ->all(); // si queda vacío, whereIn no devuelve resultados (correcto)
        }

        // 4. MOVIMIENTOS FILTRADOS POR PERÍODO Y PRODUCTOS
        $dispatchQuery = Dispatch::query();
        $receptionQuery = Reception::query();

        if ($fechaInicio && $fechaFin) {
            $dispatchQuery->whereBetween('fecha_despacho', [$fechaInicio, $fechaFin]);
            $receptionQuery->whereBetween('fecha_recepcion', [$fechaInicio, $fechaFin]);
        }

        if ($productosFiltradosIds !== null) {
            $dispatchQuery->whereHas('items', function ($q) use ($productosFiltradosIds) {
                $q->whereIn('product_id', $productosFiltradosIds);
            });
            $receptionQuery->whereHas('items', function ($q) use ($productosFiltradosIds) {
                $q->whereIn('product_id', $productosFiltradosIds);
            });
        }

        $despachosPeriodo = $dispatchQuery->count();
        $recepcionesPeriodo = $receptionQuery->count();

        // Totales históricos (sin límite de fecha, conservando filtro de producto)
        $dispatchTotalQuery = Dispatch::query();
        $receptionTotalQuery = Reception::query();

        if ($productosFiltradosIds !== null) {
            $dispatchTotalQuery->whereHas('items', function ($q) use ($productosFiltradosIds) {
                $q->whereIn('product_id', $productosFiltradosIds);
            });
            $receptionTotalQuery->whereHas('items', function ($q) use ($productosFiltradosIds) {
                $q->whereIn('product_id', $productosFiltradosIds);
            });
        }

        $totalDespachos = $dispatchTotalQuery->count();
        $totalRecepciones = $receptionTotalQuery->count();

        // 5. ALERTAS (Vencimientos y Cuarentenas - Respetando Filtros Base)
        $proximosVencer = (clone $invQuery)->where('cantidad', '>', 0)
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

        // 6. TRAZABILIDAD RECIENTE (Últimos 6 movimientos combinados)
        $recentMovements = collect();

        // Recepciones
        $recBuilder = DB::table('receptions')
            ->join('reception_items', 'receptions.id', '=', 'reception_items.reception_id')
            ->leftJoin('providers', 'receptions.provider_id', '=', 'providers.id')
            ->leftJoin('products', 'reception_items.product_id', '=', 'products.id');

        if ($fechaInicio && $fechaFin) {
            $recBuilder->whereBetween('receptions.fecha_recepcion', [$fechaInicio, $fechaFin]);
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

        // Despachos
        $dispBuilder = DB::table('dispatches')
            ->join('dispatch_items', 'dispatches.id', '=', 'dispatch_items.dispatch_id');

        if ($fechaInicio && $fechaFin) {
            $dispBuilder->whereBetween('dispatches.fecha_despacho', [$fechaInicio, $fechaFin]);
        }
        if ($productosFiltradosIds !== null) {
            $dispBuilder->whereIn('dispatch_items.product_id', $productosFiltradosIds);
        }

        $lastDispatches = $dispBuilder->select(
            'dispatch_items.producto',
            'dispatch_items.lote',
            'dispatch_items.cantidad',
            'dispatches.created_at as fecha',
            'dispatches.establecimiento_destino as destino',
            DB::raw("'Despacho' as tipo")
        )->orderBy('dispatches.created_at', 'desc')->take(6)->get();

        foreach ($lastReceptions as $rec) {
            $recentMovements->push((object) [
                'tipo_movimiento' => 'Recepción',
                'producto'        => $rec->producto ?? 'S/N',
                'lote'            => $rec->lote ?? 'S/L',
                'cantidad'        => $rec->cantidad,
                'fecha_hora'      => $rec->fecha,
                'origen_destino'  => 'De: ' . ($rec->origen ?? 'Proveedor'),
            ]);
        }

        foreach ($lastDispatches as $disp) {
            $recentMovements->push((object) [
                'tipo_movimiento' => 'Despacho',
                'producto'        => $disp->producto ?? 'S/N',
                'lote'            => $disp->lote ?? 'S/L',
                'cantidad'        => $disp->cantidad,
                'fecha_hora'      => $disp->fecha,
                'origen_destino'  => 'A: ' . ($disp->destino ?? 'Destino'),
            ]);
        }

        $recentMovements = $recentMovements->sortByDesc('fecha_hora')->take(6)->values();

        // 7. ÚLTIMO LOTE INGRESADO (Para panel QR)
        $latestInventory = (clone $invQuery)->where('cantidad', '>', 0)->latest('created_at')->first();

        // 8. LISTAS PARA DROPDOWNS
        $categoriasLista = Inventory::whereNotNull('categoria')->where('categoria', '!=', '')->distinct()->orderBy('categoria')->pluck('categoria');
        $programasLista = Inventory::whereNotNull('programa')->where('programa', '!=', '')->distinct()->orderBy('programa')->pluck('programa');

        return view('dashboard.index', compact(
            'valorInventario', 'unidadesTotales', 'despachosPeriodo', 'totalDespachos',
            'recepcionesPeriodo', 'totalRecepciones', 'productosCatalogo',
            'proximosVencer', 'cantidadCuarentena', 'lotesCuarentenaDb',
            'recentMovements', 'latestInventory', 'periodo',
            'categoria', 'programa', 'fecha_desde', 'fecha_hasta',
            'categoriasLista', 'programasLista'
        ));
    }
}
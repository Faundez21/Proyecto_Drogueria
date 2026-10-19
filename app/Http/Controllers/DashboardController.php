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
        // FILTRO GLOBAL DE PERÍODO (Por defecto: Este Mes)
        $periodo = $request->get('periodo', 'mes');
        
        $fechaInicio = Carbon::now()->startOfMonth();
        $fechaFin = Carbon::now()->endOfMonth();

        if ($periodo == 'hoy') {
            $fechaInicio = Carbon::today();
            $fechaFin = Carbon::today()->endOfDay();
        } elseif ($periodo == 'semana') {
            $fechaInicio = Carbon::now()->startOfWeek();
            $fechaFin = Carbon::now()->endOfWeek();
        } elseif ($periodo == 'anio') {
            $fechaInicio = Carbon::now()->startOfYear();
            $fechaFin = Carbon::now()->endOfYear();
        }

        // 1. KPIs PRINCIPALES (Stock actual no depende de fechas, es lo que hay HOY en bodega)
        $valorInventario = Inventory::where('cantidad', '>', 0)
            ->select(DB::raw('SUM(cantidad * valor_neto_unitario) as total'))
            ->first()->total ?? 0;

        $unidadesTotales = Inventory::where('cantidad', '>', 0)->sum('cantidad');
        $productosCatalogo = Product::count();

        // Movimientos filtrados por el período seleccionado
        $despachosPeriodo = Dispatch::whereBetween('fecha_despacho', [$fechaInicio, $fechaFin])->count();
        $totalDespachos = Dispatch::count();
        
        $recepcionesPeriodo = Reception::whereBetween('fecha_recepcion', [$fechaInicio, $fechaFin])->count();
        $totalRecepciones = Reception::count();

        // 2. ALERTAS (Vencimientos y Cuarentenas)
        $proximosVencer = Inventory::where('cantidad', '>', 0)
            ->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<=', Carbon::now()->addDays(30))
            ->count();

        $lotesCuarentenaDb = Inventory::where('estado', 'Cuarentena')
            ->where('cantidad', '>', 0)
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get();

        $cantidadCuarentena = Inventory::where('estado', 'Cuarentena')
            ->where('cantidad', '>', 0)
            ->count();

        // 3. TRAZABILIDAD RECIENTE (Últimos 6 movimientos dinámicos)
        $recentMovements = collect();

        $lastReceptions = DB::table('receptions')
            ->join('reception_items', 'receptions.id', '=', 'reception_items.reception_id')
            ->leftJoin('providers', 'receptions.provider_id', '=', 'providers.id')
            ->leftJoin('products', 'reception_items.product_id', '=', 'products.id')
            ->select(
                'products.nombre as producto', 'reception_items.lote', 'reception_items.cantidad',
                'receptions.created_at as fecha', 'providers.name as origen',
                DB::raw("'Recepción' as tipo")
            )->orderBy('receptions.created_at', 'desc')->take(6)->get();

        $lastDispatches = DB::table('dispatches')
            ->join('dispatch_items', 'dispatches.id', '=', 'dispatch_items.dispatch_id')
            ->select(
                'dispatch_items.producto', 'dispatch_items.lote', 'dispatch_items.cantidad',
                'dispatches.created_at as fecha', 'dispatches.establecimiento_destino as destino',
                DB::raw("'Despacho' as tipo")
            )->orderBy('dispatches.created_at', 'desc')->take(6)->get();

        foreach($lastReceptions as $rec) {
            $recentMovements->push((object)[
                'tipo' => 'Recepción', 'producto' => $rec->producto ?? 'S/N', 
                'lote' => $rec->lote ?? 'S/L', 'cantidad' => $rec->cantidad, 'fecha' => $rec->fecha, 'entidad' => 'De: ' . ($rec->origen ?? 'Proveedor')
            ]);
        }
        foreach($lastDispatches as $disp) {
            $recentMovements->push((object)[
                'tipo' => 'Despacho', 'producto' => $disp->producto ?? 'S/N', 
                'lote' => $disp->lote ?? 'S/L', 'cantidad' => $disp->cantidad, 'fecha' => $disp->fecha, 'entidad' => 'A: ' . ($disp->destino ?? 'Destino')
            ]);
        }
        
        $recentMovements = $recentMovements->sortByDesc('fecha')->take(6)->values();

        // 4. ÚLTIMO LOTE INGRESADO (Para panel QR)
        $latestInventory = Inventory::where('cantidad', '>', 0)->latest('created_at')->first();

        return view('dashboard.index', compact(
            'valorInventario', 'unidadesTotales', 'despachosPeriodo', 'totalDespachos', 
            'recepcionesPeriodo', 'totalRecepciones', 'productosCatalogo', 
            'proximosVencer', 'cantidadCuarentena', 'lotesCuarentenaDb', 
            'recentMovements', 'latestInventory', 'periodo'
        ));
    }
}
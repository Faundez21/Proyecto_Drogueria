<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (!$query || mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];
        $searchTerm = '%' . $query . '%';

        // 1. INVENTARIO (Busca en Bodega Físicamente)
        try {
            $inventario = DB::table('inventories')
                ->where('cantidad', '>', 0)
                ->where(function($q) use ($searchTerm) {
                    $q->where('lote', 'LIKE', $searchTerm)
                      ->orWhere('producto', 'LIKE', $searchTerm)
                      ->orWhere('correlativo_interno', 'LIKE', $searchTerm)
                      ->orWhere('rut_proveedor', 'LIKE', $searchTerm);
                })
                ->limit(4)->get();
                
            foreach ($inventario as $inv) {
                $results[] = [
                    'type' => 'Stock en Bodega',
                    'title' => $inv->producto,
                    'subtitle' => 'Lote: ' . ($inv->lote ?? 'S/L') . ' | Disp: ' . $inv->cantidad . ' uds.',
                    'url' => route('inventario.index') . '?search=' . urlencode($inv->lote ?? $inv->producto),
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>'
                ];
            }
        } catch (\Exception $e) {}

        // 2. CATÁLOGO MAESTRO (Productos registrados)
        try {
            $productos = DB::table('products')
                ->where('nombre', 'LIKE', $searchTerm)
                ->limit(3)->get();
                
            foreach ($productos as $prod) {
                $results[] = [
                    'type' => 'Catálogo General',
                    'title' => $prod->nombre,
                    'subtitle' => 'Ficha maestra de producto',
                    'url' => route('inventario.index') . '?search=' . urlencode($prod->nombre),
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>'
                ];
            }
        } catch (\Exception $e) {}

        // 3. PROVEEDORES
        try {
            $proveedores = DB::table('providers')
                ->where('name', 'LIKE', $searchTerm)
                ->orWhere('nombre', 'LIKE', $searchTerm)
                ->orWhere('razon_social', 'LIKE', $searchTerm)
                ->orWhere('rut', 'LIKE', $searchTerm)
                ->limit(2)->get();
                
            foreach ($proveedores as $prov) {
                $results[] = [
                    'type' => 'Proveedores',
                    'title' => $prov->name ?? $prov->nombre ?? $prov->razon_social ?? 'Proveedor Externo',
                    'subtitle' => 'RUT: ' . ($prov->rut ?? 'S/R'),
                    'url' => route('providers.index') . '?search=' . urlencode($prov->rut ?? ''),
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>'
                ];
            }
        } catch (\Exception $e) {}

        return response()->json($results);
    }

    public function notifications()
    {
        $notifications = [];

        try {
            $vencimientos = DB::table('inventories')
                ->where('cantidad', '>', 0)
                ->whereNotNull('fecha_vencimiento')
                ->where('fecha_vencimiento', '<=', Carbon::now()->addDays(60))
                ->orderBy('fecha_vencimiento', 'asc')
                ->limit(5)
                ->get();

            foreach($vencimientos as $vence) {
                $dias = intval(Carbon::parse($vence->fecha_vencimiento)->diffInDays(Carbon::now()));
                $estado = Carbon::parse($vence->fecha_vencimiento)->isPast() ? 'Vencido hace' : 'Vence en';

                $notifications[] = [
                    'color' => 'text-rose-600',
                    'bg' => 'bg-rose-100',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
                    'title' => 'Alerta FEFO (Vencimiento)',
                    'message' => "Lote {$vence->lote} de {$vence->producto}. {$estado} {$dias} días.",
                    'time' => 'Crítico',
                    'url' => route('traceability.index') . '?search=' . urlencode($vence->lote)
                ];
            }

            $cuarentena = DB::table('inventories')
                ->where('estado', 'Cuarentena')
                ->where('cantidad', '>', 0)
                ->limit(3)->get();

            foreach($cuarentena as $cuar) {
                $dias = (int) floor(Carbon::parse($cuar->updated_at ?? $cuar->created_at)->startOfDay()->diffInDays(now()->startOfDay()));

                $mensajeCuarentena = $dias == 0 ? "Lote {$cuar->lote} fue enviado a cuarentena hoy." : "Lote {$cuar->lote} lleva {$dias} días retenido.";

                $notifications[] = [
                    'color' => 'text-amber-600',
                    'bg' => 'bg-amber-100',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
                    'title' => 'Lote en Cuarentena',
                    'message' => $mensajeCuarentena,
                    'time' => 'Revisión',
                    'url' => route('quarantine.index')
                ];
            }

            $stockBajo = DB::table('inventories')
                ->where('estado', 'Disponible')
                ->where('cantidad', '>', 0)
                ->whereRaw('cantidad <= CASE WHEN minimo > 0 THEN minimo ELSE 0 END')
                ->orderBy('cantidad', 'asc')
                ->limit(3)
                ->get();

            foreach($stockBajo as $bajo) {
                $notifications[] = [
                    'color' => 'text-orange-600',
                    'bg' => 'bg-orange-100',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                    'title' => 'Stock Bajo',
                    'message' => "{$bajo->producto} (Lote " . ($bajo->lote ?? 'S/L') . "): quedan {$bajo->cantidad} uds. (mínimo {$bajo->minimo}).",
                    'time' => 'Reponer',
                    'url' => route('inventario.index') . '?search=' . urlencode($bajo->producto)
                ];
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'count' => count($notifications),
            'data' => collect($notifications)->take(8)->values()
        ]);
    }
}
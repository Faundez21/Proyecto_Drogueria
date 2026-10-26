<?php

namespace App\Http\Controllers;

use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (! $query || mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $user = auth()->user();
        if (! $user) {
            return response()->json([]);
        }

        $puedeVerControlados = $user->hasRole('Administrador') || $user->can('ver.despacho_controlado');
        $controladosNombres = InventoryService::controlados();

        $results = [];
        $searchTerm = '%' . $query . '%';

        // 1. INVENTARIO (Busca en Bodega Físicamente - requiere permiso ver.inventario)
        if ($user->can('ver.inventario')) {
            try {
                $inventario = DB::table('inventories')
                    ->where('cantidad', '>', 0)
                    ->when(! $puedeVerControlados, function ($q) {
                        $q->where('categoria', '!=', 'Fármacos Controlados');
                    })
                    ->where(function ($q) use ($searchTerm) {
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
                        'subtitle' => 'Lote: ' . ($inv->lote ?? 'S/L') . ' | Disp: ' . $inv->cantidad . ' uds.' . (! empty($inv->estante) ? ' · Estante ' . $inv->estante : ''),
                        'url' => route('inventory.index') . '?search=' . urlencode($inv->lote ?? $inv->producto),
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>',
                    ];
                }
            } catch (\Exception $e) {
            }

            // 2. CATÁLOGO MAESTRO (Productos registrados)
            try {
                $productos = DB::table('products')
                    ->where('nombre', 'LIKE', $searchTerm)
                    ->when(! $puedeVerControlados, function ($q) use ($controladosNombres) {
                        $q->whereNotIn('nombre', $controladosNombres);
                    })
                    ->limit(3)->get();

                foreach ($productos as $prod) {
                    $results[] = [
                        'type' => 'Catálogo General',
                        'title' => $prod->nombre,
                        'subtitle' => 'Ficha maestra de producto',
                        'url' => route('inventory.index') . '?search=' . urlencode($prod->nombre),
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>',
                    ];
                }
            } catch (\Exception $e) {
            }
        }

        // 3. PROVEEDORES (requiere permiso ver.proveedores)
        if ($user->can('ver.proveedores')) {
            try {
                $proveedores = DB::table('providers')
                    ->where(function ($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', $searchTerm)
                            ->orWhere('business_line', 'LIKE', $searchTerm)
                            ->orWhere('rut', 'LIKE', $searchTerm)
                            ->orWhere('email', 'LIKE', $searchTerm);
                    })
                    ->limit(3)->get();

                foreach ($proveedores as $prov) {
                    $results[] = [
                        'type' => 'Proveedores',
                        'title' => $prov->name ?? 'Proveedor Externo',
                        'subtitle' => 'RUT: ' . ($prov->rut ?? 'S/R') . ($prov->category ? ' | ' . $prov->category : ''),
                        'url' => route('providers.index') . '?search=' . urlencode($prov->rut ?? $prov->name ?? ''),
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>',
                    ];
                }
            } catch (\Exception $e) {
            }
        }

        // 4. SOLICITUDES DE ESTABLECIMIENTOS Y CESFAM
        if ($user->can('ver.despacho') || $user->can('ver.solicitudes') || $user->can('ver.solicitudes_cesfam')) {
            try {
                $solicitudes = DB::table('establishment_requests')
                    ->where(function ($q) use ($searchTerm) {
                        $q->where('numero_solicitud', 'LIKE', $searchTerm)
                            ->orWhere('establecimiento', 'LIKE', $searchTerm)
                            ->orWhere('responsable', 'LIKE', $searchTerm);
                    })
                    ->limit(3)->get();

                foreach ($solicitudes as $sol) {
                    $esCesfam = InventoryService::esCesfam($sol->establecimiento ?? '');
                    if ($user->can('ver.despacho')) {
                        $urlSolicitud = route('dispatch.index', ['solicitud_id' => $sol->id]);
                    } elseif ($esCesfam && $user->can('ver.solicitudes_cesfam')) {
                        $urlSolicitud = route('requests.cesfam');
                    } elseif (! $esCesfam && $user->can('ver.solicitudes')) {
                        $urlSolicitud = route('requests.index');
                    } else {
                        continue;
                    }

                    $results[] = [
                        'type' => 'Solicitud (' . $sol->estado . ')',
                        'title' => $sol->numero_solicitud . ' — ' . $sol->establecimiento,
                        'subtitle' => 'Responsable: ' . ($sol->responsable ?? 'S/R') . ' | Fecha: ' . $sol->fecha_solicitud,
                        'url' => $urlSolicitud,
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>',
                    ];
                }
            } catch (\Exception $e) {
            }
        }

        return response()->json($results);
    }

    public function notifications()
    {
        $notifications = [];

        try {
            $user = auth()->user();
            if (! $user) {
                return response()->json(['count' => 0, 'data' => []]);
            }

            $puedeVerControlados = $user->hasRole('Administrador') || $user->can('ver.despacho_controlado');

            // 0. SOLICITUDES ASIGNADAS AL USUARIO LOGUEADO (O PENDIENTES DE ASIGNAR PARA ADMIN)
            if ($user->can('ver.despacho')) {
                $misSolicitudes = DB::table('establishment_requests')
                    ->where('assigned_user_id', $user->id)
                    ->whereIn('estado', ['Pendiente', 'Asignada', 'Parcial'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                foreach ($misSolicitudes as $sol) {
                    $notifications[] = [
                        'color' => 'text-teal-600',
                        'bg' => 'bg-teal-100',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>',
                        'title' => "Solicitud {$sol->numero_solicitud} asignada a ti",
                        'message' => "{$sol->establecimiento} ({$sol->estado}). Haz clic para despachar.",
                        'time' => 'Despachar',
                        'url' => route('dispatch.index', ['solicitud_id' => $sol->id]),
                    ];
                }

                if ($user->hasRole('Administrador')) {
                    $sinAsignar = DB::table('establishment_requests')
                        ->whereNull('assigned_user_id')
                        ->whereIn('estado', ['Pendiente', 'Parcial'])
                        ->orderBy('created_at', 'desc')
                        ->limit(3)
                        ->get();

                    foreach ($sinAsignar as $sol) {
                        $notifications[] = [
                            'color' => 'text-indigo-600',
                            'bg' => 'bg-indigo-100',
                            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>',
                            'title' => "Solicitud {$sol->numero_solicitud} sin asignar",
                            'message' => "{$sol->establecimiento} requiere asignación de trabajador.",
                            'time' => 'Asignar en Despacho',
                            'url' => route('dispatch.index'),
                        ];
                    }
                }
            }

            if ($user->can('ver.usuarios')) {
                // Solicitudes pendientes de recuperación de clave para Usuarios y Permisos
                $clavesPendientes = DB::table('password_reset_tokens')
                    ->where('status', 'pending')
                    ->count();

                if ($clavesPendientes > 0) {
                    $notifications[] = [
                        'color' => 'text-purple-600',
                        'bg' => 'bg-purple-100',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>',
                        'title' => 'Recuperación de Contraseña',
                        'message' => "Hay {$clavesPendientes} usuario(s) solicitando restablecer su clave.",
                        'time' => 'Gestionar Claves',
                        'url' => route('users.index'),
                    ];
                }
            }

            if ($user->can('ver.trazabilidad') || $user->can('ver.inventario')) {
                $vencimientos = DB::table('inventories')
                    ->where('estado', 'Disponible')
                    ->where('cantidad', '>', 0)
                    ->when(! $puedeVerControlados, function ($q) {
                        $q->where('categoria', '!=', 'Fármacos Controlados');
                    })
                    ->whereNotNull('fecha_vencimiento')
                    ->where('fecha_vencimiento', '<=', Carbon::now()->addDays(60))
                    ->orderBy('fecha_vencimiento', 'asc')
                    ->limit(5)
                    ->get();

                foreach ($vencimientos as $vence) {
                    $fechaVencCarbon = Carbon::parse($vence->fecha_vencimiento)->startOfDay();
                    $hoyCarbon = Carbon::now()->startOfDay();
                    $dias = (int) abs($hoyCarbon->diffInDays($fechaVencCarbon, false));
                    $estado = $fechaVencCarbon->isPast() ? 'Vencido hace' : 'Vence en';
                    $urlVenc = $user->can('ver.trazabilidad')
                        ? route('traceability.index') . '?search=' . urlencode($vence->lote)
                        : route('inventory.index') . '?search=' . urlencode($vence->lote);

                    $notifications[] = [
                        'color' => 'text-rose-600',
                        'bg' => 'bg-rose-100',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
                        'title' => 'Alerta FEFO (Vencimiento)',
                        'message' => "Lote {$vence->lote} de {$vence->producto}. {$estado} {$dias} días.",
                        'time' => 'Crítico',
                        'url' => $urlVenc,
                    ];
                }
            }

            if ($user->can('ver.cuarentena')) {
                $cuarentena = DB::table('inventories')
                    ->where('estado', 'Cuarentena')
                    ->where('cantidad', '>', 0)
                    ->limit(3)->get();

                foreach ($cuarentena as $cuar) {
                    $dias = (int) floor(Carbon::parse($cuar->updated_at ?? $cuar->created_at)->startOfDay()->diffInDays(now()->startOfDay()));

                    $mensajeCuarentena = $dias == 0 ? "Lote {$cuar->lote} fue enviado a cuarentena hoy." : "Lote {$cuar->lote} lleva {$dias} días retenido.";

                    $notifications[] = [
                        'color' => 'text-amber-600',
                        'bg' => 'bg-amber-100',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
                        'title' => 'Lote en Cuarentena',
                        'message' => $mensajeCuarentena,
                        'time' => 'Revisión',
                        'url' => route('quarantine.index'),
                    ];
                }
            }

            if ($user->can('ver.inventario')) {
                $stockBajo = DB::table('inventories')
                    ->where('estado', 'Disponible')
                    ->where('cantidad', '>', 0)
                    ->when(! $puedeVerControlados, function ($q) {
                        $q->where('categoria', '!=', 'Fármacos Controlados');
                    })
                    ->whereRaw('cantidad <= CASE WHEN minimo > 0 THEN minimo ELSE 0 END')
                    ->orderBy('cantidad', 'asc')
                    ->limit(3)
                    ->get();

                foreach ($stockBajo as $bajo) {
                    $notifications[] = [
                        'color' => 'text-orange-600',
                        'bg' => 'bg-orange-100',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                        'title' => 'Stock Bajo',
                        'message' => "{$bajo->producto} (Lote " . ($bajo->lote ?? 'S/L') . "): quedan {$bajo->cantidad} uds. (mínimo {$bajo->minimo}).",
                        'time' => 'Reponer',
                        'url' => route('inventory.index') . '?search=' . urlencode($bajo->producto),
                    ];
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'count' => count($notifications),
            'data' => collect($notifications)->take(8)->values(),
        ]);
    }
}

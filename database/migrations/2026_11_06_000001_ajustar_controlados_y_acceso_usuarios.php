<?php

use App\Services\InventoryService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 1. Marca como "Fármacos Controlados" los lotes de inventario que ya existían
     *    y que corresponden a controlados detectados por similitud de nombre
     *    (ej. "MORFINA CLORHIDRATO 10 MG/ML AMPOLLA" que estaba como "Fármacos").
     * 2. Quita el permiso "ver.usuarios" de todos los roles: el módulo de Usuarios
     *    y Roles ahora es exclusivo del rol Administrador.
     */
    public function up(): void
    {
        if (Schema::hasTable('inventories')) {
            $productos = DB::table('inventories')
                ->whereNotNull('producto')
                ->where(function ($q) {
                    $q->whereNull('categoria')->orWhere('categoria', '!=', 'Fármacos Controlados');
                })
                ->distinct()
                ->pluck('producto');

            foreach ($productos as $producto) {
                if (InventoryService::esControlado((string) $producto)) {
                    DB::table('inventories')
                        ->where('producto', $producto)
                        ->update(['categoria' => 'Fármacos Controlados']);
                }
            }
        }

        if (Schema::hasTable('permissions')) {
            $permisoId = DB::table('permissions')->where('name', 'ver.usuarios')->value('id');

            if ($permisoId) {
                DB::table('role_has_permissions')->where('permission_id', $permisoId)->delete();
                DB::table('model_has_permissions')->where('permission_id', $permisoId)->delete();
                DB::table('permissions')->where('id', $permisoId)->delete();
            }

            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }

    public function down(): void
    {
        // No se revierte: las categorías corregidas y el acceso exclusivo de administradores se mantienen.
    }
};

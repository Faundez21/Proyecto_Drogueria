<?php

use App\Services\InventoryService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // 1. marca como "farmacos controlados" los lotes de inventario que ya existian y que corresponden a controlados detectados por similitud de nombre (ej
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
        // no se revierte: las categorias corregidas y el acceso exclusivo de administradores se mantienen
    }
};

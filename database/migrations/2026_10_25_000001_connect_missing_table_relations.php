<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. conectar inventories con providers, receptions y estructura de ubicaciones (aisles, shelves, levels, positions)
        Schema::table('inventories', function (Blueprint $table) {
            $table->foreignId('provider_id')->nullable()->after('user_id')->constrained('providers')->nullOnDelete();
            $table->foreignId('reception_id')->nullable()->after('provider_id')->constrained('receptions')->nullOnDelete();
            $table->foreignId('aisle_id')->nullable()->after('estante')->constrained('aisles')->nullOnDelete();
            $table->foreignId('shelf_id')->nullable()->after('aisle_id')->constrained('shelves')->nullOnDelete();
            $table->foreignId('level_id')->nullable()->after('nivel')->constrained('levels')->nullOnDelete();
            $table->foreignId('position_id')->nullable()->after('posicion')->constrained('positions')->nullOnDelete();
        });

        // 2. conectar dispatches y controlled_dispatches con establishment_requests y users
        Schema::table('dispatches', function (Blueprint $table) {
            $table->foreignId('establishment_request_id')->nullable()->after('id')->constrained('establishment_requests')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->after('establishment_request_id')->constrained('users')->nullOnDelete();
        });

        Schema::table('controlled_dispatches', function (Blueprint $table) {
            $table->foreignId('establishment_request_id')->nullable()->after('id')->constrained('establishment_requests')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->after('establishment_request_id')->constrained('users')->nullOnDelete();
        });

        // 3. conectar dispatch_items y controlled_dispatch_items con products
        Schema::table('dispatch_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('inventory_id')->constrained('products')->nullOnDelete();
        });

        Schema::table('controlled_dispatch_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('inventory_id')->constrained('products')->nullOnDelete();
        });

        // 4. agregar columna programa a establishment_items y usuario asignado a establishment_requests
        Schema::table('establishment_items', function (Blueprint $table) {
            $table->string('programa')->nullable()->after('product_id');
        });

        Schema::table('establishment_requests', function (Blueprint $table) {
            $table->foreignId('assigned_user_id')->nullable()->after('estado')->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable()->after('assigned_user_id');
        });

        // 5. asegurar permisos de vistas (incluyendo solicitudes establecimientos y solicitudes cesfam)
        if (Schema::hasTable('permissions')) {
            foreach (['solicitudes', 'solicitudes_cesfam'] as $permKey) {
                $permName = 'ver.' . $permKey;
                if (! DB::table('permissions')->where('name', $permName)->exists()) {
                    DB::table('permissions')->insert([
                        'name' => $permName,
                        'guard_name' => 'web',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // backfill de relaciones sobre datos existentes

        // backfill: inventories -> providers (por rut o nombre)
        $providersByRut = DB::table('providers')->pluck('id', 'rut');
        $providersByName = DB::table('providers')->pluck('id', 'name');
        $receptionsByCorrelativo = DB::table('receptions')->whereNotNull('correlativo')->pluck('id', 'correlativo');
        $aislesByName = DB::table('aisles')->pluck('id', 'name');
        $shelves = DB::table('shelves')->get()->keyBy('name');

        foreach (DB::table('inventories')->get() as $inv) {
            $updates = [];

            if (! $inv->provider_id) {
                $provId = ($inv->rut_proveedor ? ($providersByRut[$inv->rut_proveedor] ?? null) : null)
                    ?? ($inv->razon_social ? ($providersByName[$inv->razon_social] ?? null) : null);
                if ($provId) {
                    $updates['provider_id'] = $provId;
                }
            }

            if (! $inv->reception_id && $inv->correlativo_interno && isset($receptionsByCorrelativo[$inv->correlativo_interno])) {
                $updates['reception_id'] = $receptionsByCorrelativo[$inv->correlativo_interno];
            }

            $shelf = ($inv->estante && isset($shelves[$inv->estante])) ? $shelves[$inv->estante] : null;
            if ($shelf) {
                $updates['shelf_id'] = $shelf->id;
                $updates['aisle_id'] = $shelf->aisle_id;

                if ($inv->nivel) {
                    $levelNum = (int) preg_replace('/\D+/', '', (string) $inv->nivel);
                    if ($levelNum > 0) {
                        $levelId = DB::table('levels')
                            ->where('shelf_id', $shelf->id)
                            ->where('number', $levelNum)
                            ->value('id');
                        if ($levelId) {
                            $updates['level_id'] = $levelId;

                            if ($inv->posicion) {
                                $posId = DB::table('positions')
                                    ->where('level_id', $levelId)
                                    ->where('name', $inv->posicion)
                                    ->value('id');
                                if ($posId) {
                                    $updates['position_id'] = $posId;
                                }
                            }
                        }
                    }
                }
            } elseif ($inv->pasillo && isset($aislesByName[$inv->pasillo])) {
                $updates['aisle_id'] = $aislesByName[$inv->pasillo];
            }

            if (! empty($updates)) {
                DB::table('inventories')->where('id', $inv->id)->update($updates);
            }
        }

        // backfill: dispatch_items y controlled_dispatch_items -> products
        $productsByName = DB::table('products')->pluck('id', 'nombre');
        $inventoriesProductMap = DB::table('inventories')->whereNotNull('product_id')->pluck('product_id', 'id');

        foreach (DB::table('dispatch_items')->whereNull('product_id')->get() as $item) {
            $prodId = ($item->inventory_id ? ($inventoriesProductMap[$item->inventory_id] ?? null) : null)
                ?? ($productsByName[$item->producto] ?? null);
            if ($prodId) {
                DB::table('dispatch_items')->where('id', $item->id)->update(['product_id' => $prodId]);
            }
        }

        foreach (DB::table('controlled_dispatch_items')->whereNull('product_id')->get() as $item) {
            $prodId = ($item->inventory_id ? ($inventoriesProductMap[$item->inventory_id] ?? null) : null)
                ?? ($productsByName[$item->producto] ?? null);
            if ($prodId) {
                DB::table('controlled_dispatch_items')->where('id', $item->id)->update(['product_id' => $prodId]);
            }
        }

        // backfill: stock_movements sin inventory_id
        foreach (DB::table('stock_movements')->whereNull('inventory_id')->get() as $mov) {
            $invId = DB::table('inventories')
                ->where('producto', $mov->producto)
                ->where(function ($q) use ($mov) {
                    $q->where('lote', $mov->lote);
                    if ($mov->lote === 'S/L') {
                        $q->orWhereNull('lote');
                    }
                })
                ->value('id');

            if ($invId) {
                DB::table('stock_movements')->where('id', $mov->id)->update(['inventory_id' => $invId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('establishment_requests', function (Blueprint $table) {
            $table->dropForeign(['assigned_user_id']);
            $table->dropColumn(['assigned_user_id', 'assigned_at']);
        });

        Schema::table('establishment_items', function (Blueprint $table) {
            $table->dropColumn('programa');
        });

        Schema::table('controlled_dispatch_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });

        Schema::table('dispatch_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });

        Schema::table('controlled_dispatches', function (Blueprint $table) {
            $table->dropForeign(['establishment_request_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['establishment_request_id', 'user_id']);
        });

        Schema::table('dispatches', function (Blueprint $table) {
            $table->dropForeign(['establishment_request_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['establishment_request_id', 'user_id']);
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['provider_id']);
            $table->dropForeign(['reception_id']);
            $table->dropForeign(['aisle_id']);
            $table->dropForeign(['shelf_id']);
            $table->dropForeign(['level_id']);
            $table->dropForeign(['position_id']);
            $table->dropColumn([
                'provider_id',
                'reception_id',
                'aisle_id',
                'shelf_id',
                'level_id',
                'position_id',
            ]);
        });
    }
};

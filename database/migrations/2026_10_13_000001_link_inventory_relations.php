<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. inventories <-> products
        Schema::table('inventories', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('id');
            $table->index('producto');
            $table->index('lote');
            $table->index('estado');
            $table->index('fecha_vencimiento');
        });

        $productos = DB::table('products')->pluck('id', 'nombre');
        foreach (DB::table('inventories')->select('id', 'producto')->get() as $inv) {
            $productId = $productos[$inv->producto] ?? null;
            if (! $productId) {
                $productId = DB::table('products')->insertGetId([
                    'nombre' => $inv->producto,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $productos[$inv->producto] = $productId;
            }
            DB::table('inventories')->where('id', $inv->id)->update(['product_id' => $productId]);
        }

        Schema::table('inventories', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });

        // 2. receptions.correlativo unico
        Schema::table('receptions', function (Blueprint $table) {
            $table->unique('correlativo');
        });

        // 3. dispatch_items / controlled_dispatch_items -> inventories
        Schema::table('dispatch_items', function (Blueprint $table) {
            $table->foreignId('inventory_id')->nullable()->after('dispatch_id');
        });
        Schema::table('dispatch_items', function (Blueprint $table) {
            $table->foreign('inventory_id')->references('id')->on('inventories')->nullOnDelete();
        });

        Schema::table('controlled_dispatch_items', function (Blueprint $table) {
            $table->foreignId('inventory_id')->nullable()->after('controlled_dispatch_id');
        });
        Schema::table('controlled_dispatch_items', function (Blueprint $table) {
            $table->foreign('inventory_id')->references('id')->on('inventories')->nullOnDelete();
        });

        // 4. stock_movements -> inventories / users + indices
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('inventory_id')->nullable()->after('id');
            $table->foreignId('user_id')->nullable()->after('inventory_id');
            $table->index(['producto', 'lote']);
            $table->index('tipo_movimiento');
        });
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreign('inventory_id')->references('id')->on('inventories')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        // 5. backfill: movimientos historicos de recepciones
        $receptions = DB::table('receptions')->get();
        foreach ($receptions as $rec) {
            $items = DB::table('reception_items')->where('reception_id', $rec->id)->get();
            foreach ($items as $item) {
                $producto = DB::table('products')->where('id', $item->product_id)->value('nombre') ?? 'Producto #' . $item->product_id;
                $inventoryId = DB::table('inventories')
                    ->where('producto', $producto)
                    ->where(function ($q) use ($item) {
                        $q->where('lote', $item->lote ?? 'S/L')
                            ->orWhereNull('lote');
                    })
                    ->value('id');

                DB::table('stock_movements')->insert([
                    'inventory_id' => $inventoryId,
                    'user_id' => null,
                    'producto' => $producto,
                    'lote' => $item->lote ?? 'S/L',
                    'fecha_vencimiento' => $item->fecha_vencimiento,
                    'tipo_movimiento' => 'Recepción',
                    'cantidad' => $item->cantidad,
                    'fecha_hora' => $rec->created_at ?? now(),
                    'origen_destino' => 'Desde: ' . (DB::table('providers')->where('id', $rec->provider_id)->value('name') ?? 'Proveedor'),
                    'documento_respaldo' => ($rec->tipo_documento ?? 'Doc') . ' ' . ($rec->numero_documento ?? 'S/N'),
                    'estado_lote' => 'Cuarentena',
                    'created_at' => $rec->created_at ?? now(),
                    'updated_at' => $rec->created_at ?? now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['inventory_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['inventory_id', 'user_id']);
            $table->dropIndex(['producto', 'lote']);
            $table->dropIndex(['tipo_movimiento']);
        });

        Schema::table('controlled_dispatch_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_id']);
            $table->dropColumn('inventory_id');
        });

        Schema::table('dispatch_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_id']);
            $table->dropColumn('inventory_id');
        });

        Schema::table('receptions', function (Blueprint $table) {
            $table->dropUnique(['correlativo']);
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
            $table->dropIndex(['producto']);
            $table->dropIndex(['lote']);
            $table->dropIndex(['estado']);
            $table->dropIndex(['fecha_vencimiento']);
        });
    }
};

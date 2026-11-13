<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // run the migrations
    public function up(): void
    {
        // 1. ampliar products.nombre a 1000 caracteres
        DB::statement("ALTER TABLE `products` MODIFY `nombre` VARCHAR(1000) NOT NULL");

        // 2. ajustar indice de inventories.producto a prefijo de 255 caracteres para permitir varchar(1000) en utf8mb4
        $hasInvIndex = collect(DB::select("SHOW INDEX FROM `inventories` WHERE Key_name = 'inventories_producto_index'"))->isNotEmpty();
        if ($hasInvIndex) {
            DB::statement("ALTER TABLE `inventories` DROP INDEX `inventories_producto_index`");
        }

        DB::statement("ALTER TABLE `inventories`
            MODIFY `producto` VARCHAR(1000) NOT NULL,
            MODIFY `categoria` VARCHAR(500) NOT NULL,
            MODIFY `programa` VARCHAR(500) NULL,
            MODIFY `razon_social` VARCHAR(500) NULL,
            MODIFY `origen_mercaderia` VARCHAR(500) NULL,
            ADD INDEX `inventories_producto_index` (`producto`(255))
        ");

        // 3. ajustar indice compuesto en stock_movements para permitir varchar(1000) en producto
        $hasMovIndex = collect(DB::select("SHOW INDEX FROM `stock_movements` WHERE Key_name = 'stock_movements_producto_lote_index'"))->isNotEmpty();
        if ($hasMovIndex) {
            DB::statement("ALTER TABLE `stock_movements` DROP INDEX `stock_movements_producto_lote_index`");
        }

        DB::statement("ALTER TABLE `stock_movements`
            MODIFY `producto` VARCHAR(1000) NOT NULL,
            MODIFY `origen_destino` VARCHAR(500) NOT NULL,
            MODIFY `documento_respaldo` VARCHAR(500) NOT NULL,
            ADD INDEX `stock_movements_producto_lote_index` (`producto`(255), `lote`)
        ");

        // 4. ampliar tablas de despachos y proveedores
        DB::statement("ALTER TABLE `dispatch_items`
            MODIFY `producto` VARCHAR(1000) NOT NULL,
            MODIFY `programa` VARCHAR(500) NULL
        ");

        DB::statement("ALTER TABLE `controlled_dispatch_items`
            MODIFY `producto` VARCHAR(1000) NOT NULL,
            MODIFY `programa` VARCHAR(500) NULL
        ");

        if (Schema::hasTable('providers')) {
            DB::statement("ALTER TABLE `providers` MODIFY `name` VARCHAR(500) NOT NULL");
        }
    }

    // reverse the migrations
    public function down(): void
    {
        DB::statement("ALTER TABLE `products` MODIFY `nombre` VARCHAR(255) NOT NULL");

        $hasInvIndex = collect(DB::select("SHOW INDEX FROM `inventories` WHERE Key_name = 'inventories_producto_index'"))->isNotEmpty();
        if ($hasInvIndex) {
            DB::statement("ALTER TABLE `inventories` DROP INDEX `inventories_producto_index`");
        }

        DB::statement("ALTER TABLE `inventories`
            MODIFY `producto` VARCHAR(255) NOT NULL,
            MODIFY `categoria` VARCHAR(255) NOT NULL,
            MODIFY `programa` VARCHAR(255) NULL,
            MODIFY `razon_social` VARCHAR(255) NULL,
            MODIFY `origen_mercaderia` VARCHAR(255) NULL,
            ADD INDEX `inventories_producto_index` (`producto`)
        ");

        $hasMovIndex = collect(DB::select("SHOW INDEX FROM `stock_movements` WHERE Key_name = 'stock_movements_producto_lote_index'"))->isNotEmpty();
        if ($hasMovIndex) {
            DB::statement("ALTER TABLE `stock_movements` DROP INDEX `stock_movements_producto_lote_index`");
        }

        DB::statement("ALTER TABLE `stock_movements`
            MODIFY `producto` VARCHAR(255) NOT NULL,
            MODIFY `origen_destino` VARCHAR(255) NOT NULL,
            MODIFY `documento_respaldo` VARCHAR(255) NOT NULL,
            ADD INDEX `stock_movements_producto_lote_index` (`producto`, `lote`)
        ");

        DB::statement("ALTER TABLE `dispatch_items`
            MODIFY `producto` VARCHAR(255) NOT NULL,
            MODIFY `programa` VARCHAR(255) NULL
        ");

        DB::statement("ALTER TABLE `controlled_dispatch_items`
            MODIFY `producto` VARCHAR(255) NOT NULL,
            MODIFY `programa` VARCHAR(255) NULL
        ");

        if (Schema::hasTable('providers')) {
            DB::statement("ALTER TABLE `providers` MODIFY `name` VARCHAR(255) NOT NULL");
        }
    }
};

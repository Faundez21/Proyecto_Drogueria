<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->string('programa')->nullable()->after('estante');
            $table->string('nivel', 100)->nullable()->after('programa');
            $table->string('posicion', 100)->nullable()->after('nivel');
            $table->boolean('solicitud_pedido_mensual')->default(false)->after('posicion');
            $table->boolean('solicitud_stock_critico')->default(false)->after('solicitud_pedido_mensual');
            $table->boolean('adelanto_de_pedido')->default(false)->after('solicitud_stock_critico');
            $table->boolean('traspaso')->default(false)->after('adelanto_de_pedido');
            $table->index('programa');
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex(['programa']);
            $table->dropColumn([
                'programa',
                'nivel',
                'posicion',
                'solicitud_pedido_mensual',
                'solicitud_stock_critico',
                'adelanto_de_pedido',
                'traspaso',
            ]);
        });
    }
};

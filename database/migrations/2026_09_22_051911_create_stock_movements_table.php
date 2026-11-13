<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('producto');
            $table->string('lote');
            $table->date('fecha_vencimiento')->nullable();
            $table->string('tipo_movimiento'); // 'recepcion', 'despacho', 'ajuste'
            $table->integer('cantidad'); // positiva o negativa
            $table->dateTime('fecha_hora');
            $table->string('origen_destino'); // ej: 'laboratorio x' o 'cesfam y'
            $table->string('documento_respaldo'); // ej: 'factura 123' o 'gd-2026-001'
            $table->string('estado_lote')->default('Vigente'); // vigente, cuarentena, vencido
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movements');
    }
};
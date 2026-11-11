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
            $table->string('tipo_movimiento'); // 'Recepción', 'Despacho', 'Ajuste'
            $table->integer('cantidad'); // Positiva o negativa
            $table->dateTime('fecha_hora');
            $table->string('origen_destino'); // Ej: 'Laboratorio X' o 'CESFAM Y'
            $table->string('documento_respaldo'); // Ej: 'Factura 123' o 'GD-2026-001'
            $table->string('estado_lote')->default('Vigente'); // Vigente, Cuarentena, Vencido
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movements');
    }
};
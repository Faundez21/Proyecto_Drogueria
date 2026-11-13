<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // tabla cabecera del despacho
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->string('correlativo')->unique(); // ej: gd-2026-0001
            $table->date('fecha_despacho');
            $table->string('clasificacion')->default('Interno'); // interno o externo
            $table->string('estado_liberacion');
            $table->string('establecimiento_destino');
            $table->string('direccion_destino')->nullable();
            $table->string('condiciones_transporte')->nullable();
            $table->longText('firma_despacha')->nullable(); // firma base64
            $table->longText('firma_recibe')->nullable(); // firma base64
            $table->timestamps();
        });

        // tabla detalle (los productos que van en el camion)
        Schema::create('dispatch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_id')->constrained('dispatches')->onDelete('cascade');
            $table->string('producto'); // guardamos el nombre para historial estatico
            $table->string('lote')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->string('programa');
            $table->integer('cantidad');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dispatch_items');
        Schema::dropIfExists('dispatches');
    }
};
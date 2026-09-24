<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabla Cabecera del Despacho
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->string('correlativo')->unique(); // Ej: GD-2026-0001
            $table->date('fecha_despacho');
            $table->string('clasificacion')->default('Interno'); // NUEVO: Interno o Externo
            $table->string('estado_liberacion');
            $table->string('establecimiento_destino');
            $table->string('direccion_destino')->nullable();
            $table->string('condiciones_transporte')->nullable();
            $table->longText('firma_despacha')->nullable(); // NUEVO: Firma base64
            $table->longText('firma_recibe')->nullable(); // NUEVO: Firma base64
            $table->timestamps();
        });

        // Tabla Detalle (Los productos que van en el camión)
        Schema::create('dispatch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_id')->constrained('dispatches')->onDelete('cascade');
            $table->string('producto'); // Guardamos el nombre para historial estático
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
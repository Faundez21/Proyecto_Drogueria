<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // tabla cabecera del despacho de controlados
        Schema::create('controlled_dispatches', function (Blueprint $table) {
            $table->id();
            $table->string('correlativo')->unique(); // ej: gdc-2026-0001
            $table->date('fecha_despacho');
            $table->string('clasificacion')->default('Interno');
            $table->string('estado_liberacion');
            $table->string('establecimiento_destino');
            $table->string('direccion_destino')->nullable();
            $table->string('condiciones_transporte')->nullable();
            
            // campo especial para recetas cheque
            $table->text('series_recetas')->nullable(); 

            $table->longText('firma_despacha')->nullable();
            $table->longText('firma_recibe')->nullable();
            $table->timestamps();
        });

        // tabla detalle de los productos controlados despachados
        Schema::create('controlled_dispatch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('controlled_dispatch_id')->constrained('controlled_dispatches')->onDelete('cascade');
            $table->string('producto'); 
            $table->string('lote')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->string('programa');
            $table->integer('cantidad');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('controlled_dispatch_items');
        Schema::dropIfExists('controlled_dispatches');
    }
};
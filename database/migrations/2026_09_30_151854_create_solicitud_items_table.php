<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('solicitud_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('solicitud_id')
                ->constrained('solicitudes')
                ->onDelete('cascade');

            $table->foreignId('program_id')
                ->constrained('programs')
                ->onDelete('restrict');

            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('restrict');

            $table->integer('consumo_anterior')->nullable();
            $table->integer('stock_actual')->nullable();
            $table->integer('cantidad_solicitada');
            $table->integer('cantidad_despachada')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_items');
    }
};

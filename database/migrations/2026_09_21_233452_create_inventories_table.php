<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();

            // Datos del Documento y Origen
            $table->integer('correlativo_interno')->nullable();
            $table->date('fecha_recepcion');
            $table->string('tipo_documento');
            $table->string('numero_documento');
            $table->string('orden_compra')->nullable();
            $table->string('origen_mercaderia');

            // Datos del Proveedor
            $table->string('rut_proveedor');
            $table->string('razon_social');

            // Detalle del Producto
            $table->string('producto');
            $table->string('categoria');
            $table->string('lote')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->integer('cantidad');
            $table->integer('minimo')->default(0);
            $table->decimal('valor_neto_unitario', 15, 2)->default(0);

            // Estado del Lote (Disponible, Cuarentena, Merma)
            $table->string('estado')->default('Disponible');
            $table->text('motivo')->nullable();

            // Ubicación física
            $table->string('pasillo')->nullable();
            $table->string('estante')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};

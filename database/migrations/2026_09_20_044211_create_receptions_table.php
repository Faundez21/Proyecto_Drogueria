<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // tabla de productos
        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->timestamps();
            });
        }

        //  tabla de recepciones
        if (! Schema::hasTable('receptions')) {
            Schema::create('receptions', function (Blueprint $table) {
                $table->id();
                $table->integer('correlativo')->nullable();
                $table->date('fecha_recepcion');
                $table->string('tipo_documento');
                $table->string('numero_documento');
                $table->string('orden_compra')->nullable();
                $table->string('origen');

                $table->foreignId('provider_id')->constrained('providers')->onDelete('restrict');

                $table->decimal('valor_total_recepcion', 15, 2)->default(0);
                $table->longText('firma_proveedor')->nullable();
                $table->longText('firma_receptor')->nullable();

                $table->timestamps();
            });
        }

        // tabla de productos
        if (! Schema::hasTable('reception_items')) {
            Schema::create('reception_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reception_id')->constrained('receptions')->onDelete('cascade');

                $table->foreignId('product_id')->constrained('products')->onDelete('restrict');

                $table->string('tipo_producto');
                $table->string('lote')->nullable();
                $table->date('fecha_vencimiento')->nullable();
                $table->integer('cantidad');
                $table->decimal('valor_unitario', 15, 2)->nullable();
                $table->decimal('valor_total_item', 15, 2)->nullable();

                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('reception_items');
        Schema::dropIfExists('receptions');
        Schema::dropIfExists('products');
    }
};

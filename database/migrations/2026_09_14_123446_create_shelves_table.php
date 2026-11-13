<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // run the migrations
    public function up()
    {
        Schema::create('shelves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aisle_id')->constrained('aisles')->onDelete('cascade');
            $table->string('name');
            $table->string('family_category')->nullable();
            $table->text('description')->nullable();

            // coordenadas para el visor 3d
            $table->float('pos_x')->default(0);
            $table->float('pos_z')->default(0);
            $table->float('rotation')->default(0);

            $table->timestamps();
        });
    }

    // reverse the migrations
    public function down(): void
    {
        Schema::dropIfExists('shelves');
    }
};

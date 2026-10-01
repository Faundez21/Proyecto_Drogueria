<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('product_program');
        Schema::dropIfExists('programs');
    }

    public function down(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('product_program', function (Blueprint $table) {
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            $table->foreignId('program_id')
                ->constrained('programs')
                ->onDelete('cascade');

            $table->primary(['product_id', 'program_id']);
        });
    }
};

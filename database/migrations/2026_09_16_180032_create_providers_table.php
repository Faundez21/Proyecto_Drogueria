<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('rut')->unique();
            $table->string('name'); // razon social
            $table->string('business_line')->nullable(); // giro comercial
            $table->string('category');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('address')->nullable(); // direccion
            $table->enum('status', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
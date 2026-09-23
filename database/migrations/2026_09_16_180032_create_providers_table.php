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
            $table->string('name'); // Razón Social
            $table->string('business_line')->nullable(); // Giro Comercial
            $table->string('category');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('address')->nullable(); // Dirección
            $table->string('bank_name')->nullable(); // Banco
            $table->string('account_type')->nullable(); // Tipo de Cuenta
            $table->string('account_number')->nullable(); // Número de Cuenta
            $table->enum('status', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
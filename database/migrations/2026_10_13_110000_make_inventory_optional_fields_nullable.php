<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->string('origen_mercaderia')->nullable()->change();
            $table->string('rut_proveedor')->nullable()->change();
            $table->string('razon_social')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->string('origen_mercaderia')->nullable(false)->change();
            $table->string('rut_proveedor')->nullable(false)->change();
            $table->string('razon_social')->nullable(false)->change();
        });
    }
};

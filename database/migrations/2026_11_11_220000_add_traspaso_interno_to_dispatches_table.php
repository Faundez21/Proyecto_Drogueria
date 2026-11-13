<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// traspasos internos: despachos a la red sin solicitud previa
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->string('motivo_traspaso', 500)->nullable()->after('condiciones_transporte');
            $table->string('estado_recepcion', 20)->default('Recibido')->after('firma_recibe');
            $table->timestamp('fecha_recepcion')->nullable()->after('estado_recepcion');
        });

        // las guias existentes ya se emitieron con ambas firmas
        DB::table('dispatches')->whereNull('fecha_recepcion')->update(['fecha_recepcion' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('dispatches', function (Blueprint $table) {
            $table->dropColumn(['motivo_traspaso', 'estado_recepcion', 'fecha_recepcion']);
        });
    }
};

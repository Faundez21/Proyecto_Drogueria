<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // run the migrations
    public function up(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->string('establecimiento');
            $table->date('fecha_solicitud');
            $table->string('numero_solicitud');
            $table->string('responsable');
            $table->string('cargo');
            $table->text('observaciones')->nullable();
            $table->text('firma_solicitante')->nullable();
            $table->string('estado')->default('Pendiente');
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropColumn([
                'establecimiento',
                'fecha_solicitud',
                'numero_solicitud',
                'responsable',
                'cargo',
                'observaciones',
                'firma_solicitante',
                'estado',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('solicitud_items', function (Blueprint $table) {
            $table->dropForeign(['solicitud_id']);
            $table->dropForeign(['program_id']);
        });

        Schema::rename('solicitud_items', 'establishment_items');

        Schema::table('establishment_items', function (Blueprint $table) {
            $table->renameColumn('solicitud_id', 'establishment_request_id');
            $table->dropColumn('program_id');

            $table->foreign('establishment_request_id')
                ->references('id')
                ->on('establishment_requests')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('establishment_items', function (Blueprint $table) {
            $table->dropForeign(['establishment_request_id']);
            $table->renameColumn('establishment_request_id', 'solicitud_id');
            $table->unsignedBigInteger('program_id')->nullable();
        });

        Schema::rename('establishment_items', 'solicitud_items');

        Schema::table('solicitud_items', function (Blueprint $table) {
            $table->foreign('solicitud_id')
                ->references('id')
                ->on('solicitudes')
                ->onDelete('cascade');

            $table->foreign('program_id')
                ->references('id')
                ->on('programs')
                ->onDelete('restrict');
        });
    }
};

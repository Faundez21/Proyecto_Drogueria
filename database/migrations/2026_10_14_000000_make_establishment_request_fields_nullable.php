<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('establishment_requests', function (Blueprint $table) {
            $table->string('cargo')->nullable()->change();
            $table->text('observaciones')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('establishment_requests', function (Blueprint $table) {
            $table->string('cargo')->nullable(false)->change();
            $table->text('observaciones')->nullable(false)->change();
        });
    }
};

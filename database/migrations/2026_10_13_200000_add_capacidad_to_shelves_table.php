<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shelves', function (Blueprint $table) {
            $table->unsignedInteger('capacidad')->default(0)->after('rotation');
        });
    }

    public function down(): void
    {
        Schema::table('shelves', function (Blueprint $table) {
            $table->dropColumn('capacidad');
        });
    }
};

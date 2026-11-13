<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // run the migrations
    public function up(): void
    {
        Schema::table('aisles', function (Blueprint $table) {
            $table->string('color', 7)->default('#3B82F6');

        });
    }

    // reverse the migrations
    public function down(): void
    {
        Schema::table('aisles', function (Blueprint $table) {
            $table->dropColumn('color');

        });
    }
};

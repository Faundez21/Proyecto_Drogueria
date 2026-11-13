<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // run the migrations
    public function up()
    {
        Schema::rename('solicituds', 'solicitudes');
    }

    public function down()
    {
        Schema::rename('solicitudes', 'solicituds');
    }
};

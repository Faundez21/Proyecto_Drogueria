<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // cesfam y nivel comunal pasan a ser externo, prestamos y donaciones pasan a ser interno
    public function up(): void
    {
        $this->intercambiar();
    }

    public function down(): void
    {
        $this->intercambiar();
    }

    private function intercambiar(): void
    {
        foreach (['dispatches', 'controlled_dispatches'] as $tabla) {
            DB::table($tabla)->update([
                'clasificacion' => DB::raw("CASE clasificacion WHEN 'Interno' THEN 'Externo' WHEN 'Externo' THEN 'Interno' ELSE clasificacion END"),
            ]);
        }
    }
};

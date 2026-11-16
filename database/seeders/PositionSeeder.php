<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    // posiciones por nivel, de izquierda a derecha
    public const POSICIONES_POR_NIVEL = 4;

    // crea las posiciones de cada nivel que todavia no tiene ninguna; los niveles que ya tienen posiciones no se tocan
    public function run(): void
    {
        foreach (Level::with('shelf')->withCount('positions')->orderBy('shelf_id')->orderBy('number')->get() as $nivel) {
            if ($nivel->positions_count > 0) {
                continue;
            }

            for ($numero = 1; $numero <= self::POSICIONES_POR_NIVEL; $numero++) {
                Position::firstOrCreate(
                    ['level_id' => $nivel->id, 'name' => (string) $numero],
                    ['description' => "Posición {$numero} del nivel {$nivel->number}, estantería " . ($nivel->shelf->name ?? '') . '.']
                );
            }
        }
    }
}

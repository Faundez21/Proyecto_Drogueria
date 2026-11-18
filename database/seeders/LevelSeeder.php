<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\Shelf;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    // niveles por estanteria (nivel 1 es el de abajo)
    public const NIVELES_POR_ESTANTE = 4;

    // crea los niveles de cada estanteria que todavia no tiene ninguno; las que ya tienen niveles no se tocan
    public function run(): void
    {
        foreach (Shelf::withCount('levels')->orderBy('name')->get() as $estante) {
            if ($estante->levels_count > 0) {
                continue;
            }

            for ($numero = 1; $numero <= self::NIVELES_POR_ESTANTE; $numero++) {
                Level::firstOrCreate(
                    ['shelf_id' => $estante->id, 'number' => $numero],
                    ['description' => "Nivel {$numero} de la estantería {$estante->name}."]
                );
            }
        }
    }
}

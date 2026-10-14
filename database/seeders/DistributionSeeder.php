<?php

namespace Database\Seeders;

use App\Models\Aisle;
use App\Models\Position;
use App\Models\Level;
use App\Models\Shelf;
use Illuminate\Database\Seeder;

class DistributionSeeder extends Seeder
{
    public function run(): void
    {
        $estructura = [
            'Pasillo A' => [
                'color' => '#f97316',
                'estanterias' => [
                    ['name' => 'A-1', 'x' => -28, 'z' => -8, 'capacidad' => 1500],
                    ['name' => 'A-2', 'x' => -28, 'z' => 2, 'capacidad' => 1500],
                    ['name' => 'ESTANTERIA 1', 'x' => -28, 'z' => 12, 'capacidad' => 800],
                ],
            ],
            'Pasillo B' => [
                'color' => '#3b82f6',
                'estanterias' => [
                    ['name' => 'B-1', 'x' => -14, 'z' => -8, 'capacidad' => 1200],
                ],
            ],
            'Pasillo C' => [
                'color' => '#22c55e',
                'estanterias' => [
                    ['name' => 'C-3', 'x' => -14, 'z' => 2, 'capacidad' => 3000],
                    ['name' => 'M-1', 'x' => -14, 'z' => 12, 'capacidad' => 1000],
                ],
            ],
            'Zona Controlados' => [
                'color' => '#a855f7',
                'estanterias' => [
                    ['name' => 'CAJA FUERTE 1', 'x' => 26, 'z' => -8, 'capacidad' => 600],
                    ['name' => 'CAJA FUERTE 2', 'x' => 26, 'z' => 2, 'capacidad' => 600],
                    ['name' => 'CAJA FUERTE 3', 'x' => 26, 'z' => 12, 'capacidad' => 600],
                ],
            ],
        ];

        foreach ($estructura as $pasilloNombre => $datos) {
            $aisle = Aisle::firstOrCreate(
                ['name' => $pasilloNombre],
                [
                    'description' => 'Pasillo generado automáticamente para la distribución física',
                    'color' => $datos['color'],
                ]
            );

            foreach ($datos['estanterias'] as $estanteria) {
                $shelf = Shelf::firstOrCreate(
                    ['name' => $estanteria['name']],
                    [
                        'aisle_id' => $aisle->id,
                        'family_category' => $pasilloNombre,
                        'pos_x' => $estanteria['x'],
                        'pos_z' => $estanteria['z'],
                        'capacidad' => $estanteria['capacidad'],
                    ]
                );

                for ($nivel = 1; $nivel <= 3; $nivel++) {
                    $level = Level::firstOrCreate([
                        'shelf_id' => $shelf->id,
                        'number' => $nivel,
                    ], [
                        'description' => 'Nivel ' . $nivel . ' de ' . $shelf->name,
                    ]);

                    foreach (['P1', 'P2', 'P3'] as $posicion) {
                        Position::firstOrCreate([
                            'level_id' => $level->id,
                            'name' => $posicion,
                        ], [
                            'description' => 'Posición ' . $posicion . ' del nivel ' . $nivel,
                        ]);
                    }
                }
            }
        }
    }
}

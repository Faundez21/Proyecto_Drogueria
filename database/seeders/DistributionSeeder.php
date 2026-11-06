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
        // Estructura de bodega alineada con las ubicaciones del inventario inicial (InventorySeeder):
        // Pasillo 1 (A-1, A-2), Pasillo 2 (B-1), Pasillo 3 de controlados (cajas fuertes),
        // Pasillo 4 (C-3) y la zona de merma (M-1).
        $estructura = [
            'Pasillo 1' => [
                'color' => '#f97316',
                'descripcion' => 'Medicamentos de alta rotación',
                'estanterias' => [
                    ['name' => 'A-1', 'x' => -28, 'z' => -8, 'capacidad' => 1500],
                    ['name' => 'A-2', 'x' => -28, 'z' => 2, 'capacidad' => 1500],
                    ['name' => 'A-3', 'x' => -28, 'z' => 12, 'capacidad' => 1500],
                ],
            ],
            'Pasillo 2' => [
                'color' => '#3b82f6',
                'descripcion' => 'Insumos clínicos',
                'estanterias' => [
                    ['name' => 'B-1', 'x' => -14, 'z' => -8, 'capacidad' => 1200],
                    ['name' => 'B-2', 'x' => -14, 'z' => 2, 'capacidad' => 1200],
                    ['name' => 'B-3', 'x' => -14, 'z' => 12, 'capacidad' => 1200],
                ],
            ],
            'Pasillo 3 (Controlados)' => [
                'color' => '#a855f7',
                'descripcion' => 'Estupefacientes y psicotrópicos bajo llave',
                'estanterias' => [
                    ['name' => 'CAJA FUERTE 1', 'x' => 26, 'z' => -8, 'capacidad' => 600],
                    ['name' => 'CAJA FUERTE 2', 'x' => 26, 'z' => 2, 'capacidad' => 600],
                    ['name' => 'CAJA FUERTE 3', 'x' => 26, 'z' => 12, 'capacidad' => 600],
                ],
            ],
            'Pasillo 4' => [
                'color' => '#22c55e',
                'descripcion' => 'Medicamentos de baja rotación e insumos odontológicos',
                'estanterias' => [
                    ['name' => 'C-1', 'x' => 0, 'z' => -8, 'capacidad' => 3000],
                    ['name' => 'C-2', 'x' => 0, 'z' => 2, 'capacidad' => 3000],
                    ['name' => 'C-3', 'x' => 0, 'z' => 12, 'capacidad' => 3000],
                ],
            ],
            'Pasillo Merma' => [
                'color' => '#ef4444',
                'descripcion' => 'Productos dados de baja a la espera de destrucción',
                'estanterias' => [
                    ['name' => 'M-1', 'x' => 12, 'z' => 12, 'capacidad' => 1000],
                ],
            ],
        ];

        foreach ($estructura as $pasilloNombre => $datos) {
            $aisle = Aisle::firstOrCreate(
                ['name' => $pasilloNombre],
                [
                    'description' => $datos['descripcion'],
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

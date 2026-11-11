<?php

namespace Database\Seeders;

use App\Models\Aisle;
use App\Models\Shelf;
use Illuminate\Database\Seeder;

class DistributionSeeder extends Seeder
{
    /**
     * Estructura base de la bodega: 4 pasillos con 4 estanterías cada uno.
     *
     * Es la planta mínima para que la Droguería tenga dónde ubicar los lotes
     * desde el primer día. Los niveles y posiciones de cada estantería se
     * crean después desde el módulo de Distribución, según cómo quede
     * armada físicamente la bodega.
     *
     * El seeder es idempotente: se puede volver a ejecutar sin duplicar
     * pasillos ni estanterías, y respeta las coordenadas que un usuario haya
     * ajustado a mano en el mapa 3D.
     */
    public function run(): void
    {
        /*
        | Cada pasillo define su nombre, color de identificación en el mapa 3D
        | y la coordenada Z donde se dibuja su fila de estanterías.
        */
        $pasillos = [
            ['name' => 'Pasillo 1', 'color' => '#0D9488', 'descripcion' => 'Pasillo 1 de la bodega central.', 'z' => -16],
            ['name' => 'Pasillo 2', 'color' => '#D97706', 'descripcion' => 'Pasillo 2 de la bodega central.', 'z' => -6],
            ['name' => 'Pasillo 3', 'color' => '#4F46E5', 'descripcion' => 'Pasillo 3 de la bodega central.', 'z' => 4],
            ['name' => 'Pasillo 4', 'color' => '#BE123C', 'descripcion' => 'Pasillo 4 de la bodega central.', 'z' => 14],
        ];

        // Coordenada X de cada una de las 4 estanterías dentro del pasillo.
        $columnasX = [-27, -9, 9, 27];

        // Sufijo de la estantería: Pasillo 1 => P1-A, P1-B, P1-C, P1-D.
        $letras = ['A', 'B', 'C', 'D'];

        foreach ($pasillos as $indicePasillo => $datosPasillo) {
            $pasillo = Aisle::firstOrCreate(
                ['name' => $datosPasillo['name']],
                [
                    'description' => $datosPasillo['descripcion'],
                    'color' => $datosPasillo['color'],
                ]
            );

            foreach ($letras as $indiceEstante => $letra) {
                // El nombre de la estantería es único en todo el sistema,
                // por eso lleva el número del pasillo adelante.
                $nombreEstante = 'P' . ($indicePasillo + 1) . '-' . $letra;

                Shelf::firstOrCreate(
                    ['name' => $nombreEstante],
                    [
                        'aisle_id' => $pasillo->id,
                        'description' => 'Estantería ' . $letra . ' del ' . $datosPasillo['name'] . '.',
                        'capacidad' => 0,
                        'pos_x' => $columnasX[$indiceEstante],
                        'pos_z' => $datosPasillo['z'],
                        'rotation' => 0,
                    ]
                );
            }
        }
    }
}

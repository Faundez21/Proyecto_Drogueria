<?php

namespace Database\Seeders;

use App\Models\Aisle;
use App\Models\Shelf;
use Illuminate\Database\Seeder;

class DistributionSeeder extends Seeder
{
    // estructura base de la bodega: 4 pasillos con 4 estanterias cada uno
    public function run(): void
    {
        // | cada pasillo define su nombre, color de identificacion en el mapa 3d | y la coordenada z donde se dibuja su fila de estanterias
        $pasillos = [
            ['name' => 'Pasillo 1', 'color' => '#0D9488', 'descripcion' => 'Pasillo 1 de la bodega central.', 'z' => -16],
            ['name' => 'Pasillo 2', 'color' => '#D97706', 'descripcion' => 'Pasillo 2 de la bodega central.', 'z' => -6],
            ['name' => 'Pasillo 3', 'color' => '#4F46E5', 'descripcion' => 'Pasillo 3 de la bodega central.', 'z' => 4],
            ['name' => 'Pasillo 4', 'color' => '#BE123C', 'descripcion' => 'Pasillo 4 de la bodega central.', 'z' => 14],
        ];

        // coordenada x de cada una de las 4 estanterias dentro del pasillo
        $columnasX = [-27, -9, 9, 27];

        // sufijo de la estanteria: pasillo 1 => p1-a, p1-b, p1-c, p1-d
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
                // el nombre de la estanteria es unico en todo el sistema, por eso lleva el numero del pasillo adelante
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

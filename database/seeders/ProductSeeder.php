<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product; // Asegúrate de que tu modelo se llame así

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            ['nombre' => 'Paracetamol 500mg Comprimidos'],
            ['nombre' => 'Jeringa 5ml'],
            ['nombre' => 'Desfibrilador Externo Automático'],
            ['nombre' => 'Ibuprofeno 400mg'],
            ['nombre' => 'Gasa Estéril 10x10cm'],
        ];

        foreach ($products as $item) {
            Product::firstOrCreate([
                'nombre' => $item['nombre']
            ]);
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Datos mínimos para que la plataforma funcione:
     * 1. Roles y permisos por vista.
     * 2. Un usuario Administrador para poder entrar al sistema.
     * 3. La planta base de la bodega: 4 pasillos con 4 estanterías cada uno.
     *
     * Proveedores, productos e inventario se cargan desde la propia
     * plataforma (o con la Carga Masiva de Inventario).
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(DistributionSeeder::class);
    }
}

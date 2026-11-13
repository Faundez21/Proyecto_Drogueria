<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // datos minimos: roles y permisos, un administrador y la planta base de la bodega
    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ProviderSeeder::class);
        $this->call(DistributionSeeder::class);
    }
}

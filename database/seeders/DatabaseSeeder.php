<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Primero se crean roles, usuarios y proveedores
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ProviderSeeder::class);

        // 2. Luego se crean los productos
        $this->call(ProductSeeder::class);
        // inventario
        $this->call([ InventorySeeder::class]);
        // 3. Finalmente la recepción (que depende de proveedor y producto)
        $this->call(ReceptionSeeder::class);

    }
}

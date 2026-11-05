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

        // 2. Luego se crean los productos y la distribución física (pasillos y estanterías)
        $this->call(ProductSeeder::class);
        $this->call([DistributionSeeder::class]);

        // 3. Inventario (enlazado a productos, proveedores y ubicaciones)
        $this->call([InventorySeeder::class]);

        // 4. Finalmente la recepción (que depende de proveedor y producto)
        $this->call(ReceptionSeeder::class);

    }
}

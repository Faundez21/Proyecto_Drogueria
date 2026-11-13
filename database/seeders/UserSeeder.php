<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    // crea el usuario administrador inicial, indispensable para entrar a la plataforma. despues del primer ingreso conviene cambiarle la contrasena
    public function run(): void
    {
        $admin = User::updateOrCreate(
            [
                'email' => 'pabloadmin@drogueriadas.com',
            ],
            [
                'name' => 'Pablo',
                'last_name' => 'Cáceres',
                'password' => Hash::make('12345678'),
                'status' => 'activo',
            ]
        );

        $admin->assignRole('Administrador');
    }
}

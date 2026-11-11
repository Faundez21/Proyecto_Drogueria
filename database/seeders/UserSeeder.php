<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Crea el usuario Administrador inicial, indispensable para entrar a la plataforma.
     * Después del primer ingreso conviene cambiarle la contraseña.
     */
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

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user1 = User::updateOrCreate(
            [
                'email' => 'pabloadmin@drogueriadas.com',
            ],
            [
                'name' => 'Pablo',
                'last_name' => 'Cáceres',
                'password' => Hash::make('12345678'),
            ]
        );
$user2 = User::updateOrCreate(
            [
                'email' => 'usuario@drogueriadas.com',
            ],
            [
                'name' => 'Juan',
                'last_name' => 'Perez',
                'password' => Hash::make('12345678'),
            ]
        );
        $user1->assignRole('Administrador');
        $user2->assignRole('Usuario');
    }
}

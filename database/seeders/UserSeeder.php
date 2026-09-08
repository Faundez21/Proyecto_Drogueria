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
                'name' => 'Pablo Cáceres',
                'password' => Hash::make('12345678'),
            ]
        );

        $user1->assignRole('Administrador');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'name' => 'Administrador Sistema',
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'role' => 'admin'
            ],
            [
                'name' => 'Mesero Turno 1',
                'username' => 'mesero1',
                'password' => Hash::make('1234'),
                'role' => 'mesero'
            ],
            [
                'name' => 'Cocinero Principal',
                'username' => 'cocina1',
                'password' => Hash::make('1234'),
                'role' => 'cocina'
            ],
            [
                'name' => 'Cajera Central',
                'username' => 'cajera1',
                'password' => Hash::make('1234'),
                'role' => 'cajera'
            ],
        ];

        foreach ($usuarios as $u) {
            User::create($u);
        }
    }
}

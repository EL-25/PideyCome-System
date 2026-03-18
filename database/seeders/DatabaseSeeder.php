<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Esta es la única línea que necesitamos activa
        // Llama a tu seeder personalizado que tiene los roles y usernames
        $this->call(UsuarioSeeder::class);

        /* Nota: Hemos eliminado el User::factory() que viene por defecto 
           porque ese factory busca la columna 'email', la cual 
           ya no existe en nuestra tabla de usuarios.
        */
    }
}

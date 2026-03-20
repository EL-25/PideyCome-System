<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto; // Importante importar el modelo

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            ['nombre' => 'Hamburguesa Clásica', 'precio' => 8.50, 'categoria' => 'Comida', 'stock' => 15],
            ['nombre' => 'Pizza Margarita', 'precio' => 12.00, 'categoria' => 'Comida', 'stock' => 10],
            ['nombre' => 'Ensalada César', 'precio' => 7.00, 'categoria' => 'Comida', 'stock' => 8],
            ['nombre' => 'Tacos al Pastor', 'precio' => 9.00, 'categoria' => 'Comida', 'stock' => 12],
            ['nombre' => 'Burrito de Pollo', 'precio' => 8.00, 'categoria' => 'Comida', 'stock' => 20],
            ['nombre' => 'Coca Cola', 'precio' => 2.50, 'categoria' => 'Bebidas', 'stock' => 30],
            ['nombre' => 'Agua Mineral', 'precio' => 1.50, 'categoria' => 'Bebidas', 'stock' => 25],
            ['nombre' => 'Limonada Natural', 'precio' => 3.00, 'categoria' => 'Bebidas', 'stock' => 15],
            ['nombre' => 'Café Americano', 'precio' => 2.00, 'categoria' => 'Bebidas', 'stock' => 40],
            ['nombre' => 'Helado de Vainilla', 'precio' => 4.50, 'categoria' => 'Postres', 'stock' => 0], // Este saldrá Agotado
        ];

        foreach ($productos as $p) {
            Producto::create($p);
        }
    }
}

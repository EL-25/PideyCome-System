<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    // Esto permite que el Seeder guarde los datos sin errores
    protected $fillable = ['nombre', 'precio', 'categoria', 'stock', 'imagen'];
}
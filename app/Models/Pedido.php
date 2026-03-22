<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'cliente', 
        'tipo_orden', 
        'mesa_id', 
        'estado', 
        'total'
    ];

    /**
     * ACCESSOR: Obtiene el color de fondo para la tarjeta de cocina
     * Basado en tu flujo de Figma y Tailwind.
     */
    public function getBgColorAttribute()
    {
        return match($this->estado) {
            'ordenada'   => 'bg-yellow-500', // Amarillo
            'recibida'   => 'bg-blue-500',   // Azul
            'preparando' => 'bg-orange-500', // Naranja
            'despachada' => 'bg-green-500',  // Verde
            default      => 'bg-gray-500',
        };
    }

    /**
     * ACCESSOR: Obtiene el texto amigable para mostrar en los botones/badges
     */
    public function getEstadoLabelAttribute()
    {
        return match($this->estado) {
            'ordenada'   => 'Ordenada',
            'recibida'   => 'Recibida por Cocina',
            'preparando' => 'Preparando',
            'despachada' => 'Despachada',
            default      => 'Desconocido',
        };
    }

    /**
     * Relación: Un pedido pertenece a un Mesero (Usuario)
     */
    public function mesero()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: Un pedido tiene muchos detalles (platos)
     */
    public function detalles()
    {
        return $this->hasMany(PedidoDetalle::class);
    }
}

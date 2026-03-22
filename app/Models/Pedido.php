<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    // Campos que permitimos llenar masivamente
    protected $fillable = [
        'user_id', 
        'cliente', 
        'tipo_orden', 
        'mesa_id', 
        'estado', 
        'total'
    ];

    /**
     * Relación: Un pedido pertenece a un Mesero (Usuario)
     */
    public function mesero()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: Un pedido tiene muchos platos/detalles
     */
    public function detalles()
    {
        return $this->hasMany(PedidoDetalle::class);
    }
}

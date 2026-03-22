<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoDetalle extends Model
{
    use HasFactory;

    protected $table = 'pedido_detalles';

    protected $fillable = [
        'pedido_id', 
        'producto_id', // Cambia o asegúrate de tener producto_id para la relación
        'producto_nombre', 
        'cantidad', 
        'precio'
    ];

    // Permite hacer: $detalle->producto->nombre
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}

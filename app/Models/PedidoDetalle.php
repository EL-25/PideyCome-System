<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoDetalle extends Model
{
    use HasFactory;

    // Nombre de la tabla que crearemos en la migración
    protected $table = 'pedido_detalles';

    // Campos que se pueden llenar
    protected $fillable = [
        'pedido_id', 
        'producto_nombre', 
        'cantidad', 
        'precio'
    ];

    // Relación: Este detalle pertenece a un pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}

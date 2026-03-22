<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class CocinaController extends Controller
{
    /**
     * Muestra la pantalla principal de cocina con filtros por pestañas.
     */
    public function index(Request $request)
    {
        // Obtenemos la pestaña actual, por defecto 'todas'
        $tab = $request->input('tab', 'todas');

        // Iniciamos la consulta cargando relaciones para optimizar (Eager Loading)
        // Traemos 'detalles.producto' por si acaso, aunque ya uses producto_nombre
        $query = Pedido::with(['detalles.producto', 'mesero'])
                       ->where('estado', '!=', 'despachada') // Cocina solo ve lo pendiente
                       ->orderBy('created_at', 'asc');

        // Aplicamos filtros según la pestaña seleccionada
        if ($tab === 'nuevas') {
            $query->where('estado', 'ordenada');
        } elseif ($tab === 'recibidas') {
            $query->where('estado', 'recibida');
        } elseif ($tab === 'preparando') {
            $query->where('estado', 'preparando');
        }

        $pedidos = $query->get();

        return view('cocina.index', compact('pedidos', 'tab'));
    }

    /**
     * Lógica para avanzar el pedido al siguiente estado del flujo.
     */
    public function avanzarEstado($id)
    {
        // Buscamos el pedido o lanzamos error 404 si no existe
        $pedido = Pedido::findOrFail($id);

        // Definimos el flujo lógico de estados usando match (PHP 8+)
        $siguienteEstado = match ($pedido->estado) {
            'ordenada'   => 'recibida',
            'recibida'   => 'preparando',
            'preparando' => 'despachada',
            default      => $pedido->estado,
        };

        $pedido->estado = $siguienteEstado;

        /**
         * LÓGICA DE NOTIFICACIÓN PARA EL MESERO:
         * Al despachar, marcamos notificacion_leida como false para que
         * se active la alerta en el panel del mesero.
         */
        if ($siguienteEstado === 'despachada') {
            $pedido->notificacion_leida = false; 
        }

        $pedido->save();

        // Generamos el mensaje de éxito usando el estado actual
        // Asegúrate de tener el accesor getEstadoLabelAttribute en tu modelo Pedido
        $label = $this->obtenerLabel($siguienteEstado);
        $mensaje = "¡Orden #{$pedido->id} actualizada a: {$label}!";
        
        return back()->with('success', $mensaje);
    }

    /**
     * Método auxiliar para labels rápidos en el controlador
     */
    private function obtenerLabel($estado)
    {
        return match ($estado) {
            'ordenada'   => 'Nueva',
            'recibida'   => 'Recibida',
            'preparando' => 'En Preparación',
            'despachada' => 'Despachada (Lista)',
            default      => $estado,
        };
    }
}

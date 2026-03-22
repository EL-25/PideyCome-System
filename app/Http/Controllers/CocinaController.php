<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class CocinaController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'todas');

        // Cargamos relaciones para evitar errores de "propiedad no encontrada"
        $query = Pedido::with(['detalles.producto', 'user']) // Cambié 'mesero' por 'user' si es la relación de Auth
                       ->where('estado', '!=', 'despachada')
                       ->orderBy('created_at', 'asc');

        if ($tab === 'nuevas') {
            $query->where('estado', 'ordenada');
        } elseif ($tab === 'recibidas') {
            $query->where('estado', 'recibida');
        } elseif ($tab === 'preparando') {
            $query->where('estado', 'preparando');
        }

        $pedidos = $query->get();

        // Renderiza el partial que acabas de crear en la nueva carpeta
        if ($request->ajax()) {
            return view('cocina.partials.pedidos_cards', compact('pedidos'))->render();
        }

        return view('cocina.index', compact('pedidos', 'tab'));
    }

    /**
     * Este método coincide con la ruta 'cocina.despachar' en web.php
     */
    public function avanzarEstado(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);

        // Lógica de progresión de estados
        $siguienteEstado = match ($pedido->estado) {
            'ordenada'   => 'recibida',
            'recibida'   => 'preparando',
            'preparando' => 'despachada',
            default      => $pedido->estado,
        };

        $pedido->estado = $siguienteEstado;

        // Si ya está lista, marcamos la notificación para el mesero
        if ($siguienteEstado === 'despachada') {
            $pedido->notificacion_leida = false; 
        }

        $pedido->save();

        $label = $this->obtenerLabel($siguienteEstado);
        $mensaje = "¡Orden #{$pedido->id} actualizada a: {$label}!";
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'nuevo_estado' => $siguienteEstado
            ]);
        }

        return back()->with('success', $mensaje);
    }

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

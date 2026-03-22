<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class CocinaController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'todas');

        $query = Pedido::with(['detalles.producto', 'mesero'])
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

        // --- CAMBIO CLAVE PARA AJAX ---
        if ($request->ajax()) {
            return view('cocina.partials.pedidos_cards', compact('pedidos'))->render();
        }

        return view('cocina.index', compact('pedidos', 'tab'));
    }

    public function avanzarEstado(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);

        $siguienteEstado = match ($pedido->estado) {
            'ordenada'   => 'recibida',
            'recibida'   => 'preparando',
            'preparando' => 'despachada',
            default      => $pedido->estado,
        };

        $pedido->estado = $siguienteEstado;

        if ($siguienteEstado === 'despachada') {
            $pedido->notificacion_leida = false; 
        }

        $pedido->save();

        $label = $this->obtenerLabel($siguienteEstado);
        $mensaje = "¡Orden #{$pedido->id} actualizada a: {$label}!";
        
        // --- CAMBIO CLAVE PARA RESPUESTA JSON ---
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

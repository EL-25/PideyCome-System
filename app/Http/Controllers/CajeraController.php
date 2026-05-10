<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CajeraController extends Controller
{
    /**
     * Muestra el panel principal de la cajera con las mesas activas.
     * Agrupa los pedidos por mesa_id para que se "acumulen" si no han pagado.
     */
    public function index()
    {
        // Obtenemos todos los pedidos que NO están pagados
        $pedidosActivos = Pedido::where('estado', '!=', 'pagado')
            ->with(['detalles', 'mesero'])
            ->get();

        // Agrupamos por mesa_id. 
        // Si mesa_id es null (para llevar), usamos un identificador único por pedido.
        $mesasActivas = $pedidosActivos->groupBy(function($item) {
            return $item->mesa_id ?: 'Llevar-' . $item->id;
        });

        return view('cajera.index', compact('mesasActivas'));
    }

    /**
     * Muestra el detalle de la cuenta de una mesa específica.
     */
    public function detalleCuenta($mesa_id)
    {
        // Si el ID empieza con 'Llevar-', es un pedido individual para llevar
        if (str_starts_with($mesa_id, 'Llevar-')) {
            $id = str_replace('Llevar-', '', $mesa_id);
            $pedidos = Pedido::where('id', $id)->with('detalles')->get();
        } else {
            $pedidos = Pedido::where('mesa_id', $mesa_id)
                ->where('estado', '!=', 'pagado')
                ->with('detalles')
                ->get();
        }

        if ($pedidos->isEmpty()) {
            return redirect()->route('cajera.index')->with('error', 'No hay pedidos activos para esta mesa.');
        }

        $totalGeneral = $pedidos->sum('total');
        $cliente = $pedidos->first()->cliente;

        return view('cajera.detalle', compact('pedidos', 'mesa_id', 'totalGeneral', 'cliente'));
    }

    /**
     * Procesa el pago de todas las órdenes de una mesa.
     */
    public function procesarPago(Request $request, $mesa_id)
    {
        $request->validate([
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'monto_pagado' => 'required|numeric|min:0',
        ]);

        if (str_starts_with($mesa_id, 'Llevar-')) {
            $id = str_replace('Llevar-', '', $mesa_id);
            $pedidos = Pedido::where('id', $id)->get();
        } else {
            $pedidos = Pedido::where('mesa_id', $mesa_id)
                ->where('estado', '!=', 'pagado')
                ->get();
        }

        $totalAbonar = $pedidos->sum('total');
        $cambio = $request->monto_pagado - $totalAbonar;

        foreach ($pedidos as $pedido) {
            $pedido->update([
                'estado' => 'pagado',
                'metodo_pago' => $request->metodo_pago,
                'cajera_id' => Auth::id(),
                'updated_at' => Carbon::now(),
            ]);
        }

        return redirect()->route('cajera.index')->with('success', "Pago procesado con éxito. Cambio: $" . number_format($cambio, 2));
    }
}

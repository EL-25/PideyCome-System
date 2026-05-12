<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Mail\FacturaElectronicaMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CajeraController extends Controller
{
    /**
     * Muestra el panel principal de la cajera con las mesas activas.
     * Agrupa los pedidos por mesa_id para que se "acumulen" si no han pagado.
     */
    public function index(Request $request)
    {
        // Obtenemos solo los pedidos que están esperando cobro
        $pedidosActivos = Pedido::where('estado', 'por_cobrar')
            ->with(['detalles', 'mesero'])
            ->get();

        // Agrupamos por mesa_id. 
        // Si mesa_id es null (para llevar), usamos un identificador único por pedido.
        $mesasActivas = $pedidosActivos->groupBy(function($item) {
            return $item->mesa_id ?: 'Llevar-' . $item->id;
        });

        if ($request->ajax()) {
            return view('cajera.partials.mesas_grid', compact('mesasActivas'))->render();
        }

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
            'tipo_comprobante' => 'required|in:ticket,factura',
            'cliente_email' => 'required_if:tipo_comprobante,factura|nullable|email',
        ]);

        if (str_starts_with($mesa_id, 'Llevar-')) {
            $id = str_replace('Llevar-', '', $mesa_id);
            $pedidos = Pedido::where('id', $id)->get();
        } else {
            $pedidos = Pedido::where('mesa_id', $mesa_id)
                ->where('estado', 'por_cobrar')
                ->get();
        }

        $totalAbonar = $pedidos->sum('total');
        $cambio = $request->monto_pagado - $totalAbonar;

        foreach ($pedidos as $pedido) {
            $pedido->update([
                'estado' => 'pagado',
                'metodo_pago' => $request->metodo_pago,
                'tipo_comprobante' => $request->tipo_comprobante,
                'cliente_email' => $request->cliente_email,
                'cajera_id' => Auth::id(),
                'updated_at' => Carbon::now(),
            ]);
        }

        $mensaje = "Pago procesado con éxito en Restaurante UDB.";
        if ($request->tipo_comprobante === 'factura') {
            try {
                Mail::to($request->cliente_email)->send(new FacturaElectronicaMail($pedidos, $totalAbonar, $pedidos->first()->cliente));
                $mensaje .= " Factura electrónica enviada a " . $request->cliente_email;
            } catch (\Exception $e) {
                $mensaje .= " Error al enviar correo: " . $e->getMessage();
            }
        } elseif ($request->metodo_pago === 'efectivo') {
            $mensaje .= " Cambio: $" . number_format($cambio, 2);
        }

        return redirect()->route('cajera.index')->with('success', $mensaje);
    }
}

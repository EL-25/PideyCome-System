<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class MeseroController extends Controller
{
    public function index(Request $request)
    {
        // Lógica de Filtros (Para las pestañas de Comida, Bebidas, etc.)
        $query = Producto::query();
        
        if ($request->has('categoria') && $request->categoria != 'Todos') {
            $query->where('categoria', $request->categoria);
        }

        $productos = $query->get();
        
        // Recuperamos el carrito de la sesión
        $carrito = session()->get('carrito', []);

        return view('mesero.index', compact('productos', 'carrito'));
    }

    public function agregar($id)
    {
        $producto = Producto::findOrFail($id);
        $carrito = session()->get('carrito', []);

        // Si el producto ya está en el carrito, aumentamos cantidad
        if(isset($carrito[$id])) {
            $carrito[$id]['cantidad']++;
        } else {
            // Si es nuevo, lo agregamos
            $carrito[$id] = [
                "nombre" => $producto->nombre,
                "cantidad" => 1,
                "precio" => $producto->precio,
                "categoria" => $producto->categoria
            ];
        }

        session()->put('carrito', $carrito);
        return redirect()->back()->with('success', $producto->nombre . ' agregado al carrito');
    }

    //Limpiar el carrito de la sesión
    public function limpiar()
    {
        session()->forget('carrito');
        return redirect()->back()->with('info', 'La orden ha sido limpiada');
    }
}

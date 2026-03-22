<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MeseroController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::query();
        
        // Filtrado por categoría
        if ($request->has('categoria') && $request->categoria != 'Todos') {
            $query->where('categoria', $request->categoria);
        }

        $productos = $query->get();
        $carrito = session()->get('carrito', []);

        // Si la petición es AJAX (clic en Comida/Bebida), solo devolvemos la cuadrícula de productos
        if ($request->ajax()) {
            return view('mesero.partials.productos_grid', compact('productos'))->render();
        }

        // LÓGICA DE NOTIFICACIONES: Contamos órdenes listas para entregar (Despachadas)
        $notificacionesCount = Pedido::where('user_id', Auth::id())
            ->where('estado', 'Despachada') 
            ->where('notificacion_leida', false)
            ->count();

        return view('mesero.index', compact('productos', 'carrito', 'notificacionesCount'));
    }

    // --- NUEVA FUNCIÓN PARA LA VISTA "MIS ÓRDENES" ---
    public function misOrdenes(Request $request)
    {
        $query = Pedido::where('user_id', Auth::id())->with('detalles');

        // Filtro por mesa (Captura image_b8a981.png)
        if ($request->filled('mesa') && $request->mesa != 'Todas las órdenes') {
            $query->where('mesa_id', $request->mesa);
        }

        $ordenes = $query->orderBy('created_at', 'desc')->get();

        // Si filtramos por mesa vía AJAX, solo devolvemos la lista de tarjetas
        if ($request->ajax()) {
            return view('mesero.partials.pedidos_lista', compact('ordenes'))->render();
        }

        return view('mesero.mis_ordenes', compact('ordenes'));
    }

    // --- FUNCIONES AJAX DEL CARRITO (Se mantienen tus mejoras) ---

    public function agregarAjax(Request $request)
    {
        $producto = Producto::find($request->id);
        
        if (!$producto || $producto->stock <= 0) {
            return response()->json(['error' => 'Producto agotado'], 400);
        }

        $carrito = session()->get('carrito', []);

        if(isset($carrito[$producto->id])) {
            if ($carrito[$producto->id]['cantidad'] + 1 > $producto->stock) {
                return response()->json(['error' => 'No hay más stock'], 400);
            }
            $carrito[$producto->id]['cantidad']++;
        } else {
            $carrito[$producto->id] = [
                "id" => $producto->id,
                "nombre" => $producto->nombre,
                "cantidad" => 1,
                "precio" => $producto->precio,
                "categoria" => $producto->categoria,
                "stock_max" => $producto->stock 
            ];
        }

        session()->put('carrito', $carrito);
        
        return response()->json([
            'message' => 'Agregado',
            'carrito' => $carrito,
            'total' => collect($carrito)->sum(fn($i) => $i['precio'] * $i['cantidad'])
        ]);
    }

    public function actualizarCantidadAjax(Request $request)
    {
        $id = $request->id;
        $accion = $request->accion;
        $carrito = session()->get('carrito', []);
        $producto = Producto::find($id);

        if(!isset($carrito[$id])) return response()->json(['error' => 'No encontrado'], 404);

        if ($accion === 'incrementar') {
            if ($carrito[$id]['cantidad'] + 1 > $producto->stock) {
                return response()->json(['error' => 'Máximo alcanzado'], 400);
            }
            $carrito[$id]['cantidad']++;
        } else {
            $carrito[$id]['cantidad']--;
            if ($carrito[$id]['cantidad'] <= 0) unset($carrito[$id]);
        }

        session()->put('carrito', $carrito);
        return response()->json([
            'carrito' => $carrito,
            'total' => collect($carrito)->sum(fn($i) => $i['precio'] * $i['cantidad'])
        ]);
    }

    public function eliminarAjax(Request $request)
    {
        $carrito = session()->get('carrito', []);
        if(isset($carrito[$request->id])) {
            unset($carrito[$request->id]);
            session()->put('carrito', $carrito);
        }
        return response()->json([
            'carrito' => $carrito,
            'total' => collect($carrito)->sum(fn($i) => $i['precio'] * $i['cantidad'])
        ]);
    }

    public function store(Request $request)
    {
        $carrito = session()->get('carrito', []);
        if (empty($carrito)) return redirect()->back()->with('error', 'Orden vacía.');

        try {
            DB::transaction(function () use ($request, $carrito) {
                $total = collect($carrito)->sum(fn($item) => $item['precio'] * $item['cantidad']);

                $pedido = Pedido::create([
                    'user_id' => Auth::id(),
                    'cliente' => $request->cliente,
                    'tipo_orden' => $request->tipo_orden,
                    'mesa_id' => $request->tipo_orden == 'comer_aqui' ? $request->mesa_id : null,
                    'estado' => 'Ordenada',
                    'total' => $total
                ]);

                foreach ($carrito as $id => $item) {
                    PedidoDetalle::create([
                        'pedido_id' => $pedido->id,
                        'producto_nombre' => $item['nombre'],
                        'cantidad' => $item['cantidad'],
                        'precio' => $item['precio']
                    ]);
                    Producto::where('id', $id)->decrement('stock', $item['cantidad']);
                }
            });

            session()->forget('carrito');
            return redirect()->route('mesero.index')->with('success', '¡Orden enviada!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // --- FUNCIÓN PARA LIMPIAR EL CARRITO ---
    public function limpiar()
    {
        // Eliminamos la variable 'carrito' de la sesión
        session()->forget('carrito');
        
        // Redireccionamos de vuelta al panel con un mensaje
        return redirect()->route('mesero.index')->with('success', 'Orden limpiada correctamente.');
    }
}

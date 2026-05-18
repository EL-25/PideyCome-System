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

        // Búsqueda por nombre
        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        $productos = $query->get();
        $carrito = session()->get('carrito', []);

        // Lógica de notificaciones para el mesero
        $notificacionesCount = Pedido::where('user_id', Auth::id())
            ->where('estado', 'despachada') 
            ->where('notificacion_leida', false)
            ->count();

        if ($request->ajax()) {
            if ($request->has('check_notifications')) {
                return response()->json(['count' => $notificacionesCount]);
            }
            return view('mesero.partials.productos_grid', compact('productos'))->render();
        }

        return view('mesero.index', compact('productos', 'carrito', 'notificacionesCount'));
    }

    public function misOrdenes(Request $request)
    {
        // Al entrar, marcamos como leídas las notificaciones de órdenes despachadas
        Pedido::where('user_id', Auth::id())
            ->where('estado', 'despachada')
            ->where('notificacion_leida', false)
            ->update(['notificacion_leida' => true]);

        $query = Pedido::where('user_id', Auth::id())->with('detalles.producto');

        if ($request->filled('mesa') && $request->mesa != 'Todas las órdenes') {
            $query->where('mesa_id', $request->mesa);
        }

        $ordenes = $query->orderBy('created_at', 'desc')->get();

        if ($request->ajax()) {
            return view('mesero.partials.pedidos_lista', compact('ordenes'))->render();
        }

        return view('mesero.mis_ordenes', compact('ordenes'));
    }

    // --- FUNCIONES AJAX DEL CARRITO ---

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

    // --- PROCESAR LA ORDEN (CORREGIDO) ---
    public function store(Request $request)
    {
        $carrito = session()->get('carrito', []);
        if (empty($carrito)) return redirect()->back()->with('error', 'Orden vacía.');

        $rules = [
            'cliente' => ['required', 'string', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u'],
            'tipo_orden' => ['required', 'in:comer_aqui,para_llevar'],
        ];

        if ($request->tipo_orden === 'comer_aqui') {
            $rules['mesa_id'] = ['required', 'integer', 'between:1,10'];
        }

        $messages = [
            'cliente.required' => 'El nombre del cliente es obligatorio.',
            'cliente.regex' => 'El nombre del cliente solo debe contener letras (nada de números ni símbolos).',
            'mesa_id.required' => 'Debe elegir una mesa para comer aquí.',
            'mesa_id.between' => 'La mesa elegida no es válida.',
        ];

        $request->validate($rules, $messages);

        try {
            DB::transaction(function () use ($request, $carrito) {
                $total = collect($carrito)->sum(fn($item) => $item['precio'] * $item['cantidad']);

                $pedido = Pedido::create([
                    'user_id' => Auth::id(),
                    'cliente' => $request->cliente,
                    'tipo_orden' => $request->tipo_orden,
                    'mesa_id' => $request->tipo_orden == 'comer_aqui' ? $request->mesa_id : null,
                    'estado' => 'ordenada', 
                    'total' => $total,
                    'notificacion_leida' => true 
                ]);

                foreach ($carrito as $id => $item) {
                    PedidoDetalle::create([
                        'pedido_id'       => $pedido->id,
                        'producto_nombre' => $item['nombre'],
                        'cantidad'        => $item['cantidad'],
                        'precio'          => $item['precio']
                    ]);
                    
                    // Descontamos stock del producto en Railway
                    Producto::where('id', $id)->decrement('stock', $item['cantidad']);
                }
            });

            session()->forget('carrito');
            return redirect()->route('mesero.index')->with('success', '¡Orden enviada a cocina!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al procesar: ' . $e->getMessage());
        }
    }

    public function limpiar()
    {
        session()->forget('carrito');
        return redirect()->route('mesero.index')->with('success', 'Orden limpiada correctamente.');
    }

    /**
     * Consulta si una mesa tiene una cuenta activa y quién es el cliente.
     */
    public function checkMesaStatus($mesa_id)
    {
        $pedidoActivo = Pedido::where('mesa_id', $mesa_id)
            ->where('estado', '!=', 'pagado')
            ->first();

        if ($pedidoActivo) {
            return response()->json([
                'ocupada' => true,
                'cliente' => $pedidoActivo->cliente
            ]);
        }

        return response()->json(['ocupada' => false]);
    }

    /**
     * Marca un pedido como entregado (quita la notificación del mesero).
     */
    /**
     * Marca un pedido como entregado (quita la notificación del mesero).
     */
    public function entregar($id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->update(['notificacion_leida' => true]);
        
        return back()->with('success', '¡Orden entregada a la mesa!');
    }

    /**
     * Marca todas las órdenes de una mesa como "por_cobrar" para que aparezcan en caja.
     */
    public function solicitarCuenta($mesa_id)
    {
        // Si es para llevar, el ID es diferente
        if (str_starts_with($mesa_id, 'Llevar-')) {
            $id = str_replace('Llevar-', '', $mesa_id);
            Pedido::where('id', $id)->update(['estado' => 'por_cobrar']);
        } else {
            // Buscamos órdenes de esa mesa que no estén pagadas ni ya solicitadas
            Pedido::where('mesa_id', $mesa_id)
                ->where('estado', 'despachada')
                ->update(['estado' => 'por_cobrar']);
        }

        return back()->with('success', '¡Cuenta enviada a caja exitosamente!');
    }
}

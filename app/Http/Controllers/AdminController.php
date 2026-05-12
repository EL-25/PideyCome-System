<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Movimiento;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtener todos los datos para las tablas
        $productos = Producto::all();
        $usuarios = User::all();
        
        // Filtro de movimientos
        $queryMovimientos = Movimiento::with('user')->orderBy('created_at', 'desc');
        
        if ($request->has('fecha') && $request->fecha) {
            $queryMovimientos->whereDate('created_at', $request->fecha);
        } else {
            $queryMovimientos->whereDate('created_at', Carbon::today());
        }

        if ($request->has('filtro') && $request->filtro != 'todos') {
            $queryMovimientos->where('tipo', $request->filtro);
        }

        $movimientos = $queryMovimientos->get();
        
        // 2. Calcular estadísticas para los "Cards" superiores
        $hoy = Carbon::today();
        
        $stats = [
            // Suma del total de pedidos cuya fecha de creación sea hoy
            'ventas_hoy' => Pedido::whereDate('created_at', $hoy)->sum('total'),
            
            // Conteo total de órdenes en el sistema
            'ordenes_totales' => Pedido::count(),
            
            // Conteo de productos registrados
            'productos_activos' => Producto::count(),
            
            // Conteo de productos con stock menor a 5 unidades (ajustable)
            'stock_bajo' => Producto::where('stock', '<', 5)->count(),
        ];

        // 3. Retornar la vista pasando las variables compactadas
        if ($request->ajax()) {
            return response()->json(['stats' => $stats]);
        }

        return view('admin.index', compact('productos', 'usuarios', 'stats', 'movimientos'));
    }

    private function registrarMovimiento($tipo, $accion, $descripcion)
    {
        Movimiento::create([
            'user_id' => auth()->id(),
            'tipo' => $tipo,
            'accion' => $accion,
            'descripcion' => $descripcion
        ]);
    }

    /**
     * Ejemplo de método para guardar un producto desde el panel
     */
    public function storeProducto(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'categoria' => 'required|string',
            'stock' => 'required|integer|min:0',
        ]);

        $producto = Producto::create([
            'nombre' => $request->nombre,
            'precio' => $request->precio,
            'categoria' => $request->categoria,
            'stock' => $request->stock,
            'imagen' => 'default.png', // Opcional según tu lógica
        ]);

        $this->registrarMovimiento('producto', 'creación', "Se creó el producto: {$producto->nombre} con stock inicial de {$producto->stock}");

        return back()->with('success', 'Producto agregado correctamente.');
    }

    public function updateProducto(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'categoria' => 'required|string',
            'stock' => 'required|integer|min:0',
        ]);

        $producto = Producto::findOrFail($id);
        $oldStock = $producto->stock;
        $producto->update([
            'nombre' => $request->nombre,
            'precio' => $request->precio,
            'categoria' => $request->categoria,
            'stock' => $request->stock,
        ]);

        $this->registrarMovimiento('producto', 'edición', "Se editó el producto: {$producto->nombre}. Stock anterior: {$oldStock}, Nuevo stock: {$producto->stock}");

        return back()->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Ejemplo para eliminar un producto
     */
    public function destroyProducto($id)
    {
        $producto = Producto::findOrFail($id);
        $nombre = $producto->nombre;
        $producto->delete();
        
        $this->registrarMovimiento('producto', 'eliminación', "Se eliminó el producto: {$nombre}");
        
        return back()->with('success', 'Producto eliminado.');
    }

    public function inventario()
    {
        $productos = Producto::all();
        return view('admin.inventario', compact('productos'));
    }

    public function actualizarStock(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer|min:0'
        ]);

        $producto = Producto::findOrFail($id);
        $oldStock = $producto->stock;
        $producto->update(['stock' => $request->stock]);

        $this->registrarMovimiento('inventario', 'ajuste', "Se ajustó el stock de {$producto->nombre}. De {$oldStock} a {$producto->stock}");

        return back()->with('success', 'Stock actualizado.');
    }

    public function usuarios()
    {
        $usuarios = User::all();
        return view('admin.usuarios', compact('usuarios'));
    }

    public function storeUsuario(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'role' => 'required|string',
        ]);

        // Generar contraseña aleatoria de 8 caracteres
        $passwordAleatoria = str()->random(8);

        $usuario = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => bcrypt($passwordAleatoria),
            'role' => $request->role,
            'temp_passwd' => 1, // Obligatorio cambiar en el primer login
        ]);

        $this->registrarMovimiento('usuario', 'creación', "Se creó el usuario: {$usuario->name} con rol {$usuario->role}");

        return back()->with('success', "Usuario creado. Contraseña temporal: {$passwordAleatoria} (Debe cambiarla al entrar).");
    }

    public function updateUsuario(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $id,
            'role' => 'required|string',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'role' => $request->role,
        ]);

        // Si se proporciona una contraseña, se actualiza y se marca como temporal
        if ($request->filled('password')) {
            $user->update([
                'password' => bcrypt($request->password),
                'temp_passwd' => 1
            ]);
        }

        $this->registrarMovimiento('usuario', 'edición', "Se editó el usuario: {$user->name}");

        return back()->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroyUsuario($id)
    {
        $user = User::findOrFail($id);
        
        // Evitar que el admin se elimine a sí mismo
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'No puedes eliminar tu propia cuenta.']);
        }

        $nombre = $user->name;
        $user->delete();
        
        $this->registrarMovimiento('usuario', 'eliminación', "Se eliminó el usuario: {$nombre}");

        return back()->with('success', 'Usuario eliminado.');
    }

    public function movimientos(Request $request)
    {
        $query = Movimiento::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }

        if ($request->filled('tipo') && $request->tipo !== 'todos') {
            $query->where('tipo', $request->tipo);
        }

        $movimientos = $query->paginate(20);
        return view('admin.movimientos', compact('movimientos'));
    }

    public function ventas(Request $request)
    {
        $query = Pedido::with(['detalles', 'mesero'])
            ->where('estado', 'pagado')
            ->orderBy('updated_at', 'desc');

        if ($request->filled('fecha')) {
            $query->whereDate('updated_at', $request->fecha);
        }

        $ventas = $query->paginate(15);
        
        // Calcular total del periodo filtrado
        $totalVentas = $query->sum('total');

        return view('admin.ventas', compact('ventas', 'totalVentas'));
    }
}
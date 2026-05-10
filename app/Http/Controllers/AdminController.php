<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\User;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Obtener todos los datos para las tablas
        $productos = Producto::all();
        $usuarios = User::all();
        
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
        return view('admin.index', compact('productos', 'usuarios', 'stats'));
    }

    /**
     * Ejemplo de método para guardar un producto desde el panel
     */
    public function storeProducto(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'categoria' => 'required|string',
            'stock' => 'required|integer',
        ]);

        Producto::create([
            'nombre' => $request->nombre,
            'precio' => $request->precio,
            'categoria' => $request->categoria,
            'stock' => $request->stock,
            'imagen' => 'default.png', // Opcional según tu lógica
        ]);

        return back()->with('success', 'Producto agregado correctamente.');
    }

    public function updateProducto(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'categoria' => 'required|string',
            'stock' => 'required|integer',
        ]);

        $producto = Producto::findOrFail($id);
        $producto->update([
            'nombre' => $request->nombre,
            'precio' => $request->precio,
            'categoria' => $request->categoria,
            'stock' => $request->stock,
        ]);

        return back()->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Ejemplo para eliminar un producto
     */
    public function destroyProducto($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();
        
        return back()->with('success', 'Producto eliminado.');
    }

    public function inventario()
    {
        $productos = Producto::all();
        return view('admin.inventario', compact('productos'));
    }

    public function actualizarStock(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        $producto->update(['stock' => $request->stock]);
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

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => bcrypt($passwordAleatoria),
            'role' => $request->role,
            'temp_passwd' => 1, // Obligatorio cambiar en el primer login
        ]);

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

        return back()->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroyUsuario($id)
    {
        $user = User::findOrFail($id);
        
        // Evitar que el admin se elimine a sí mismo
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'No puedes eliminar tu propia cuenta.']);
        }

        $user->delete();
        return back()->with('success', 'Usuario eliminado.');
    }

    public function reportes()
    {
        return view('admin.reportes');
    }
}
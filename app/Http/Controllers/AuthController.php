<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Mostrar la vista del Login
    public function showLogin()
    {
        return view('auth.login');
    }

    // 2. Procesar el formulario con validaciones
    public function login(Request $request)
    {
        // Validamos que los campos no vengan vacíos
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Oye, olvidaste poner tu usuario.',
            'password.required' => 'La contraseña es obligatoria para entrar.',
        ]);

        // Intentamos iniciar sesión
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirección inteligente por ROL
            $role = Auth::user()->role;
            
            // ACTUALIZADO: Usamos route() para que coincida con web.php
            return match($role) {
                'admin'   => redirect()->route('admin.index'),
                'mesero'  => redirect()->route('mesero.index'),
                'cocina'  => redirect()->route('cocina.index'), // Antes decía /cocina/dashboard
                'cajera'  => redirect()->route('cajera.index'),
                default   => redirect('/'),
            };
        }

        // Si falla, regresamos con un mensaje de error
        return back()->withErrors([
            'error_login' => 'Usuario o contraseña incorrectos. Verifica tus datos.',
        ])->withInput();
    }

    // 3. Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}

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
            $user = Auth::user();

            // Si tiene contraseña temporal, lo mandamos a cambiarla
            if ($user->temp_passwd) {
                return redirect()->route('password.change');
            }

            // Redirección inteligente por ROL
            $role = $user->role;
            
            // ACTUALIZADO: Usamos route() para que coincida con web.php
            return match($role) {
                'admin'   => redirect()->route('admin.index'),
                'mesero'  => redirect()->route('mesero.index'),
                'cocina'  => redirect()->route('cocina.index'),
                'cajera'  => redirect()->route('cajera.index'),
                default   => redirect('/'),
            };
        }

        // Si falla, regresamos con un mensaje de error
        return back()->withErrors([
            'error_login' => 'Usuario o contraseña incorrectos. Verifica tus datos.',
        ])->withInput();
    }

    // 3. Mostrar vista de cambio de contraseña
    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    // 4. Procesar el cambio de contraseña
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:4|confirmed',
        ]);

        $user = Auth::user();
        $user->password = bcrypt($request->password);
        $user->temp_passwd = 0; // Ya no es temporal
        $user->save();

        Auth::logout();
        return redirect()->route('login')->with('success', 'Contraseña actualizada. Inicia sesión con tus nuevos datos.');
    }

    // 5. Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}

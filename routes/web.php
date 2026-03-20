<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MeseroController;
use Illuminate\Support\Facades\Route;

// Rutas Públicas
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas Protegidas (Solo si están logueados)
Route::middleware(['auth'])->group(function () {
    
    // --- RUTAS DEL MESERO ---
    Route::get('/mesero', [MeseroController::class, 'index'])->name('mesero.index');
    
    // PASO 1: Rutas para la lógica del carrito
    Route::post('/carrito/agregar/{id}', [MeseroController::class, 'agregar'])->name('carrito.agregar');
    Route::post('/carrito/limpiar', [MeseroController::class, 'limpiar'])->name('carrito.limpiar');
    // ------------------------

    // Otras rutas de ejemplo
    Route::get('/admin/dashboard', function () { return "Panel de Administrador - PideYCome"; });
    Route::get('/cocina/dashboard', function () { return "Panel de Cocina - PideYCome"; });
    Route::get('/cajera/dashboard', function () { return "Panel de Cajera - PideYCome"; });
});

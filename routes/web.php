<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MeseroController;
use Illuminate\Support\Facades\Route;

// --- Rutas Públicas ---
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Rutas Protegidas (Requieren Login) ---
Route::middleware(['auth'])->group(function () {
    
    // --- GRUPO DE RUTAS DEL MESERO ---
    Route::prefix('mesero')->group(function () {
        
        // Vista principal (Panel de toma de pedidos)
        Route::get('/', [MeseroController::class, 'index'])->name('mesero.index');
        
        // NUEVA: Vista de "Mis Órdenes" (Seguimiento y Filtro por Mesa)
        // Esta ruta maneja lo que ves en las capturas de filtrado y estados
        Route::get('/mis-ordenes', [MeseroController::class, 'misOrdenes'])->name('mesero.ordenes');

        // Acción final: Enviar la orden a cocina
        Route::post('/ordenar', [MeseroController::class, 'store'])->name('pedido.store');
    });

    // Estas rutas permiten que el carrito y el menú de categorías se actualicen sin parpadeos
    Route::prefix('carrito')->group(function () {
        Route::post('/agregar', [MeseroController::class, 'agregarAjax'])->name('carrito.agregar.ajax');
        Route::post('/actualizar', [MeseroController::class, 'actualizarCantidadAjax'])->name('carrito.actualizar.ajax');
        Route::post('/eliminar', [MeseroController::class, 'eliminarAjax'])->name('carrito.eliminar.ajax');
        Route::post('/limpiar', [MeseroController::class, 'limpiar'])->name('mesero.limpiar');
    });

    // --- OTRAS RUTAS DEL SISTEMA ---
    Route::get('/admin/dashboard', function () { return "Panel de Administrador - PideYCome"; });
    Route::get('/cocina/dashboard', function () { return "Panel de Cocina - PideYCome"; });
    Route::get('/cajera/dashboard', function () { return "Panel de Cajera - PideYCome"; });
});

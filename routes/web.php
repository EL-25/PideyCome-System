<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MeseroController;
use App\Http\Controllers\CocinaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - PideYCome
|--------------------------------------------------------------------------
*/

// --- Rutas Públicas ---
// La raíz muestra el login directamente
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Rutas Protegidas (Requieren estar autenticado) ---
Route::middleware(['auth'])->group(function () {
    
    // --- ÁREA DE MESERO ---
    // URL Base: /mesero
    Route::prefix('mesero')->group(function () {
        Route::get('/', [MeseroController::class, 'index'])->name('mesero.index');
        Route::get('/mis-ordenes', [MeseroController::class, 'misOrdenes'])->name('mesero.ordenes');
        Route::post('/ordenar', [MeseroController::class, 'store'])->name('pedido.store');
    });

    // --- ÁREA DE COCINA ---
    // URL Base: /cocina (Ya no requiere /dashboard al final)
    Route::prefix('cocina')->group(function () {
        Route::get('/', [CocinaController::class, 'index'])->name('cocina.index');
        
        // Acción para que el cocinero cambie el estado del pedido
        Route::post('/avanzar/{id}', [CocinaController::class, 'avanzarEstado'])->name('cocina.avanzar');
    });

    // --- CARRITO AJAX (Funciones compartidas del sistema de pedidos) ---
    Route::prefix('carrito')->group(function () {
        Route::post('/agregar', [MeseroController::class, 'agregarAjax'])->name('carrito.agregar.ajax');
        Route::post('/actualizar', [MeseroController::class, 'actualizarCantidadAjax'])->name('carrito.actualizar.ajax');
        Route::post('/eliminar', [MeseroController::class, 'eliminarAjax'])->name('carrito.eliminar.ajax');
        Route::post('/limpiar', [MeseroController::class, 'limpiar'])->name('mesero.limpiar');
    });

    // --- OTRAS RUTAS (Futuras implementaciones) ---
    Route::get('/admin/dashboard', function () { 
        return "Panel de Administrador - Próximamente"; 
    })->name('admin.index');

    Route::get('/cajera/dashboard', function () { 
        return "Panel de Cajera - Próximamente"; 
    })->name('cajera.index');

});

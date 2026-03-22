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
// Cambiamos el nombre de la ruta POST para que no choque con el GET en móviles
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login-proceso', [AuthController::class, 'login'])->name('login.post'); 
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Rutas Protegidas ---
Route::middleware(['auth'])->group(function () {
    
    // --- ÁREA DE MESERO ---
    Route::prefix('mesero')->group(function () {
        Route::get('/', [MeseroController::class, 'index'])->name('mesero.index');
        Route::get('/mis-ordenes', [MeseroController::class, 'misOrdenes'])->name('mesero.ordenes');
        Route::post('/ordenar', [MeseroController::class, 'store'])->name('pedido.store');
    });

    // --- ÁREA DE COCINA ---
    Route::prefix('cocina')->group(function () {
        Route::get('/', [CocinaController::class, 'index'])->name('cocina.index');
        
        // Cambiado a patch para que coincida con @method('PATCH') de tu Blade
        Route::patch('/despachar/{id}', [CocinaController::class, 'avanzarEstado'])->name('cocina.despachar');
    });

    // --- CARRITO AJAX ---
    Route::prefix('carrito')->group(function () {
        Route::post('/agregar-ajax', [MeseroController::class, 'agregarAjax'])->name('carrito.agregar.ajax');
        Route::post('/actualizar-ajax', [MeseroController::class, 'actualizarCantidadAjax'])->name('carrito.actualizar.ajax');
        Route::post('/eliminar-ajax', [MeseroController::class, 'eliminarAjax'])->name('carrito.eliminar.ajax');
        Route::post('/limpiar', [MeseroController::class, 'limpiar'])->name('mesero.limpiar');
    });

    // --- DASHBOARDS ---
    Route::get('/admin/dashboard', function () { 
        return "Panel de Administrador - Próximamente"; 
    })->name('admin.index');

    Route::get('/cajera/dashboard', function () { 
        return "Panel de Cajera - Próximamente"; 
    })->name('cajera.index');
});

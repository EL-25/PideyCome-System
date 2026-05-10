<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MeseroController;
use App\Http\Controllers\CocinaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CajeraController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - PideYCome
|--------------------------------------------------------------------------
*/

// --- Rutas Públicas ---
// Cambiamos el nombre de la ruta POST para que no choque con el GET en móviles
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login-proceso', [AuthController::class, 'login'])->name('login.post'); 
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Rutas Protegidas ---
Route::middleware(['auth'])->group(function () {
    
    // --- ÁREA DE MESERO ---
    Route::prefix('mesero')->group(function () {
        Route::get('/', [MeseroController::class, 'index'])->name('mesero.index');
        Route::get('/mis-ordenes', [MeseroController::class, 'misOrdenes'])->name('mesero.ordenes');
        Route::post('/ordenar', [MeseroController::class, 'store'])->name('pedido.store');
        Route::get('/mesa-status/{mesa_id}', [MeseroController::class, 'checkMesaStatus'])->name('mesero.mesa.status');
        Route::post('/entregar/{id}', [MeseroController::class, 'entregar'])->name('mesero.entregar');
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

    // --- DASHBOARDS ADMINISTRATIVOS ---
   // --- ÁREA DE ADMINISTRADOR ---
    Route::prefix('admin')->group(function () {
        // Dashboard principal
        Route::get('/', [AdminController::class, 'index'])->name('admin.index');
        Route::get('/dashboard', [AdminController::class, 'index']);
        
        // Gestión de Inventario
        Route::get('/inventario', [AdminController::class, 'inventario'])->name('admin.inventario');
        Route::post('/inventario/actualizar/{id}', [AdminController::class, 'actualizarStock'])->name('admin.actualizarStock');
        
        // Gestión de Productos
        Route::post('/productos/guardar', [AdminController::class, 'storeProducto'])->name('admin.productos.store');
        Route::post('/productos/actualizar/{id}', [AdminController::class, 'updateProducto'])->name('admin.productos.update');
        Route::delete('/productos/eliminar/{id}', [AdminController::class, 'destroyProducto'])->name('admin.productos.destroy');
    
        // Gestión de Usuarios
        Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('admin.usuarios');
        Route::post('/usuarios/guardar', [AdminController::class, 'storeUsuario'])->name('admin.usuarios.store');
        Route::post('/usuarios/actualizar/{id}', [AdminController::class, 'updateUsuario'])->name('admin.usuarios.update');
        Route::delete('/usuarios/eliminar/{id}', [AdminController::class, 'destroyUsuario'])->name('admin.usuarios.destroy');
        
        // Reportes
        Route::get('/reportes', [AdminController::class, 'reportes'])->name('admin.reportes');
    });

    // Ruta para cambio de contraseña obligatoria
    Route::get('/cambiar-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/cambiar-password', [AuthController::class, 'updatePassword'])->name('password.update');

    // --- ÁREA DE CAJERA ---
    Route::prefix('cajera')->group(function () {
        Route::get('/', [CajeraController::class, 'index'])->name('cajera.index');
        Route::get('/cuenta/{mesa_id}', [CajeraController::class, 'detalleCuenta'])->name('cajera.cuenta');
        Route::post('/pagar/{mesa_id}', [CajeraController::class, 'procesarPago'])->name('cajera.pagar');
    });
});

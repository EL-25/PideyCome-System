<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Ruta principal: El Login
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas (Solo entran si están logueados)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', function () { return "Panel de Administrador - PideYCome"; });
    Route::get('/mesero/dashboard', function () { return "Panel de Mesero - PideYCome"; });
    Route::get('/cocina/dashboard', function () { return "Panel de Cocina - PideYCome"; });
    Route::get('/cajera/dashboard', function () { return "Panel de Cajera - PideYCome"; });
});

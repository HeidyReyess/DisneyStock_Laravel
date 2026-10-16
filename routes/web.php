<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;

use App\Http\Controllers\InventarioController;

// ── Pública ───────────────────────────────────────────────
Route::get('/', fn() => view('welcome'))->name('home');

// ── Auth (login / logout) ─────────────────────────────────
require __DIR__.'/auth.php';

// ═══════════════════════════════════════════════════════════
//  GRUPO AUTENTICADO — admin + empleado
// ═══════════════════════════════════════════════════════════
Route::middleware(['auth', 'no-cache'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Productos (admin y empleado pueden ver; toggle y destroy solo admin en el controller)
    Route::resource('productos', ProductoController::class)
        ->except(['create', 'edit', 'show']);
    Route::patch('productos/{producto}/toggle', [ProductoController::class, 'toggle'])
        ->name('productos.toggle');

    // Inventario — admin y empleado pueden ver; solo admin registra movimientos
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::post('/inventario/movimiento', [InventarioController::class, 'movimiento'])->name('inventario.movimiento');

    // Aquí irán: ventas, reportes …

});

// ═══════════════════════════════════════════════════════════
//  GRUPO SOLO ADMINISTRADOR
// ═══════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:administrador', 'no-cache'])->group(function () {

    // Categorías
    Route::resource('categorias', CategoriaController::class)
        ->except(['create', 'edit', 'show']);

    // Aquí irán: usuarios …

});

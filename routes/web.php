<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;

use App\Http\Controllers\ReporteController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\VentaController;

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

    // Ventas
    Route::resource('ventas', VentaController::class)
        ->except(['create', 'edit']);
    Route::patch('ventas/{venta}/anular', [VentaController::class, 'anular'])
        ->name('ventas.anular');

    // Reportes — admin y empleado
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');

    // Aquí irán: …

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

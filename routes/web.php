<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ConfiguracionController;

// ── Pública ───────────────────────────────────────────────
Route::get('/', fn() => view('welcome'))->name('home');

// ── Auth (login / logout) ─────────────────────────────────
require __DIR__.'/auth.php';

// ═══════════════════════════════════════════════════════════
//  GRUPO AUTENTICADO — admin + empleado
// ═══════════════════════════════════════════════════════════
Route::middleware(['auth', 'activo', 'no-cache'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Productos — empleado puede ver; store/update/toggle/destroy solo admin (en controller)
    Route::resource('productos', ProductoController::class)
        ->except(['create', 'edit', 'show']);
    Route::patch('productos/{producto}/toggle', [ProductoController::class, 'toggle'])
        ->name('productos.toggle');

    // Inventario — admin y empleado pueden ver el stock
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::get('/inventario/historial', [InventarioController::class, 'historial'])->name('inventario.historial');

    // Ventas — empleado puede ver y registrar; show para modal AJAX
    // [1] Excluir update y destroy: no existen en VentaController
    Route::resource('ventas', VentaController::class)
        ->except(['create', 'edit', 'update', 'destroy']);

    // Reportes — admin y empleado
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');

});

// ═══════════════════════════════════════════════════════════
//  GRUPO SOLO ADMINISTRADOR
// ═══════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:administrador', 'activo', 'no-cache'])->group(function () {

    // Categorías
    Route::resource('categorias', CategoriaController::class)
        ->except(['create', 'edit', 'show']);

    // [7] Entrada de stock — solo administrador
    Route::post('/inventario/movimiento', [InventarioController::class, 'movimiento'])
        ->name('inventario.movimiento');

    // [8] Anular venta — solo administrador
    Route::patch('ventas/{venta}/anular', [VentaController::class, 'anular'])
        ->name('ventas.anular');

    // Usuarios — solo administrador
    Route::resource('usuarios', UsuarioController::class)
        ->except(['create', 'edit', 'show']);
    Route::patch('usuarios/{usuario}/toggle', [UsuarioController::class, 'toggle'])
        ->name('usuarios.toggle');
    Route::patch('usuarios/{usuario}/reset-password', [UsuarioController::class, 'resetPassword'])
        ->name('usuarios.reset-password');

    // Configuración — solo administrador
    Route::get('/configuracion',  [ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::post('/configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');

});

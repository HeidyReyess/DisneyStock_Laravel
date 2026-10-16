<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoriaController;

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

    // Aquí irán: productos, inventario, ventas, reportes …

});

// ═══════════════════════════════════════════════════════════
//  GRUPO SOLO ADMINISTRADOR
// ═══════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:administrador', 'no-cache'])->group(function () {

    // Categorías (sin create/edit — formularios en modales inline)
    Route::resource('categorias', CategoriaController::class)
        ->except(['create', 'edit', 'show']);

    // Aquí irán: usuarios …

});

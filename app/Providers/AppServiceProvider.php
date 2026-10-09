<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Comparte $alertasStock con todas las vistas del layout sidebar.
        // Cuenta productos con stock_minimo > 0 y cantidad_stock <= stock_minimo.
        // Si la tabla no existe todavía, devuelve 0 sin lanzar error.
        View::composer('layouts.sidebar', function ($view) {
            try {
                $alertasStock = DB::table('inventarios')
                    ->join('productos', 'productos.id', '=', 'inventarios.producto_id')
                    ->whereColumn('inventarios.cantidad_stock', '<=', 'inventarios.stock_minimo')
                    ->where('inventarios.stock_minimo', '>', 0)
                    ->where('productos.estado', 'activo')
                    ->count();
            } catch (\Throwable $e) {
                $alertasStock = 0;
            }

            $view->with('alertasStock', $alertasStock);
        });
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user()->load('rol');

        // Fecha en español con Carbon
        $fechaHoy = ucfirst(Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY'));

        // Productos activos
        $productosActivos = DB::table('productos')
            ->where('estado', 'activo')
            ->count();

        // Ventas hoy
        $ventasHoy = DB::table('ventas')
            ->whereDate('fecha_venta', Carbon::today())
            ->where('estado', '!=', 'anulada')
            ->count();

        // Ingresos del mes actual
        $ingresosMes = DB::table('ventas')
            ->whereYear('fecha_venta', Carbon::now()->year)
            ->whereMonth('fecha_venta', Carbon::now()->month)
            ->where('estado', '!=', 'anulada')
            ->sum('total');

        // Productos con stock bajo o igual al mínimo (solo activos)
        $stockBajo = DB::table('inventarios')
            ->join('productos', 'productos.id', '=', 'inventarios.producto_id')
            ->whereColumn('inventarios.cantidad_stock', '<=', 'inventarios.stock_minimo')
            ->where('inventarios.stock_minimo', '>', 0)
            ->where('productos.estado', 'activo')
            ->count();

        return view('admin.dashboard', compact(
            'usuario',
            'fechaHoy',
            'productosActivos',
            'ventasHoy',
            'ingresosMes',
            'stockBajo'
        ));
    }
}

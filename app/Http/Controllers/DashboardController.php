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

        // Últimas 5 ventas no anuladas con usuario
        $ventasRecientes = \App\Models\Venta::with('user')
            ->where('estado', '!=', 'anulada')
            ->orderByDesc('fecha_venta')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        // Top 5 productos más vendidos (ventas no anuladas)
        $topProductos = DB::table('detalle_ventas')
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
            ->where('ventas.estado', '!=', 'anulada')
            ->select(
                'productos.nombre',
                DB::raw('SUM(detalle_ventas.cantidad) as total_cantidad'),
                DB::raw('SUM(detalle_ventas.subtotal) as total_vendido')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total_cantidad')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'usuario',
            'fechaHoy',
            'productosActivos',
            'ventasHoy',
            'ingresosMes',
            'stockBajo',
            'ventasRecientes',
            'topProductos'
        ));
    }
}

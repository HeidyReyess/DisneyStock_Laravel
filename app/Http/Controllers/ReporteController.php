<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Categoria;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    // GET /reportes
    public function index(Request $request)
    {
        $tipo    = $request->get('tipo', 'ventas');
        $periodo = $request->get('periodo', 'mes');

        // Calcular rango de fechas según período
        switch ($periodo) {
            case 'hoy':
                $desde = today()->toDateString();
                $hasta = today()->toDateString();
                break;
            case 'semana':
                $desde = now()->startOfWeek(Carbon::MONDAY)->toDateString();
                $hasta = today()->toDateString();
                break;
            case 'personalizado':
                $desde = $request->get('desde', now()->startOfMonth()->toDateString());
                $hasta = $request->get('hasta', today()->toDateString());
                break;
            case 'mes':
            default:
                $desde = now()->startOfMonth()->toDateString();
                $hasta = today()->toDateString();
                break;
        }

        // Movimientos siempre admiten rango manual
        if ($tipo === 'movimientos') {
            $desde = $request->get('desde', $desde);
            $hasta = $request->get('hasta', $hasta);
        }

        $datos           = [];
        $metricas        = null;
        $ventasPorDia    = [];
        $topProductos    = [];
        $ventasPorCategoria = [];

        // Filtros adicionales para ventas
        $empleadoId  = $request->get('empleado_id', '');
        $categoriaId = $request->get('categoria_id', '');

        switch ($tipo) {

            case 'ventas':
                $queryVentas = Venta::with(['factura', 'user'])
                    ->whereBetween('fecha_venta', [$desde, $hasta])
                    ->orderByDesc('fecha_venta');
                if ($empleadoId) $queryVentas->where('user_id', $empleadoId);
                if ($categoriaId) {
                    $queryVentas->whereHas('detalles.producto', function ($q) use ($categoriaId) {
                        $q->where('categoria_id', $categoriaId);
                    });
                }
                $datos = $queryVentas->get();

                $metricas = [
                    'total_ventas'      => $datos->where('estado', '!=', 'anulada')->sum('total'),
                    'num_transacciones' => $datos->where('estado', '!=', 'anulada')->count(),
                    'promedio_venta'    => $datos->where('estado', '!=', 'anulada')->avg('total') ?? 0,
                    'anuladas'          => $datos->where('estado', 'anulada')->count(),
                ];

                $ventasPorDiaQ = Venta::selectRaw('fecha_venta as dia, COUNT(*) as transacciones, COALESCE(SUM(total),0) as monto')
                    ->whereBetween('fecha_venta', [$desde, $hasta])
                    ->where('estado', '!=', 'anulada');
                if ($empleadoId) $ventasPorDiaQ->where('user_id', $empleadoId);
                $ventasPorDia = $ventasPorDiaQ->groupBy('fecha_venta')->orderBy('fecha_venta')->get();

                $topQ = DB::table('detalle_ventas as d')
                    ->join('productos as p', 'p.id', '=', 'd.producto_id')
                    ->join('ventas as v', 'v.id', '=', 'd.venta_id')
                    ->whereBetween('v.fecha_venta', [$desde, $hasta])
                    ->where('v.estado', '!=', 'anulada');
                if ($empleadoId) $topQ->where('v.user_id', $empleadoId);
                if ($categoriaId) $topQ->where('p.categoria_id', $categoriaId);
                $topProductos = $topQ
                    ->selectRaw('p.nombre, SUM(d.cantidad) as unidades, SUM(d.subtotal) as ingresos')
                    ->groupBy('p.id', 'p.nombre')
                    ->orderByDesc('unidades')
                    ->limit(5)
                    ->get();

                // Resumen por categoría
                $catQ = DB::table('detalle_ventas as d')
                    ->join('productos as p', 'p.id', '=', 'd.producto_id')
                    ->join('categorias as c', 'c.id', '=', 'p.categoria_id')
                    ->join('ventas as v', 'v.id', '=', 'd.venta_id')
                    ->whereBetween('v.fecha_venta', [$desde, $hasta])
                    ->where('v.estado', '!=', 'anulada');
                if ($empleadoId) $catQ->where('v.user_id', $empleadoId);
                if ($categoriaId) $catQ->where('p.categoria_id', $categoriaId);
                $ventasPorCategoria = $catQ
                    ->selectRaw('c.nombre_categoria, SUM(d.cantidad) as unidades, SUM(d.subtotal) as total')
                    ->groupBy('c.id', 'c.nombre_categoria')
                    ->orderByDesc('total')
                    ->get();
                break;

            case 'inventario':
                $datos = Producto::with(['categoria', 'inventario'])
                    ->where('estado', 'activo')
                    ->orderBy('nombre')
                    ->get();
                break;

            case 'stock_bajo':
                $datos = Producto::with(['categoria', 'inventario'])
                    ->where('estado', 'activo')
                    ->whereHas('inventario', function ($q) {
                        $q->whereColumn('cantidad_stock', '<=', 'stock_minimo')
                          ->where('stock_minimo', '>', 0);
                    })
                    ->orderBy('nombre')
                    ->get();
                break;

            case 'movimientos':
                $idProd = $request->get('producto_id', 0);
                $query  = MovimientoInventario::with(['producto', 'user'])
                    ->whereBetween('fecha', [$desde, $hasta])
                    ->orderByDesc('fecha')
                    ->orderByDesc('id');

                if ($idProd) {
                    $query->where('producto_id', $idProd);
                }
                $datos = $query->get();
                break;
        }

        $productosLista = Producto::where('estado', 'activo')->orderBy('nombre')->get();
        $empleadosLista = \App\Models\User::orderBy('nombre')->get();
        $categoriasLista = \App\Models\Categoria::orderBy('nombre_categoria')->get();

        return view('admin.reportes.index', compact(
            'tipo', 'periodo', 'desde', 'hasta',
            'datos', 'metricas', 'ventasPorDia', 'topProductos',
            'ventasPorCategoria',
            'productosLista', 'empleadosLista', 'categoriasLista',
            'empleadoId', 'categoriaId'
        ));
    }
}

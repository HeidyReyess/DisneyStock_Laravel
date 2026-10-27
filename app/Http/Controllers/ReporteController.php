<?php

namespace App\Http\Controllers;

use App\Exports\ReporteExport;
use App\Models\Categoria;
use App\Models\Configuracion;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    // GET /reportes
    public function index(Request $request)
    {
        $reporte = $this->obtenerDatosReporte($request);

        if ($reporte['rangoInvalido']) {
            return view('admin.reportes.index', $reporte);
        }

        return view('admin.reportes.index', $reporte);
    }

    // GET /reportes/export/pdf — solo administrador
    public function exportPdf(Request $request)
    {
        if (!Auth::user()->esAdmin()) {
            abort(403, 'No tienes permiso para exportar reportes.');
        }

        $reporte  = $this->obtenerDatosReporte($request);
        $tipo     = $reporte['tipo'];
        $fecha    = today()->format('Y-m-d');
        $fileName = "reporte_{$tipo}_{$fecha}.pdf";

        // Generar PDF con DomPDF
        $pdf = Pdf::loadView('admin.reportes.pdf', $reporte)
            ->setPaper('a4', 'portrait');

        return $pdf->download($fileName);
    }

    // GET /reportes/export/excel — solo administrador
    public function exportExcel(Request $request)
    {
        if (!Auth::user()->esAdmin()) {
            abort(403, 'No tienes permiso para exportar reportes.');
        }

        $reporte  = $this->obtenerDatosReporte($request);
        $tipo     = $reporte['tipo'];
        $fecha    = today()->format('Y-m-d');
        $fileName = "reporte_{$tipo}_{$fecha}.xlsx";

        return Excel::download(new ReporteExport($tipo, $reporte), $fileName);
    }

    /**
     * Obtiene y prepara los datos del reporte según los filtros recibidos.
     */
    protected function obtenerDatosReporte(Request $request): array
    {
        $tipo    = $request->get('tipo', 'ventas');
        $periodo = $request->get('periodo', 'mes');

        // Calcular rango de fechas según período
        switch ($periodo) {
            case 'hoy':
                $desde = today()->toDateString();
                $hasta = today()->toDateString();
                $textoPeriodo = 'Hoy (' . Carbon::parse($desde)->format('d/m/Y') . ')';
                break;
            case 'semana':
                $desde = now()->startOfWeek(Carbon::MONDAY)->toDateString();
                $hasta = today()->toDateString();
                $textoPeriodo = 'Esta semana (' . Carbon::parse($desde)->format('d/m/Y') . ' al ' . Carbon::parse($hasta)->format('d/m/Y') . ')';
                break;
            case 'personalizado':
                $desde = $request->get('desde', now()->startOfMonth()->toDateString());
                $hasta = $request->get('hasta', today()->toDateString());
                $textoPeriodo = Carbon::parse($desde)->format('d/m/Y') . ($desde !== $hasta ? ' al ' . Carbon::parse($hasta)->format('d/m/Y') : '');
                break;
            case 'mes':
            default:
                $desde = now()->startOfMonth()->toDateString();
                $hasta = today()->toDateString();
                $textoPeriodo = 'Este mes (' . Carbon::parse($desde)->format('d/m/Y') . ' al ' . Carbon::parse($hasta)->format('d/m/Y') . ')';
                break;
        }

        // Movimientos siempre admiten rango manual
        if ($tipo === 'movimientos') {
            $desde = $request->get('desde', $desde);
            $hasta = $request->get('hasta', $hasta);
            $textoPeriodo = Carbon::parse($desde)->format('d/m/Y') . ($desde !== $hasta ? ' al ' . Carbon::parse($hasta)->format('d/m/Y') : '');
        }

        // Para inventario y stock bajo, el período es la fecha actual
        if (in_array($tipo, ['inventario', 'stock_bajo'])) {
            $textoPeriodo = 'Inventario actual (' . now()->format('d/m/Y') . ')';
        }

        // Validar rango de fechas
        $rangoInvalido = ($periodo === 'personalizado' || $tipo === 'movimientos')
            && $request->filled('desde') && $request->filled('hasta')
            && $request->desde > $request->hasta;

        $productosLista  = Producto::where('estado', 'activo')->orderBy('nombre')->get();
        $empleadosLista  = User::orderBy('nombre')->get();
        $categoriasLista = Categoria::orderBy('nombre_categoria')->get();

        $empleadoId  = $request->get('empleado_id', '');
        $categoriaId = $request->get('categoria_id', '');
        $productoId  = $request->get('producto_id', '');

        $filtroEmpleadoTexto  = $empleadoId ? ($empleadosLista->firstWhere('id', $empleadoId)?->nombre ?? '') : '';
        $filtroCategoriaTexto = $categoriaId ? ($categoriasLista->firstWhere('id', $categoriaId)?->nombre_categoria ?? '') : '';
        $filtroProductoTexto  = $productoId ? ($productosLista->firstWhere('id', $productoId)?->nombre ?? '') : '';

        $nombreNegocio = Configuracion::valor('nombre_negocio', 'DisneyStock');

        $titulos = [
            'ventas'      => 'Reporte de Ventas',
            'inventario'  => 'Reporte de Inventario Valorizado',
            'stock_bajo'  => 'Reporte de Productos con Stock Bajo',
            'movimientos' => 'Reporte de Movimientos de Inventario',
        ];
        $tituloReporte   = $titulos[$tipo] ?? 'Reporte';
        $fechaGeneracion = now()->format('d/m/Y H:i');

        if ($rangoInvalido) {
            return [
                'tipo'                 => $tipo,
                'periodo'              => $periodo,
                'desde'                => $desde,
                'hasta'                => $hasta,
                'textoPeriodo'         => $textoPeriodo,
                'datos'                => collect(),
                'metricas'             => null,
                'ventasPorDia'         => collect(),
                'topProductos'         => collect(),
                'ventasPorCategoria'   => collect(),
                'productosLista'       => $productosLista,
                'empleadosLista'       => $empleadosLista,
                'categoriasLista'      => $categoriasLista,
                'empleadoId'           => $empleadoId,
                'categoriaId'          => $categoriaId,
                'productoId'           => $productoId,
                'filtroEmpleadoTexto'  => $filtroEmpleadoTexto,
                'filtroCategoriaTexto' => $filtroCategoriaTexto,
                'filtroProductoTexto'  => $filtroProductoTexto,
                'nombreNegocio'        => $nombreNegocio,
                'tituloReporte'        => $tituloReporte,
                'fechaGeneracion'      => $fechaGeneracion,
                'rangoInvalido'        => true,
            ];
        }

        $datos              = collect();
        $metricas           = null;
        $ventasPorDia       = collect();
        $topProductos       = collect();
        $ventasPorCategoria = collect();

        switch ($tipo) {

            case 'ventas':
                $queryVentas = Venta::with(['factura', 'user'])
                    ->whereBetween('fecha_venta', [$desde, $hasta])
                    ->orderByDesc('fecha_venta');
                if ($empleadoId) {
                    $queryVentas->where('user_id', $empleadoId);
                }
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
                if ($empleadoId) {
                    $ventasPorDiaQ->where('user_id', $empleadoId);
                }
                $ventasPorDia = $ventasPorDiaQ->groupBy('fecha_venta')->orderBy('fecha_venta')->get();

                $topQ = DB::table('detalle_ventas as d')
                    ->join('productos as p', 'p.id', '=', 'd.producto_id')
                    ->join('ventas as v', 'v.id', '=', 'd.venta_id')
                    ->whereBetween('v.fecha_venta', [$desde, $hasta])
                    ->where('v.estado', '!=', 'anulada');
                if ($empleadoId) {
                    $topQ->where('v.user_id', $empleadoId);
                }
                if ($categoriaId) {
                    $topQ->where('p.categoria_id', $categoriaId);
                }
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
                if ($empleadoId) {
                    $catQ->where('v.user_id', $empleadoId);
                }
                if ($categoriaId) {
                    $catQ->where('p.categoria_id', $categoriaId);
                }
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

        return [
            'tipo'                 => $tipo,
            'periodo'              => $periodo,
            'desde'                => $desde,
            'hasta'                => $hasta,
            'textoPeriodo'         => $textoPeriodo,
            'datos'                => $datos,
            'metricas'             => $metricas,
            'ventasPorDia'         => $ventasPorDia,
            'topProductos'         => $topProductos,
            'ventasPorCategoria'   => $ventasPorCategoria,
            'productosLista'       => $productosLista,
            'empleadosLista'       => $empleadosLista,
            'categoriasLista'      => $categoriasLista,
            'empleadoId'           => $empleadoId,
            'categoriaId'          => $categoriaId,
            'productoId'           => $productoId,
            'filtroEmpleadoTexto'  => $filtroEmpleadoTexto,
            'filtroCategoriaTexto' => $filtroCategoriaTexto,
            'filtroProductoTexto'  => $filtroProductoTexto,
            'nombreNegocio'        => $nombreNegocio,
            'tituloReporte'        => $tituloReporte,
            'fechaGeneracion'      => $fechaGeneracion,
            'rangoInvalido'        => false,
        ];
    }
}

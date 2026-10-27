<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $tituloReporte }}</title>
    <style>
        @page {
            margin: 20px 25px;
        }
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #1E1B4B;
            line-height: 1.4;
        }
        .header {
            border-bottom: 2px solid #7C3AED;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .negocio-nombre {
            font-size: 16px;
            font-weight: bold;
            color: #4A1D96;
            text-transform: uppercase;
        }
        .doc-title {
            font-size: 13px;
            font-weight: bold;
            color: #1E1B4B;
            margin-top: 2px;
        }
        .meta-info {
            font-size: 9px;
            color: #6B7280;
            text-align: right;
            line-height: 1.35;
        }
        .periodo-badge {
            display: inline-block;
            background: #F5F3FF;
            color: #5B21B6;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            margin-top: 4px;
        }
        /* Métricas */
        .metrics-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .metric-cell {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            padding: 8px 10px;
            text-align: center;
            width: 25%;
        }
        .metric-val {
            font-size: 13px;
            font-weight: bold;
            color: #4A1D96;
        }
        .metric-lbl {
            font-size: 8px;
            color: #6B7280;
            text-transform: uppercase;
            margin-top: 2px;
        }
        /* Tablas */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #4A1D96;
            margin: 12px 0 6px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .data-table th {
            background: #F5F3FF;
            color: #5B21B6;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6px 8px;
            border-bottom: 1.5px solid #DDD6FE;
            text-align: left;
        }
        .data-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #F3F4F6;
            font-size: 9px;
            color: #374151;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) td {
            background: #FAFAFA;
        }
        .data-table tfoot td {
            background: #EDE9FE;
            color: #4A1D96;
            font-weight: bold;
            border-top: 1.5px solid #DDD6FE;
            font-size: 9px;
            padding: 6px 8px;
        }
        .text-left   { text-align: left; }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-completada, .badge-ok, .badge-entrada { background: #D1FAE5; color: #065F46; }
        .badge-anulada, .badge-bajo, .badge-salida   { background: #FEE2E2; color: #991B1B; }
        .badge-ajuste                                { background: #EDE9FE; color: #5B21B6; }
        .empty-alert {
            background: #F9FAFB;
            border: 1px dashed #D1D5DB;
            color: #6B7280;
            padding: 24px;
            text-align: center;
            font-size: 11px;
            margin: 20px 0;
            border-radius: 6px;
        }
        .footer {
            margin-top: 16px;
            text-align: center;
            font-size: 8px;
            color: #9CA3AF;
            border-top: 1px solid #E5E7EB;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    {{-- Encabezado --}}
    <div class="header">
        <table>
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <div class="negocio-nombre">{{ $nombreNegocio }}</div>
                    <div class="doc-title">{{ $tituloReporte }}</div>
                    <div class="periodo-badge">Período: {{ $textoPeriodo }}</div>
                </td>
                <td style="width: 40%; vertical-align: top;" class="meta-info">
                    <div><strong>Fecha y hora de emisión:</strong> {{ $fechaGeneracion }}</div>
                    <div><strong>Generado por:</strong> {{ Auth::user()->nombre ?? 'Administrador' }}</div>
                    @if(!empty($filtroEmpleadoTexto))
                        <div><strong>Empleado:</strong> {{ $filtroEmpleadoTexto }}</div>
                    @endif
                    @if(!empty($filtroCategoriaTexto))
                        <div><strong>Categoría:</strong> {{ $filtroCategoriaTexto }}</div>
                    @endif
                    @if(!empty($filtroProductoTexto))
                        <div><strong>Producto:</strong> {{ $filtroProductoTexto }}</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Caso sin datos --}}
    @if($datos->isEmpty())
        <div class="empty-alert">
            No hay información para el período seleccionado
        </div>
    @else

        {{-- ══════════════════════════════ VENTAS ══════════════════════════════ --}}
        @if($tipo === 'ventas')

            {{-- Métricas resumen --}}
            @if($metricas)
            <table class="metrics-table">
                <tr>
                    <td class="metric-cell">
                        <div class="metric-val">$ {{ number_format($metricas['total_ventas'], 0, ',', '.') }}</div>
                        <div class="metric-lbl">Total ingresos</div>
                    </td>
                    <td class="metric-cell">
                        <div class="metric-val">{{ $metricas['num_transacciones'] }}</div>
                        <div class="metric-lbl">Ventas completadas</div>
                    </td>
                    <td class="metric-cell">
                        <div class="metric-val">$ {{ number_format($metricas['promedio_venta'], 0, ',', '.') }}</div>
                        <div class="metric-lbl">Promedio por venta</div>
                    </td>
                    <td class="metric-cell">
                        <div class="metric-val">{{ $metricas['anuladas'] }}</div>
                        <div class="metric-lbl">Ventas anuladas</div>
                    </td>
                </tr>
            </table>
            @endif

            {{-- Tabla detalle de ventas --}}
            <div class="section-title">Detalle de ventas ({{ $datos->count() }} registros)</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 14%;">Comprobante</th>
                        <th style="width: 22%;">Vendedor</th>
                        <th style="width: 13%;" class="text-right">Subtotal</th>
                        <th style="width: 13%;" class="text-right">Descuento</th>
                        <th style="width: 14%;" class="text-right">Total</th>
                        <th style="width: 12%;" class="text-center">Estado</th>
                        <th style="width: 12%;" class="text-center">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos as $v)
                    <tr>
                        <td style="font-weight: bold; color: #4A1D96;">{{ $v->factura?->numero ?? ('DS-' . str_pad($v->id, 6, '0', STR_PAD_LEFT)) }}</td>
                        <td>{{ $v->user?->nombre ?? '—' }}</td>
                        <td class="text-right">$ {{ number_format($v->subtotal, 0, ',', '.') }}</td>
                        <td class="text-right">{{ $v->descuento > 0 ? '$ ' . number_format($v->descuento, 0, ',', '.') : '—' }}</td>
                        <td class="text-right" style="font-weight: bold;">$ {{ number_format($v->total, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge badge-{{ $v->estado }}">{{ ucfirst($v->estado) }}</span>
                        </td>
                        <td class="text-center">{{ $v->fecha_venta->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align: right;">TOTALES (sin anuladas):</td>
                        <td class="text-right">$ {{ number_format($datos->where('estado', '!=', 'anulada')->sum('subtotal'), 0, ',', '.') }}</td>
                        <td class="text-right">$ {{ number_format($datos->where('estado', '!=', 'anulada')->sum('descuento'), 0, ',', '.') }}</td>
                        <td class="text-right">$ {{ number_format($datos->where('estado', '!=', 'anulada')->sum('total'), 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>

            {{-- Ventas por categoría si existen --}}
            @if($ventasPorCategoria && $ventasPorCategoria->count() > 0)
            <div class="section-title">Resumen de ventas por categoría</div>
            <table class="data-table" style="width: 70%;">
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th class="text-center" style="width: 30%;">Unidades vendidas</th>
                        <th class="text-right" style="width: 35%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventasPorCategoria as $vc)
                    <tr>
                        <td style="font-weight: bold;">{{ $vc->nombre_categoria }}</td>
                        <td class="text-center">{{ number_format($vc->unidades, 0, ',', '.') }}</td>
                        <td class="text-right">$ {{ number_format($vc->total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td style="text-align: right;">TOTAL:</td>
                        <td class="text-center">{{ number_format($ventasPorCategoria->sum('unidades'), 0, ',', '.') }}</td>
                        <td class="text-right">$ {{ number_format($ventasPorCategoria->sum('total'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
            @endif

        {{-- ══════════════════════════════ INVENTARIO ══════════════════════════════ --}}
        @elseif($tipo === 'inventario')

            <div class="section-title">Inventario valorizado ({{ $datos->count() }} productos activos)</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Producto</th>
                        <th style="width: 18%;">Categoría</th>
                        <th style="width: 10%;" class="text-center">Stock</th>
                        <th style="width: 10%;" class="text-center">Mínimo</th>
                        <th style="width: 11%;" class="text-center">Estado</th>
                        <th style="width: 13%;" class="text-right">Valor costo</th>
                        <th style="width: 13%;" class="text-right">Valor venta</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos as $prod)
                    @php
                        $stock  = $prod->inventario?->cantidad_stock ?? 0;
                        $minimo = $prod->inventario?->stock_minimo   ?? 0;
                        $bajo   = $minimo > 0 && $stock <= $minimo;
                    @endphp
                    <tr>
                        <td style="font-weight: bold;">{{ $prod->nombre }}</td>
                        <td>{{ $prod->categoria?->nombre_categoria ?? '—' }}</td>
                        <td class="text-center" style="font-weight: bold; color: {{ $bajo ? '#DC2626' : '#1E1B4B' }};">{{ $stock }}</td>
                        <td class="text-center">{{ $minimo }}</td>
                        <td class="text-center">
                            <span class="badge {{ $bajo ? 'badge-bajo' : 'badge-ok' }}">{{ $bajo ? 'Bajo' : 'Normal' }}</span>
                        </td>
                        <td class="text-right">$ {{ number_format($stock * $prod->precio_compra, 0, ',', '.') }}</td>
                        <td class="text-right" style="font-weight: bold;">$ {{ number_format($stock * $prod->precio_venta, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" style="text-align: right;">TOTAL VALORIZADO:</td>
                        <td class="text-right">
                            $ {{ number_format($datos->sum(fn($p) => ($p->inventario?->cantidad_stock ?? 0) * $p->precio_compra), 0, ',', '.') }}
                        </td>
                        <td class="text-right">
                            $ {{ number_format($datos->sum(fn($p) => ($p->inventario?->cantidad_stock ?? 0) * $p->precio_venta), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>

        {{-- ══════════════════════════════ STOCK BAJO ══════════════════════════════ --}}
        @elseif($tipo === 'stock_bajo')

            <div class="section-title">Productos con stock bajo ({{ $datos->count() }} necesitan reposición)</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 28%;">Producto</th>
                        <th style="width: 20%;">Categoría</th>
                        <th style="width: 12%;" class="text-center">Stock actual</th>
                        <th style="width: 12%;" class="text-center">Stock mínimo</th>
                        <th style="width: 12%;" class="text-center">Faltante</th>
                        <th style="width: 16%;">Proveedor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos as $prod)
                    @php
                        $stock    = $prod->inventario?->cantidad_stock ?? 0;
                        $minimo   = $prod->inventario?->stock_minimo   ?? 0;
                        $faltante = max(0, $minimo - $stock);
                    @endphp
                    <tr>
                        <td style="font-weight: bold;">{{ $prod->nombre }}</td>
                        <td>{{ $prod->categoria?->nombre_categoria ?? '—' }}</td>
                        <td class="text-center" style="font-weight: bold; color: #DC2626;">{{ $stock }}</td>
                        <td class="text-center">{{ $minimo }}</td>
                        <td class="text-center">
                            <span class="badge badge-bajo">{{ $faltante }}</span>
                        </td>
                        <td>{{ $prod->proveedor ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        {{-- ══════════════════════════════ MOVIMIENTOS ══════════════════════════════ --}}
        @elseif($tipo === 'movimientos')

            <div class="section-title">Historial de movimientos ({{ $datos->count() }} registros)</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 22%;">Producto</th>
                        <th style="width: 12%;" class="text-center">Tipo</th>
                        <th style="width: 10%;" class="text-center">Cantidad</th>
                        <th style="width: 22%;">Descripción</th>
                        <th style="width: 14%;">Proveedor</th>
                        <th style="width: 10%;">Usuario</th>
                        <th style="width: 10%;" class="text-center">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos as $mov)
                    <tr>
                        <td style="font-weight: bold;">{{ $mov->producto?->nombre ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge badge-{{ $mov->tipo_movimiento }}">{{ ucfirst($mov->tipo_movimiento) }}</span>
                        </td>
                        <td class="text-center" style="font-weight: bold; color: {{ $mov->tipo_movimiento === 'entrada' ? '#059669' : '#DC2626' }};">
                            {{ $mov->tipo_movimiento === 'entrada' ? '+' : '-' }}{{ $mov->cantidad }}
                        </td>
                        <td>{{ $mov->descripcion ?? '—' }}</td>
                        <td>{{ $mov->proveedor ?? '—' }}</td>
                        <td>{{ $mov->user?->nombre ?? 'Sistema' }}</td>
                        <td class="text-center">{{ $mov->fecha?->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        @endif

    @endif

    {{-- Pie de página --}}
    <div class="footer">
        DisneyStock — Documento generado automáticamente el {{ $fechaGeneracion }}
    </div>

</body>
</html>

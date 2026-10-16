@extends('layouts.sidebar')

@section('title', 'Reportes')
@section('page-title', 'Reportes')

@section('content')
<style>
    /* ── Tabs de tipo ── */
    .tabs { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:22px; }
    .tab {
        padding:9px 18px; border-radius:10px; font-size:.86rem; font-weight:600;
        text-decoration:none; border:1.5px solid #DDD6FE;
        display:inline-flex; align-items:center; gap:7px; transition:all .2s;
    }
    .tab.active { background:#4A1D96; color:#fff; border-color:#4A1D96; }
    .tab:not(.active) { background:#F5F3FF; color:#5B21B6; }
    .tab:not(.active):hover { background:#EDE9FE; }

    /* ── Filtros ── */
    .filtros-bar {
        background:#fff; border:1px solid #EDE9FE; border-radius:12px;
        padding:16px 20px; margin-bottom:20px;
        display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;
    }
    .filtro-group { display:flex; flex-direction:column; gap:4px; }
    .filtro-label { font-size:.76rem; font-weight:600; color:#6B7280; text-transform:uppercase; letter-spacing:.06em; }
    .filtro-input, .filtro-select {
        padding:8px 12px; border:1.5px solid #E5E7EB; border-radius:9px;
        font-size:.86rem; color:#1E1B4B; background:#FAFAFA;
        transition:border-color .2s; min-width:130px;
    }
    .filtro-input:focus, .filtro-select:focus { outline:none; border-color:#7C3AED; background:#fff; }
    .btn-filtrar { padding:9px 18px; background:#4A1D96; color:#fff; border:2px solid #4A1D96; border-radius:9px; font-size:.86rem; font-weight:700; cursor:pointer; transition:all .2s; align-self:flex-end; display:inline-flex; align-items:center; gap:6px; }
    .btn-filtrar:hover { background:#7C3AED; border-color:#7C3AED; }

    /* ── Métricas ── */
    .metrics-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:14px; margin-bottom:20px; }
    .metric-card { background:#fff; border-radius:12px; padding:16px 18px; border:1px solid #EDE9FE; box-shadow:0 2px 8px rgba(74,29,150,.05); }
    .metric-val { font-family:'Outfit',sans-serif; font-size:1.45rem; font-weight:800; color:#1E1B4B; line-height:1; }
    .metric-lbl { font-size:.77rem; color:#6B7280; margin-top:4px; }

    /* ── Grid ventas ── */
    .grid-rep { display:grid; grid-template-columns:1fr 320px; gap:18px; margin-bottom:20px; }
    @media(max-width:1000px) { .grid-rep { grid-template-columns:1fr; } }

    /* ── Tabla ── */
    .table-wrap { overflow-x:auto; }
    table { width:100%; border-collapse:collapse; font-size:.86rem; }
    thead th { padding:11px 14px; text-align:left; font-size:.7rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#6B7280; border-bottom:2px solid #F5F3FF; white-space:nowrap; }
    tbody td { padding:11px 14px; border-bottom:1px solid #F9F5FF; color:#374151; vertical-align:middle; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover td { background:#FAFAFF; }

    /* ── Badges ── */
    .badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:20px; font-size:.73rem; font-weight:600; }
    .badge-completada { background:#D1FAE5; color:#065F46; }
    .badge-anulada    { background:#FEE2E2; color:#991B1B; }
    .badge-ok         { background:#EDE9FE; color:#5B21B6; }
    .badge-bajo       { background:#FEE2E2; color:#991B1B; }
    .badge-entrada    { background:#D1FAE5; color:#059669; }
    .badge-salida     { background:#FEE2E2; color:#DC2626; }
    .badge-ajuste     { background:#EDE9FE; color:#7C3AED; }

    /* ── Top productos ── */
    .top-item { display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid #F5F3FF; }
    .top-item:last-child { border-bottom:none; }
    .top-rank { width:26px; height:26px; border-radius:50%; background:#EDE9FE; color:#5B21B6; font-size:.78rem; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .top-nombre { flex:1; font-size:.86rem; font-weight:600; color:#1E1B4B; }
    .top-dato { font-size:.8rem; color:#6B7280; }

    /* ── Días tabla ── */
    .dias-item { display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid #F5F3FF; font-size:.84rem; }
    .dias-item:last-child { border-bottom:none; }
    .dias-bar-wrap { flex:1; background:#F3F4F6; border-radius:4px; height:6px; overflow:hidden; }
    .dias-bar { height:6px; background:#7C3AED; border-radius:4px; }

    /* ── Empty ── */
    .empty-state { text-align:center; padding:40px; color:#9CA3AF; }
    .empty-state i { font-size:2rem; color:#C4B5FD; display:block; margin-bottom:10px; }
    .empty-state p { font-size:.87rem; }

    /* ── Stock bar ── */
    .stock-bar-bg { background:#F3F4F6; border-radius:4px; height:5px; }
    .stock-bar-fill { height:5px; border-radius:4px; }
    .stock-bar-fill.ok  { background:#7C3AED; }
    .stock-bar-fill.low { background:#EF4444; }
</style>

{{-- Tabs de tipo de reporte --}}
<div class="tabs">
    <a href="{{ route('reportes.index', ['tipo' => 'ventas',     'periodo' => $periodo]) }}"
       class="tab {{ $tipo === 'ventas'     ? 'active' : '' }}">
        <i class="fas fa-receipt"></i> Ventas
    </a>
    <a href="{{ route('reportes.index', ['tipo' => 'inventario', 'periodo' => $periodo]) }}"
       class="tab {{ $tipo === 'inventario' ? 'active' : '' }}">
        <i class="fas fa-boxes-stacked"></i> Inventario
    </a>
    <a href="{{ route('reportes.index', ['tipo' => 'stock_bajo', 'periodo' => $periodo]) }}"
       class="tab {{ $tipo === 'stock_bajo' ? 'active' : '' }}">
        <i class="fas fa-triangle-exclamation"></i> Stock bajo
    </a>
    <a href="{{ route('reportes.index', ['tipo' => 'movimientos','periodo' => $periodo]) }}"
       class="tab {{ $tipo === 'movimientos'? 'active' : '' }}">
        <i class="fas fa-clock-rotate-left"></i> Movimientos
    </a>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('reportes.index') }}">
    <input type="hidden" name="tipo" value="{{ $tipo }}">
    <div class="filtros-bar">

        @if($tipo === 'ventas')
            <div class="filtro-group">
                <span class="filtro-label">Período</span>
                <select name="periodo" class="filtro-select" onchange="this.form.submit()">
                    <option value="hoy"    {{ $periodo === 'hoy'    ? 'selected' : '' }}>Hoy</option>
                    <option value="semana" {{ $periodo === 'semana' ? 'selected' : '' }}>Esta semana</option>
                    <option value="mes"    {{ $periodo === 'mes'    ? 'selected' : '' }}>Este mes</option>
                </select>
            </div>
        @endif

        @if($tipo === 'movimientos')
            <div class="filtro-group">
                <span class="filtro-label">Desde</span>
                <input type="date" name="desde" class="filtro-input" value="{{ $desde }}">
            </div>
            <div class="filtro-group">
                <span class="filtro-label">Hasta</span>
                <input type="date" name="hasta" class="filtro-input" value="{{ $hasta }}">
            </div>
            <div class="filtro-group">
                <span class="filtro-label">Producto</span>
                <select name="producto_id" class="filtro-select">
                    <option value="">Todos</option>
                    @foreach($productosLista as $p)
                        <option value="{{ $p->id }}" {{ request('producto_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-filtrar"><i class="fas fa-filter"></i> Filtrar</button>
        @endif

        {{-- Rango mostrado --}}
        <div style="margin-left:auto; font-size:.8rem; color:#9CA3AF; align-self:center;">
            {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}
            @if($desde !== $hasta)
                — {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
            @endif
        </div>
    </div>
</form>

{{-- ══════════════════════════════════════════
     REPORTE: VENTAS
══════════════════════════════════════════ --}}
@if($tipo === 'ventas')

    {{-- Métricas --}}
    @if($metricas)
    <div class="metrics-row">
        <div class="metric-card">
            <div class="metric-val">$ {{ number_format($metricas['total_ventas'], 0, ',', '.') }}</div>
            <div class="metric-lbl">Total ingresos</div>
        </div>
        <div class="metric-card">
            <div class="metric-val">{{ $metricas['num_transacciones'] }}</div>
            <div class="metric-lbl">Ventas completadas</div>
        </div>
        <div class="metric-card">
            <div class="metric-val">$ {{ number_format($metricas['promedio_venta'], 0, ',', '.') }}</div>
            <div class="metric-lbl">Promedio por venta</div>
        </div>
        <div class="metric-card">
            <div class="metric-val">{{ $metricas['anuladas'] }}</div>
            <div class="metric-lbl">Ventas anuladas</div>
        </div>
    </div>
    @endif

    <div class="grid-rep">

        {{-- Tabla de ventas --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-receipt" style="color:#7C3AED;margin-right:8px;"></i>Detalle de ventas</span>
                <span style="font-size:.8rem; color:#9CA3AF;">{{ $datos->count() }} registros</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Factura</th>
                            <th>Vendedor</th>
                            <th>Subtotal</th>
                            <th>Descuento</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($datos as $v)
                        <tr>
                            <td style="font-weight:600; color:#4A1D96;">{{ $v->factura?->numero ?? '—' }}</td>
                            <td style="color:#6B7280;">{{ $v->user?->nombre ?? '—' }}</td>
                            <td>$ {{ number_format($v->subtotal, 0, ',', '.') }}</td>
                            <td>{{ $v->descuento > 0 ? '$ '.number_format($v->descuento,0,',','.') : '—' }}</td>
                            <td style="font-weight:700;">$ {{ number_format($v->total, 0, ',', '.') }}</td>
                            <td><span class="badge badge-{{ $v->estado }}">{{ ucfirst($v->estado) }}</span></td>
                            <td style="color:#9CA3AF; font-size:.8rem;">{{ $v->fecha_venta->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" style="text-align:center; padding:32px; color:#9CA3AF;">Sin ventas en este período.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Panel lateral --}}
        <div style="display:flex; flex-direction:column; gap:18px;">

            {{-- Top 5 productos --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-trophy" style="color:#7C3AED;margin-right:8px;"></i>Top 5 productos</span>
                </div>
                <div class="card-body">
                    @forelse($topProductos as $i => $top)
                    <div class="top-item">
                        <div class="top-rank">{{ $i + 1 }}</div>
                        <div style="flex:1;">
                            <div class="top-nombre">{{ $top->nombre }}</div>
                            <div class="top-dato">{{ $top->unidades }} unidades · $ {{ number_format($top->ingresos, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state" style="padding:16px;">
                        <i class="fas fa-trophy"></i><p>Sin datos.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Ventas por día --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-chart-bar" style="color:#7C3AED;margin-right:8px;"></i>Por día</span>
                </div>
                <div class="card-body">
                    @php $maxMonto = $ventasPorDia->max('monto') ?: 1; @endphp
                    @forelse($ventasPorDia as $dia)
                    <div class="dias-item">
                        <div style="width:68px; font-size:.78rem; color:#6B7280; flex-shrink:0;">
                            {{ \Carbon\Carbon::parse($dia->dia)->format('d/m') }}
                        </div>
                        <div class="dias-bar-wrap">
                            <div class="dias-bar" style="width:{{ round(($dia->monto/$maxMonto)*100) }}%"></div>
                        </div>
                        <div style="font-size:.78rem; font-weight:600; color:#1E1B4B; min-width:70px; text-align:right;">
                            $ {{ number_format($dia->monto, 0, ',', '.') }}
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center; padding:16px; color:#9CA3AF; font-size:.84rem;">Sin ventas en este período.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

{{-- ══════════════════════════════════════════
     REPORTE: INVENTARIO VALORIZADO
══════════════════════════════════════════ --}}
@elseif($tipo === 'inventario')
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-boxes-stacked" style="color:#7C3AED;margin-right:8px;"></i>Inventario valorizado</span>
            <span style="font-size:.8rem; color:#9CA3AF;">{{ $datos->count() }} productos activos</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Stock</th>
                        <th>Mínimo</th>
                        <th>Estado stock</th>
                        <th>Valor costo</th>
                        <th>Valor venta</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($datos as $prod)
                    @php
                        $stock  = $prod->inventario?->cantidad_stock ?? 0;
                        $minimo = $prod->inventario?->stock_minimo   ?? 0;
                        $bajo   = $minimo > 0 && $stock <= $minimo;
                        $pct    = $minimo > 0 ? min(100, round(($stock/($minimo*2))*100)) : ($stock > 0 ? 100 : 0);
                    @endphp
                    <tr>
                        <td style="font-weight:600; color:#1E1B4B;">{{ $prod->nombre }}</td>
                        <td style="color:#6B7280; font-size:.82rem;">{{ $prod->categoria?->nombre_categoria ?? '—' }}</td>
                        <td>
                            <div style="display:flex; flex-direction:column; gap:3px; min-width:90px;">
                                <div class="stock-bar-bg">
                                    <div class="stock-bar-fill {{ $bajo ? 'low' : 'ok' }}" style="width:{{ $pct }}%"></div>
                                </div>
                                <span style="font-size:.78rem; font-weight:700; color:{{ $bajo ? '#DC2626' : '#4A1D96' }};">{{ $stock }}</span>
                            </div>
                        </td>
                        <td style="color:#9CA3AF; font-size:.82rem;">{{ $minimo }}</td>
                        <td><span class="badge {{ $bajo ? 'badge-bajo' : 'badge-ok' }}">{{ $bajo ? 'Bajo' : 'Normal' }}</span></td>
                        <td>$ {{ number_format($stock * $prod->precio_compra, 0, ',', '.') }}</td>
                        <td style="font-weight:600;">$ {{ number_format($stock * $prod->precio_venta, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center; padding:32px; color:#9CA3AF;">Sin productos activos.</td></tr>
                    @endforelse
                </tbody>
                @if($datos->count() > 0)
                <tfoot>
                    <tr style="background:#F5F3FF;">
                        <td colspan="5" style="padding:10px 14px; font-weight:700; color:#4A1D96; font-size:.84rem;">Total</td>
                        <td style="padding:10px 14px; font-weight:700;">
                            $ {{ number_format($datos->sum(fn($p) => ($p->inventario?->cantidad_stock ?? 0) * $p->precio_compra), 0, ',', '.') }}
                        </td>
                        <td style="padding:10px 14px; font-weight:700;">
                            $ {{ number_format($datos->sum(fn($p) => ($p->inventario?->cantidad_stock ?? 0) * $p->precio_venta), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

{{-- ══════════════════════════════════════════
     REPORTE: STOCK BAJO
══════════════════════════════════════════ --}}
@elseif($tipo === 'stock_bajo')
    @if($datos->count() > 0)
    <div style="background:#FEE2E2; border:1px solid #FECACA; border-radius:12px; padding:14px 18px; margin-bottom:18px; display:flex; align-items:center; gap:10px; color:#991B1B; font-size:.88rem; font-weight:600;">
        <i class="fas fa-triangle-exclamation"></i>
        {{ $datos->count() }} {{ $datos->count() === 1 ? 'producto necesita' : 'productos necesitan' }} reposición.
    </div>
    @endif
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-triangle-exclamation" style="color:#DC2626;margin-right:8px;"></i>Productos con stock bajo</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Stock actual</th>
                        <th>Stock mínimo</th>
                        <th>Faltante</th>
                        <th>Proveedor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($datos as $prod)
                    @php
                        $stock    = $prod->inventario?->cantidad_stock ?? 0;
                        $minimo   = $prod->inventario?->stock_minimo   ?? 0;
                        $faltante = max(0, $minimo - $stock);
                    @endphp
                    <tr>
                        <td style="font-weight:600; color:#1E1B4B;">{{ $prod->nombre }}</td>
                        <td style="color:#6B7280; font-size:.82rem;">{{ $prod->categoria?->nombre_categoria ?? '—' }}</td>
                        <td style="font-weight:700; color:#DC2626;">{{ $stock }}</td>
                        <td style="color:#6B7280;">{{ $minimo }}</td>
                        <td>
                            <span class="badge badge-bajo">
                                <i class="fas fa-arrow-down"></i> {{ $faltante }}
                            </span>
                        </td>
                        <td style="color:#6B7280; font-size:.82rem;">{{ $prod->proveedor ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center; padding:32px; color:#9CA3AF;">
                        <i class="fas fa-check-circle" style="font-size:1.5rem; color:#A7F3D0; display:block; margin-bottom:8px;"></i>
                        ¡Todos los productos tienen stock suficiente!
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

{{-- ══════════════════════════════════════════
     REPORTE: MOVIMIENTOS
══════════════════════════════════════════ --}}
@elseif($tipo === 'movimientos')
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-clock-rotate-left" style="color:#7C3AED;margin-right:8px;"></i>Historial de movimientos</span>
            <span style="font-size:.8rem; color:#9CA3AF;">{{ $datos->count() }} registros</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Descripción</th>
                        <th>Proveedor</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($datos as $mov)
                    <tr>
                        <td style="font-weight:600; color:#1E1B4B;">{{ $mov->producto?->nombre ?? '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $mov->tipo_movimiento }}">
                                <i class="fas {{ $mov->tipo_movimiento === 'entrada' ? 'fa-arrow-down' : ($mov->tipo_movimiento === 'salida' ? 'fa-arrow-up' : 'fa-arrows-rotate') }}"></i>
                                {{ ucfirst($mov->tipo_movimiento) }}
                            </span>
                        </td>
                        <td style="font-weight:700; color:{{ $mov->tipo_movimiento === 'entrada' ? '#059669' : '#DC2626' }};">
                            {{ $mov->tipo_movimiento === 'entrada' ? '+' : '-' }}{{ $mov->cantidad }}
                        </td>
                        <td style="color:#6B7280; font-size:.82rem;">{{ $mov->descripcion ?? '—' }}</td>
                        <td style="color:#6B7280; font-size:.82rem;">{{ $mov->proveedor ?? '—' }}</td>
                        <td style="color:#6B7280; font-size:.82rem;">{{ $mov->user?->nombre ?? 'Sistema' }}</td>
                        <td style="color:#9CA3AF; font-size:.8rem;">{{ $mov->fecha?->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center; padding:32px; color:#9CA3AF;">Sin movimientos en este período.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

@endsection

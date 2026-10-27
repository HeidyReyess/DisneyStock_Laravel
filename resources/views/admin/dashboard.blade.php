@extends('layouts.sidebar')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<style>
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 28px; }
    .stat-card {
        background: #fff; border-radius: 14px; padding: 22px 24px;
        border: 1px solid #EDE9FE;
        box-shadow: 0 2px 10px rgba(74,29,150,.06);
        display: flex; align-items: center; gap: 18px;
        transition: transform .2s, box-shadow .2s;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 22px rgba(74,29,150,.1); }
    .stat-icon {
        width: 52px; height: 52px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
    }
    .stat-icon.purple { background: #EDE9FE; color: #7C3AED; }
    .stat-icon.red    { background: #FEE2E2; color: #DC2626; }
    .stat-value { font-family: 'Outfit', sans-serif; font-size: 1.8rem; font-weight: 800; color: #1E1B4B; line-height: 1; }
    .stat-label { font-size: .8rem; color: #6B7280; margin-top: 4px; font-weight: 500; }

    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px; }
    @media (max-width: 900px) { .grid-2 { grid-template-columns: 1fr; } }

    /* ── Tabla ── */
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: .87rem; }
    thead th {
        padding: 12px 16px; text-align: left;
        font-size: .72rem; font-weight: 700; letter-spacing: .08em;
        text-transform: uppercase; color: #6B7280;
        border-bottom: 2px solid #F5F3FF;
    }
    tbody td { padding: 13px 16px; border-bottom: 1px solid #F9F5FF; color: #374151; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover td { background: #FAFAFF; }

    /* ── Badges ── */
    .badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 20px; font-size: .74rem; font-weight: 600;
    }
    .badge-purple { background: #EDE9FE; color: #5B21B6; }
    .badge-red    { background: #FEE2E2; color: #991B1B; }

    /* ── Empty state ── */
    .empty-state { text-align: center; padding: 32px 16px; color: #9CA3AF; }
    .empty-state i { font-size: 2rem; margin-bottom: 10px; color: #C4B5FD; display: block; }
    .empty-state p { font-size: .87rem; }

    /* ── Welcome banner ── */
    .welcome-banner {
        background: #4A1D96;
        border-radius: 16px; padding: 28px 32px;
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 28px; overflow: hidden; position: relative;
    }
    .welcome-banner::after {
        content: '';
        position: absolute; right: -20px; top: -20px;
        width: 180px; height: 180px; border-radius: 50%;
        background: rgba(255,255,255,.06);
    }
    .welcome-banner h2 { font-family: 'Outfit', sans-serif; font-size: 1.4rem; font-weight: 800; color: #fff; margin-bottom: 6px; }
    .welcome-banner p { font-size: .88rem; color: rgba(255,255,255,.75); }
    .welcome-banner img { height: 70px; width: auto; object-fit: contain; opacity: .9; filter: brightness(0) invert(1); position: relative; z-index: 1; }
</style>

{{-- Banner de bienvenida --}}
<div class="welcome-banner">
    <div>
        <h2>¡Hola, {{ Auth::user()->nombre }}! 👋</h2>
        <p>Bienvenido al panel de control de DisneyStock — {{ $fechaHoy }}</p>
    </div>
    <img src="{{ asset('img/BlancoSolo.png') }}" alt="DisneyStock">
</div>

{{-- Tarjetas de métricas --}}
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-boxes-stacked"></i></div>
        <div>
            <div class="stat-value">{{ $productosActivos }}</div>
            <div class="stat-label">Productos activos</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-cash-register"></i></div>
        <div>
            <div class="stat-value">{{ $ventasHoy }}</div>
            <div class="stat-label">{{ $esAdmin ? 'Ventas hoy' : 'Mis ventas hoy' }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-dollar-sign"></i></div>
        <div>
            <div class="stat-value">$ {{ number_format($ingresosMes, 0, ',', '.') }}</div>
            <div class="stat-label">{{ $esAdmin ? 'Ingresos del mes' : 'Mis ingresos del mes' }}</div>
        </div>
    </div>
    {{-- [1] Ícono rojo solo si hay stock bajo; lila si es 0 --}}
    <div class="stat-card">
        <div class="stat-icon {{ $stockBajo > 0 ? 'red' : 'purple' }}">
            <i class="fas fa-triangle-exclamation"></i>
        </div>
        <div>
            <div class="stat-value">{{ $stockBajo }}</div>
            <div class="stat-label">Stock bajo mínimo</div>
        </div>
    </div>
</div>

{{-- Ventas recientes + alertas stock --}}
<div class="grid-2">

    {{-- Ventas recientes --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-receipt" style="color:#7C3AED;margin-right:8px;"></i>{{ $esAdmin ? 'Ventas recientes' : 'Mis ventas recientes' }}</span>
            <a href="{{ route('ventas.index') }}"
               style="font-size:.8rem;color:#7C3AED;text-decoration:none;font-weight:600;">Ver todas →</a>
        </div>
        <div class="card-body" style="padding:0 0 4px;">
            @forelse($ventasRecientes as $venta)
                @if($loop->first)
                <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th># Venta</th>
                            <th>Fecha</th>
                            <th>Empleado</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                @endif
                        <tr>
                            <td>
                                <a href="{{ route('ventas.show', $venta) }}"
                                   style="color:#5B21B6;font-weight:600;text-decoration:none;">
                                    #{{ $venta->id }}
                                </a>
                            </td>
                            <td>{{ $venta->fecha_venta->format('d/m/Y') }}</td>
                            <td style="color:#6B7280;">{{ $venta->user?->nombre ?? '—' }}</td>
                            <td style="font-weight:600;">$ {{ number_format($venta->total, 0, ',', '.') }}</td>
                        </tr>
                @if($loop->last)
                    </tbody>
                </table>
                </div>
                @endif
            @empty
                <div class="empty-state">
                    <i class="fas fa-receipt"></i>
                    <p>Aún no hay ventas registradas.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Alertas de stock --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-bell" style="color:#7C3AED;margin-right:8px;"></i>Alertas de stock</span>
            <a href="{{ route('inventario.index') }}"
               style="font-size:.8rem;color:#7C3AED;text-decoration:none;font-weight:600;">Ver inventario →</a>
        </div>
        <div class="card-body" style="padding:0 0 4px;">
            @forelse($alertasActivas as $alerta)
                @if($loop->first)
                <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Stock</th>
                            <th>Mínimo</th>
                        </tr>
                    </thead>
                    <tbody>
                @endif
                        <tr>
                            <td>
                                <a href="{{ route('inventario.index', ['producto_id' => $alerta->producto_id]) }}"
                                   style="color:#DC2626;font-weight:600;text-decoration:none;">
                                    <i class="fas fa-triangle-exclamation" style="font-size:.75rem;margin-right:4px;"></i>
                                    {{ $alerta->producto?->nombre ?? '—' }}
                                </a>
                            </td>
                            <td style="font-weight:700;color:#DC2626;">
                                {{ $alerta->producto?->inventario?->cantidad_stock ?? '—' }}
                            </td>
                            <td style="color:#6B7280;">
                                {{ $alerta->producto?->inventario?->stock_minimo ?? '—' }}
                            </td>
                        </tr>
                @if($loop->last)
                    </tbody>
                </table>
                </div>
                @endif
            @empty
                <div class="empty-state" style="padding:24px 16px;">
                    <i class="fas fa-bell-slash"></i>
                    <p>No hay alertas activas de stock bajo.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- Productos más vendidos + accesos rápidos --}}
<div class="grid-2">

    {{-- Productos más vendidos --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-trophy" style="color:#7C3AED;margin-right:8px;"></i>Productos más vendidos</span>
        </div>
        <div class="card-body" style="padding:0 0 4px;">
            @forelse($topProductos as $item)
                @if($loop->first)
                <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Producto</th>
                            <th>Unidades</th>
                            <th>Total vendido</th>
                        </tr>
                    </thead>
                    <tbody>
                @endif
                        <tr>
                            <td><span class="badge badge-purple">{{ $loop->iteration }}</span></td>
                            <td style="font-weight:600;color:#1E1B4B;">{{ $item->nombre }}</td>
                            <td>{{ number_format($item->total_cantidad, 0, ',', '.') }}</td>
                            <td style="font-weight:600;">$ {{ number_format($item->total_vendido, 0, ',', '.') }}</td>
                        </tr>
                @if($loop->last)
                    </tbody>
                </table>
                </div>
                @endif
            @empty
                <div class="empty-state">
                    <i class="fas fa-trophy"></i>
                    <p>Los productos más vendidos<br>aparecerán aquí.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Accesos rápidos --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-bolt" style="color:#7C3AED;margin-right:8px;"></i>Accesos rápidos</span>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <a href="{{ Route::has('ventas.create') ? route('ventas.create') : '#' }}"
                   onclick="{{ Route::has('ventas.create') ? 'return true' : 'proximamente(); return false' }}"
                   style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 12px;background:#F5F3FF;border-radius:12px;text-decoration:none;transition:background .2s;" onmouseover="this.style.background='#EDE9FE'" onmouseout="this.style.background='#F5F3FF'">
                    <i class="fas fa-plus-circle" style="font-size:1.4rem;color:#7C3AED;"></i>
                    <span style="font-size:.8rem;font-weight:600;color:#1E1B4B;">Nueva venta</span>
                </a>
                <a href="{{ Route::has('productos.create') ? route('productos.create') : '#' }}"
                   onclick="{{ Route::has('productos.create') ? 'return true' : 'proximamente(); return false' }}"
                   style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 12px;background:#F5F3FF;border-radius:12px;text-decoration:none;transition:background .2s;" onmouseover="this.style.background='#EDE9FE'" onmouseout="this.style.background='#F5F3FF'">
                    <i class="fas fa-box-open" style="font-size:1.4rem;color:#7C3AED;"></i>
                    <span style="font-size:.8rem;font-weight:600;color:#1E1B4B;">Nuevo producto</span>
                </a>
                <a href="{{ Route::has('inventario.index') ? route('inventario.index') : '#' }}"
                   onclick="{{ Route::has('inventario.index') ? 'return true' : 'proximamente(); return false' }}"
                   style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 12px;background:#F5F3FF;border-radius:12px;text-decoration:none;transition:background .2s;" onmouseover="this.style.background='#EDE9FE'" onmouseout="this.style.background='#F5F3FF'">
                    <i class="fas fa-truck-ramp-box" style="font-size:1.4rem;color:#7C3AED;"></i>
                    <span style="font-size:.8rem;font-weight:600;color:#1E1B4B;">Entrada de stock</span>
                </a>
                <a href="{{ Route::has('reportes.index') ? route('reportes.index') : '#' }}"
                   onclick="{{ Route::has('reportes.index') ? 'return true' : 'proximamente(); return false' }}"
                   style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 12px;background:#F5F3FF;border-radius:12px;text-decoration:none;transition:background .2s;" onmouseover="this.style.background='#EDE9FE'" onmouseout="this.style.background='#F5F3FF'">
                    <i class="fas fa-chart-bar" style="font-size:1.4rem;color:#7C3AED;"></i>
                    <span style="font-size:.8rem;font-weight:600;color:#1E1B4B;">Ver reportes</span>
                </a>
            </div>
        </div>
    </div>

</div>

@endsection

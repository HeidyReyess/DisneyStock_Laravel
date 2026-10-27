@extends('layouts.sidebar')

@section('title', 'Historial de movimientos')
@section('page-title', 'Historial de movimientos')

@section('content')
<style>
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
        transition:border-color .2s; min-width:150px;
    }
    .filtro-input:focus, .filtro-select:focus { outline:none; border-color:#7C3AED; background:#fff; }
    .btn-filtrar {
        padding:9px 18px; background:#4A1D96; color:#fff;
        border:2px solid #4A1D96; border-radius:9px;
        font-size:.86rem; font-weight:700; cursor:pointer;
        transition:all .2s; align-self:flex-end;
        display:inline-flex; align-items:center; gap:6px;
    }
    .btn-filtrar:hover { background:#7C3AED; border-color:#7C3AED; }
    .btn-limpiar {
        padding:9px 14px; background:#F5F3FF; color:#5B21B6;
        border:1.5px solid #DDD6FE; border-radius:9px;
        font-size:.86rem; font-weight:600; cursor:pointer;
        transition:all .2s; align-self:flex-end; text-decoration:none;
        display:inline-flex; align-items:center; gap:6px;
    }
    .btn-limpiar:hover { background:#EDE9FE; }

    .table-wrap { overflow-x:auto; }
    table { width:100%; border-collapse:collapse; font-size:.87rem; }
    thead th {
        padding:12px 16px; text-align:left;
        font-size:.71rem; font-weight:700; letter-spacing:.08em;
        text-transform:uppercase; color:#6B7280;
        border-bottom:2px solid #F5F3FF; white-space:nowrap;
    }
    tbody td { padding:12px 16px; border-bottom:1px solid #F9F5FF; color:#374151; vertical-align:middle; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover td { background:#FAFAFF; }

    .badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:.74rem; font-weight:600; }
    .badge-entrada { background:#D1FAE5; color:#059669; }
    .badge-salida  { background:#FEE2E2; color:#DC2626; }
    .badge-ajuste  { background:#EDE9FE; color:#7C3AED; }

    .empty-row td { text-align:center; padding:40px; color:#9CA3AF; }
    .empty-row i  { font-size:1.8rem; color:#C4B5FD; display:block; margin-bottom:8px; }

    /* Paginación */
    .paginacion { display:flex; justify-content:flex-end; margin-top:16px; gap:6px; flex-wrap:wrap; }
    .paginacion a, .paginacion span {
        padding:6px 12px; border-radius:8px; font-size:.83rem; font-weight:600;
        border:1.5px solid #DDD6FE; text-decoration:none; color:#5B21B6;
        transition:all .2s;
    }
    .paginacion a:hover { background:#EDE9FE; }
    .paginacion span.active-page { background:#4A1D96; color:#fff; border-color:#4A1D96; }
    .paginacion span.disabled { color:#D1D5DB; border-color:#F3F4F6; cursor:default; }
</style>

{{-- Botón volver --}}
<div style="margin-bottom:16px;">
    <a href="{{ route('inventario.index') }}"
       style="display:inline-flex;align-items:center;gap:7px;font-size:.86rem;font-weight:600;color:#5B21B6;text-decoration:none;">
        <i class="fas fa-arrow-left"></i> Volver a Inventario
    </a>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('inventario.historial') }}">
    <div class="filtros-bar">
        <div class="filtro-group">
            <span class="filtro-label">Producto</span>
            <select name="producto_id" class="filtro-select">
                <option value="">Todos</option>
                @foreach($productosSelect as $p)
                    <option value="{{ $p->id }}" {{ request('producto_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filtro-group">
            <span class="filtro-label">Tipo</span>
            <select name="tipo" class="filtro-select" style="min-width:130px;">
                <option value="">Todos</option>
                <option value="entrada" {{ request('tipo') === 'entrada' ? 'selected' : '' }}>Entrada</option>
                <option value="salida"  {{ request('tipo') === 'salida'  ? 'selected' : '' }}>Salida</option>
                <option value="ajuste"  {{ request('tipo') === 'ajuste'  ? 'selected' : '' }}>Ajuste</option>
            </select>
        </div>
        <div class="filtro-group">
            <span class="filtro-label">Desde</span>
            <input type="date" name="desde" class="filtro-input" value="{{ request('desde') }}">
        </div>
        <div class="filtro-group">
            <span class="filtro-label">Hasta</span>
            <input type="date" name="hasta" class="filtro-input" value="{{ request('hasta') }}">
        </div>
        <button type="submit" class="btn-filtrar"><i class="fas fa-filter"></i> Filtrar</button>
        @if(request()->hasAny(['producto_id','tipo','desde','hasta']))
            <a href="{{ route('inventario.historial') }}" class="btn-limpiar">
                <i class="fas fa-xmark"></i> Limpiar
            </a>
        @endif
    </div>
</form>

{{-- Mensaje de rango de fechas inválido --}}
<div id="errorRangoFechas" class="alert-error" style="margin-bottom:20px; {{ !($rangoInvalido ?? false) ? 'display:none;' : '' }}">
    <i class="fas fa-circle-xmark"></i>
    <span>Rango de fechas inválido.</span>
</div>

{{-- Tabla --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">
            <i class="fas fa-clock-rotate-left" style="color:#7C3AED;margin-right:8px;"></i>
            Historial de movimientos
        </span>
        <span style="font-size:.8rem;color:#9CA3AF;">{{ $movimientos->total() }} registros</span>
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
                    <th>Registrado por</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movimientos as $mov)
                <tr>
                    <td style="font-weight:600;color:#1E1B4B;">{{ $mov->producto?->nombre ?? '—' }}</td>
                    <td>
                        <span class="badge badge-{{ $mov->tipo_movimiento }}">
                            <i class="fas {{ $mov->tipo_movimiento === 'entrada' ? 'fa-arrow-down' : ($mov->tipo_movimiento === 'salida' ? 'fa-arrow-up' : 'fa-arrows-rotate') }}"></i>
                            {{ ucfirst($mov->tipo_movimiento) }}
                        </span>
                    </td>
                    <td style="font-weight:700;color:{{ $mov->tipo_movimiento === 'entrada' ? '#059669' : '#DC2626' }};">
                        {{ $mov->tipo_movimiento === 'entrada' ? '+' : '-' }}{{ $mov->cantidad }}
                    </td>
                    <td style="color:#6B7280;font-size:.82rem;">{{ $mov->descripcion ?? '—' }}</td>
                    <td style="color:#6B7280;font-size:.82rem;">{{ $mov->proveedor ?? '—' }}</td>
                    <td style="color:#6B7280;font-size:.82rem;">{{ $mov->user?->nombre ?? 'Sistema' }}</td>
                    <td style="color:#9CA3AF;font-size:.8rem;">{{ $mov->fecha?->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="7">
                        <i class="fas fa-inbox"></i>
                        No hay movimientos que coincidan con los filtros.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($movimientos->hasPages())
    <div style="padding:12px 20px;">
        <div class="paginacion">
            {{-- Anterior --}}
            @if($movimientos->onFirstPage())
                <span class="disabled"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $movimientos->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a>
            @endif

            {{-- Páginas --}}
            @foreach($movimientos->getUrlRange(max(1, $movimientos->currentPage()-2), min($movimientos->lastPage(), $movimientos->currentPage()+2)) as $page => $url)
                @if($page == $movimientos->currentPage())
                    <span class="active-page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            {{-- Siguiente --}}
            @if($movimientos->hasMorePages())
                <a href="{{ $movimientos->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formHistorial = document.querySelector('form[action="{{ route('inventario.historial') }}"]');
    if (formHistorial) {
        formHistorial.addEventListener('submit', function(e) {
            const desde = formHistorial.querySelector('input[name="desde"]')?.value;
            const hasta = formHistorial.querySelector('input[name="hasta"]')?.value;
            const errorMsg = document.getElementById('errorRangoFechas');
            if (desde && hasta && desde > hasta) {
                e.preventDefault();
                if (errorMsg) errorMsg.style.display = 'flex';
            }
        });
    }
});
</script>

@extends('layouts.sidebar')

@section('title', 'Inventario')
@section('page-title', 'Inventario')

@section('content')
<style>
    /* ── Toolbar ── */
    .toolbar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; margin-bottom:22px; }
    .filters { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
    .filter-btn {
        padding:8px 16px; border-radius:9px; font-size:.85rem; font-weight:600;
        border:1.5px solid #DDD6FE; cursor:pointer; transition:all .2s; text-decoration:none;
        display:inline-flex; align-items:center; gap:6px;
    }
    .filter-btn.active, .filter-btn:hover { background:#4A1D96; color:#fff; border-color:#4A1D96; }
    .filter-btn:not(.active) { background:#F5F3FF; color:#5B21B6; }
    .btn-nuevo {
        display:inline-flex; align-items:center; gap:8px;
        padding:10px 20px; background:#4A1D96; color:#fff;
        border:2px solid #4A1D96; border-radius:10px;
        font-family:'Outfit',sans-serif; font-size:.9rem; font-weight:700;
        cursor:pointer; transition:all .2s; white-space:nowrap;
    }
    .btn-nuevo:hover { background:#7C3AED; border-color:#7C3AED; }

    /* ── Banner stock bajo ── */
    .banner-bajo {
        background:#FEE2E2; border:1px solid #FECACA; border-radius:12px;
        padding:14px 18px; margin-bottom:20px;
        display:flex; align-items:center; gap:12px; color:#991B1B;
        font-size:.88rem; font-weight:600;
    }
    .banner-bajo i { font-size:1.1rem; flex-shrink:0; }

    /* ── Grid principal ── */
    .inv-grid { display:grid; grid-template-columns:1fr 360px; gap:20px; align-items:start; }
    @media(max-width:1100px) { .inv-grid { grid-template-columns:1fr; } }

    /* ── Tabla ── */
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

    /* ── Stock bar ── */
    .stock-wrap { display:flex; flex-direction:column; gap:4px; min-width:120px; }
    .stock-bar-bg { background:#F3F4F6; border-radius:4px; height:6px; overflow:hidden; }
    .stock-bar-fill { height:6px; border-radius:4px; transition:width .4s; }
    .stock-bar-fill.ok  { background:#7C3AED; }
    .stock-bar-fill.low { background:#EF4444; }
    .stock-nums { display:flex; justify-content:space-between; font-size:.74rem; color:#9CA3AF; }

    /* ── Badges ── */
    .badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:.74rem; font-weight:600; }
    .badge-ok   { background:#EDE9FE; color:#5B21B6; }
    .badge-bajo { background:#FEE2E2; color:#991B1B; }

    /* ── Empty state ── */
    .empty-row td { text-align:center; padding:40px 16px; color:#9CA3AF; }
    .empty-row i  { font-size:1.8rem; color:#C4B5FD; display:block; margin-bottom:8px; }

    /* ── Panel lateral movimientos ── */
    .panel-mov .card-header { border-bottom:1px solid #F5F3FF; }
    .mov-item { display:flex; align-items:flex-start; gap:12px; padding:12px 0; border-bottom:1px solid #F9F5FF; }
    .mov-item:last-child { border-bottom:none; }
    .mov-icon {
        width:34px; height:34px; border-radius:9px; flex-shrink:0;
        display:flex; align-items:center; justify-content:center; font-size:.9rem;
    }
    .mov-icon.entrada { background:#D1FAE5; color:#059669; }
    .mov-icon.salida  { background:#FEE2E2; color:#DC2626; }
    .mov-icon.ajuste  { background:#EDE9FE; color:#7C3AED; }
    .mov-producto { font-weight:600; font-size:.85rem; color:#1E1B4B; }
    .mov-detalle  { font-size:.77rem; color:#6B7280; margin-top:2px; }

    /* ── Modal ── */
    .modal-overlay { display:none; position:fixed; inset:0; z-index:2000; align-items:center; justify-content:center; padding:20px; }
    .modal-overlay.open { display:flex; }
    .modal-backdrop { position:absolute; inset:0; background:rgba(30,7,71,.45); }
    .modal-box { position:relative; background:#fff; border-radius:16px; width:100%; max-width:480px; padding:32px 28px; box-shadow:0 20px 60px rgba(74,29,150,.18); }
    .modal-close { position:absolute; top:14px; right:16px; background:none; border:none; font-size:1.2rem; color:#9CA3AF; cursor:pointer; }
    .modal-close:hover { color:#4A1D96; }
    .modal-title { font-family:'Outfit',sans-serif; font-size:1.1rem; font-weight:800; color:#1E1B4B; margin-bottom:20px; display:flex; align-items:center; gap:10px; }
    .modal-title i { color:#059669; }
    .form-group { margin-bottom:16px; }
    .form-label { display:block; font-size:.81rem; font-weight:600; color:#374151; margin-bottom:5px; }
    .form-input, .form-select, .form-textarea {
        width:100%; padding:9px 12px; border:1.5px solid #E5E7EB; border-radius:9px;
        font-size:.9rem; color:#1E1B4B; font-family:'Inter',sans-serif;
        transition:border-color .2s; background:#FAFAFA; box-sizing:border-box;
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline:none; border-color:#7C3AED; box-shadow:0 0 0 3px rgba(124,58,237,.1); background:#fff;
    }
    .form-input.is-invalid { border-color:#EF4444; }
    .invalid-msg { font-size:.76rem; color:#EF4444; margin-top:3px; }
    .modal-footer { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; }
    .btn-cancelar { padding:9px 18px; background:#F5F3FF; color:#5B21B6; border:1.5px solid #DDD6FE; border-radius:9px; font-size:.87rem; font-weight:600; cursor:pointer; }
    .btn-cancelar:hover { background:#EDE9FE; }
    .btn-guardar { padding:9px 22px; background:#4A1D96; color:#fff; border:2px solid #4A1D96; border-radius:9px; font-family:'Outfit',sans-serif; font-size:.88rem; font-weight:700; cursor:pointer; }
    .btn-guardar:hover { background:#7C3AED; border-color:#7C3AED; }

    /* Info box stock actual */
    .stock-info-box {
        background:#F5F3FF; border:1px solid #DDD6FE; border-radius:10px;
        padding:12px 16px; margin-bottom:16px; display:flex; align-items:center; gap:12px;
    }
    .stock-info-box .num { font-family:'Outfit',sans-serif; font-size:1.6rem; font-weight:800; color:#4A1D96; }
    .stock-info-box .lbl { font-size:.8rem; color:#6B7280; }
</style>

{{-- Banner alerta stock bajo --}}
@if($totalBajo > 0)
<div class="banner-bajo">
    <i class="fas fa-triangle-exclamation"></i>
    <span>
        {{ $totalBajo }} {{ $totalBajo === 1 ? 'producto tiene' : 'productos tienen' }} stock igual o por debajo del mínimo.
        <a href="{{ route('inventario.index', ['filtro' => 'bajo']) }}" style="color:#991B1B; font-weight:700; margin-left:6px;">Ver solo esos →</a>
    </span>
</div>
@endif

{{-- Toolbar --}}
<div class="toolbar">
    <div class="filters">
        <a href="{{ route('inventario.index') }}"
           class="filter-btn {{ !request('filtro') ? 'active' : '' }}">
            <i class="fas fa-list"></i> Todos
        </a>
        <a href="{{ route('inventario.index', ['filtro' => 'bajo']) }}"
           class="filter-btn {{ request('filtro') === 'bajo' ? 'active' : '' }}">
            <i class="fas fa-triangle-exclamation"></i> Stock bajo
        </a>
    </div>
    @if(Auth::user()->esAdmin())
    <button class="btn-nuevo" onclick="document.getElementById('modalEntrada').classList.add('open')">
        <i class="fas fa-truck-ramp-box"></i> Registrar entrada
    </button>
    @endif
</div>

{{-- Grid: tabla + panel movimientos --}}
<div class="inv-grid">

    {{-- Tabla de inventario --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-warehouse" style="color:#7C3AED;margin-right:8px;"></i>Stock actual</span>
            <span style="font-size:.8rem; color:#9CA3AF;">{{ $productos->count() }} productos</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Stock</th>
                        <th>Mínimo</th>
                        <th>Estado</th>
                        <th>Actualizado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $prod)
                    @php
                        $inv    = $prod->inventario;
                        $stock  = $inv?->cantidad_stock ?? 0;
                        $minimo = $inv?->stock_minimo ?? 0;
                        $bajo   = $minimo > 0 && $stock <= $minimo;
                        $pct    = $minimo > 0 ? min(100, round(($stock / ($minimo * 2)) * 100)) : ($stock > 0 ? 100 : 0);
                    @endphp
                    <tr>
                        <td style="font-weight:600; color:#1E1B4B;">{{ $prod->nombre }}</td>
                        <td style="color:#6B7280; font-size:.82rem;">{{ $prod->categoria?->nombre_categoria ?? '—' }}</td>
                        <td>
                            <div class="stock-wrap">
                                <div class="stock-bar-bg">
                                    <div class="stock-bar-fill {{ $bajo ? 'low' : 'ok' }}" style="width:{{ $pct }}%"></div>
                                </div>
                                <div class="stock-nums">
                                    <span style="font-weight:700; color:{{ $bajo ? '#DC2626' : '#4A1D96' }};">{{ $stock }}</span>
                                    <span>mín {{ $minimo }}</span>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:.83rem; color:#6B7280;">{{ $minimo }}</td>
                        <td>
                            <span class="badge {{ $bajo ? 'badge-bajo' : 'badge-ok' }}">
                                <i class="fas {{ $bajo ? 'fa-triangle-exclamation' : 'fa-check' }}"></i>
                                {{ $bajo ? 'Stock bajo' : 'Normal' }}
                            </span>
                        </td>
                        <td style="font-size:.8rem; color:#9CA3AF;">
                            {{ $inv?->fecha_actualizacion?->format('d/m/Y') ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="6">
                            <i class="fas fa-warehouse"></i>
                            Aún no hay productos en inventario.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Panel lateral: últimos movimientos --}}
    <div class="card panel-mov">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-clock-rotate-left" style="color:#7C3AED;margin-right:8px;"></i>Movimientos recientes</span>
        </div>
        <div class="card-body">
            @forelse($movimientos as $mov)
            <div class="mov-item">
                <div class="mov-icon {{ $mov->tipo_movimiento }}">
                    <i class="fas {{ $mov->tipo_movimiento === 'entrada' ? 'fa-arrow-down' : ($mov->tipo_movimiento === 'salida' ? 'fa-arrow-up' : 'fa-arrows-rotate') }}"></i>
                </div>
                <div style="flex:1; min-width:0;">
                    <div class="mov-producto">{{ $mov->producto?->nombre ?? '—' }}</div>
                    <div class="mov-detalle">
                        {{ ucfirst($mov->tipo_movimiento) }} · {{ $mov->cantidad }} uds
                        · {{ $mov->fecha?->format('d/m/Y') }}
                    </div>
                    @if($mov->descripcion)
                        <div class="mov-detalle" style="color:#9CA3AF;">{{ $mov->descripcion }}</div>
                    @endif
                </div>
                <div style="font-size:.75rem; color:{{ $mov->tipo_movimiento === 'entrada' ? '#059669' : '#DC2626' }}; font-weight:700; white-space:nowrap;">
                    {{ $mov->tipo_movimiento === 'entrada' ? '+' : '-' }}{{ $mov->cantidad }}
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:24px; color:#9CA3AF; font-size:.87rem;">
                <i class="fas fa-inbox" style="font-size:1.5rem; color:#C4B5FD; display:block; margin-bottom:8px;"></i>
                Aún no hay movimientos registrados.
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════
     MODAL REGISTRAR ENTRADA
════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalEntrada">
    <div class="modal-backdrop" onclick="this.parentElement.classList.remove('open')"></div>
    <div class="modal-box">
        <button class="modal-close" onclick="document.getElementById('modalEntrada').classList.remove('open')">
            <i class="fas fa-xmark"></i>
        </button>
        <div class="modal-title"><i class="fas fa-truck-ramp-box"></i> Registrar entrada de stock</div>

        {{-- Info stock actual (se actualiza con JS) --}}
        <div class="stock-info-box" id="stockInfoBox" style="display:none;">
            <div>
                <div class="num" id="stockActualNum">0</div>
                <div class="lbl">unidades en stock</div>
            </div>
            <div style="border-left:1px solid #DDD6FE; padding-left:14px;">
                <div class="num" id="stockMinimoNum" style="color:#6B7280; font-size:1.1rem;">0</div>
                <div class="lbl">stock mínimo</div>
            </div>
        </div>

        <form method="POST" action="{{ route('inventario.movimiento') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Producto <span style="color:#EF4444">*</span></label>
                <select name="producto_id" id="selectProducto" class="form-select {{ $errors->has('producto_id') ? 'is-invalid' : '' }}"
                    onchange="mostrarStock(this)">
                    <option value="">Selecciona un producto...</option>
                    @foreach($productosSelect as $p)
                        <option value="{{ $p->id }}"
                            data-stock="{{ $p->inventario?->cantidad_stock ?? 0 }}"
                            data-minimo="{{ $p->inventario?->stock_minimo ?? 0 }}"
                            {{ old('producto_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('producto_id') <div class="invalid-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Cantidad a ingresar <span style="color:#EF4444">*</span></label>
                <input type="number" name="cantidad" class="form-input {{ $errors->has('cantidad') ? 'is-invalid' : '' }}"
                    value="{{ old('cantidad', 1) }}" min="1" placeholder="Ej: 10">
                @error('cantidad') <div class="invalid-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Proveedor</label>
                <input type="text" name="proveedor" class="form-input"
                    value="{{ old('proveedor') }}" placeholder="Nombre del proveedor (opcional)" maxlength="100">
            </div>

            <div class="form-group">
                <label class="form-label">Descripción / motivo</label>
                <textarea name="descripcion" class="form-input" rows="2"
                    placeholder="Compra de mercancía, reposición, etc." maxlength="200"
                    style="resize:vertical;">{{ old('descripcion') }}</textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancelar"
                    onclick="document.getElementById('modalEntrada').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn-guardar">
                    <i class="fas fa-check"></i> Registrar entrada
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function mostrarStock(sel) {
        const opt = sel.options[sel.selectedIndex];
        const box = document.getElementById('stockInfoBox');
        if (opt.value) {
            document.getElementById('stockActualNum').textContent = opt.dataset.stock;
            document.getElementById('stockMinimoNum').textContent = opt.dataset.minimo;
            box.style.display = 'flex';
        } else {
            box.style.display = 'none';
        }
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') document.getElementById('modalEntrada').classList.remove('open');
    });

    @if($errors->any())
        document.getElementById('modalEntrada').classList.add('open');
        // Restaurar info de stock si hubo error
        const sel = document.getElementById('selectProducto');
        if (sel) mostrarStock(sel);
    @endif
</script>
@endsection

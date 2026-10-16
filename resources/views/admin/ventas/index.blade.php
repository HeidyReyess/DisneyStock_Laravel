@extends('layouts.sidebar')

@section('title', 'Ventas')
@section('page-title', 'Ventas')

@section('content')
<style>
    /* ── Métricas ── */
    .metrics-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:16px; margin-bottom:22px; }
    .metric-card { background:#fff; border-radius:12px; padding:18px 20px; border:1px solid #EDE9FE; box-shadow:0 2px 8px rgba(74,29,150,.05); display:flex; align-items:center; gap:14px; }
    .metric-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
    .metric-icon.purple { background:#EDE9FE; color:#7C3AED; }
    .metric-icon.green  { background:#D1FAE5; color:#059669; }
    .metric-val  { font-family:'Outfit',sans-serif; font-size:1.5rem; font-weight:800; color:#1E1B4B; line-height:1; }
    .metric-lbl  { font-size:.78rem; color:#6B7280; margin-top:3px; }

    /* ── Toolbar ── */
    .toolbar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:18px; }
    .filters { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
    .filter-input { padding:8px 12px; border:1.5px solid #E5E7EB; border-radius:9px; font-size:.84rem; color:#1E1B4B; background:#fff; transition:border-color .2s; }
    .filter-input:focus { outline:none; border-color:#7C3AED; }
    .btn-filtrar { padding:8px 14px; background:#F5F3FF; color:#5B21B6; border:1.5px solid #DDD6FE; border-radius:9px; font-size:.84rem; font-weight:600; cursor:pointer; transition:all .2s; }
    .btn-filtrar:hover { background:#EDE9FE; }
    .btn-nuevo { display:inline-flex; align-items:center; gap:8px; padding:10px 20px; background:#4A1D96; color:#fff; border:2px solid #4A1D96; border-radius:10px; font-family:'Outfit',sans-serif; font-size:.9rem; font-weight:700; cursor:pointer; transition:all .2s; white-space:nowrap; }
    .btn-nuevo:hover { background:#7C3AED; border-color:#7C3AED; }

    /* ── Tabla ── */
    .table-wrap { overflow-x:auto; }
    table { width:100%; border-collapse:collapse; font-size:.87rem; }
    thead th { padding:12px 16px; text-align:left; font-size:.71rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#6B7280; border-bottom:2px solid #F5F3FF; white-space:nowrap; }
    tbody td { padding:12px 16px; border-bottom:1px solid #F9F5FF; color:#374151; vertical-align:middle; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover td { background:#FAFAFF; }

    /* ── Badges ── */
    .badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:.74rem; font-weight:600; }
    .badge-completada { background:#D1FAE5; color:#065F46; }
    .badge-anulada    { background:#FEE2E2; color:#991B1B; }
    .badge-pendiente  { background:#FEF3C7; color:#92400E; }

    /* ── Botones ── */
    .btn-accion { display:inline-flex; align-items:center; gap:5px; padding:5px 11px; border-radius:8px; font-size:.79rem; font-weight:600; border:none; cursor:pointer; transition:all .2s; text-decoration:none; white-space:nowrap; }
    .btn-ver     { background:#EDE9FE; color:#5B21B6; }
    .btn-ver:hover { background:#DDD6FE; }
    .btn-anular  { background:#FEE2E2; color:#991B1B; }
    .btn-anular:hover { background:#FECACA; }

    .empty-row td { text-align:center; padding:40px; color:#9CA3AF; }
    .empty-row i  { font-size:1.8rem; color:#C4B5FD; display:block; margin-bottom:8px; }

    /* ── Modales ── */
    .modal-overlay { display:none; position:fixed; inset:0; z-index:2000; align-items:center; justify-content:center; padding:20px; }
    .modal-overlay.open { display:flex; }
    .modal-backdrop { position:absolute; inset:0; background:rgba(30,7,71,.45); }
    .modal-box { position:relative; background:#fff; border-radius:16px; width:100%; padding:32px 28px; box-shadow:0 20px 60px rgba(74,29,150,.18); max-height:90vh; overflow-y:auto; }
    .modal-box.lg { max-width:640px; }
    .modal-box.sm { max-width:460px; }
    .modal-close { position:absolute; top:14px; right:16px; background:none; border:none; font-size:1.2rem; color:#9CA3AF; cursor:pointer; }
    .modal-close:hover { color:#4A1D96; }
    .modal-title { font-family:'Outfit',sans-serif; font-size:1.1rem; font-weight:800; color:#1E1B4B; margin-bottom:20px; display:flex; align-items:center; gap:10px; }
    .modal-title i { color:#7C3AED; }
    .form-group { margin-bottom:14px; }
    .form-label { display:block; font-size:.81rem; font-weight:600; color:#374151; margin-bottom:5px; }
    .form-input, .form-select { width:100%; padding:9px 12px; border:1.5px solid #E5E7EB; border-radius:9px; font-size:.9rem; color:#1E1B4B; font-family:'Inter',sans-serif; transition:border-color .2s; background:#FAFAFA; box-sizing:border-box; }
    .form-input:focus, .form-select:focus { outline:none; border-color:#7C3AED; box-shadow:0 0 0 3px rgba(124,58,237,.1); background:#fff; }
    .invalid-msg { font-size:.76rem; color:#EF4444; margin-top:3px; }
    .modal-footer { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; }
    .btn-cancelar { padding:9px 18px; background:#F5F3FF; color:#5B21B6; border:1.5px solid #DDD6FE; border-radius:9px; font-size:.87rem; font-weight:600; cursor:pointer; }
    .btn-cancelar:hover { background:#EDE9FE; }
    .btn-guardar { padding:9px 22px; background:#4A1D96; color:#fff; border:2px solid #4A1D96; border-radius:9px; font-family:'Outfit',sans-serif; font-size:.88rem; font-weight:700; cursor:pointer; }
    .btn-guardar:hover { background:#7C3AED; border-color:#7C3AED; }

    /* ── Carrito ── */
    .carrito-vacio { text-align:center; padding:20px; color:#9CA3AF; font-size:.86rem; }
    .carrito-vacio i { font-size:1.5rem; color:#C4B5FD; display:block; margin-bottom:6px; }
    .carrito-item { display:flex; align-items:center; gap:10px; padding:9px 0; border-bottom:1px solid #F5F3FF; }
    .carrito-item:last-child { border-bottom:none; }
    .carrito-nombre { flex:1; font-size:.86rem; font-weight:600; color:#1E1B4B; }
    .carrito-precio { font-size:.83rem; color:#6B7280; }
    .carrito-total-row { display:flex; justify-content:space-between; align-items:center; padding-top:12px; margin-top:8px; border-top:2px solid #EDE9FE; }
    .carrito-total-num { font-family:'Outfit',sans-serif; font-size:1.3rem; font-weight:800; color:#4A1D96; }
    .btn-quitar { background:none; border:none; color:#EF4444; cursor:pointer; font-size:.95rem; padding:2px 6px; border-radius:6px; transition:background .2s; }
    .btn-quitar:hover { background:#FEE2E2; }
    .qty-input { width:52px; padding:4px 6px; border:1.5px solid #E5E7EB; border-radius:7px; font-size:.85rem; text-align:center; color:#1E1B4B; }
    .qty-input:focus { outline:none; border-color:#7C3AED; }
</style>

{{-- Métricas rápidas --}}
<div class="metrics-row">
    <div class="metric-card">
        <div class="metric-icon purple"><i class="fas fa-cash-register"></i></div>
        <div>
            <div class="metric-val">{{ $ventasHoy }}</div>
            <div class="metric-lbl">Ventas hoy</div>
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-icon green"><i class="fas fa-dollar-sign"></i></div>
        <div>
            <div class="metric-val">$ {{ number_format($ingresosHoy, 0, ',', '.') }}</div>
            <div class="metric-lbl">Ingresos hoy</div>
        </div>
    </div>
</div>

{{-- Toolbar --}}
<form method="GET" action="{{ route('ventas.index') }}">
<div class="toolbar">
    <div class="filters">
        <input type="date" name="desde" class="filter-input" value="{{ request('desde') }}" title="Desde">
        <input type="date" name="hasta" class="filter-input" value="{{ request('hasta') }}" title="Hasta">
        <select name="estado" class="filter-input" style="min-width:130px;">
            <option value="">Todos los estados</option>
            <option value="completada" {{ request('estado') === 'completada' ? 'selected' : '' }}>Completadas</option>
            <option value="anulada"    {{ request('estado') === 'anulada'    ? 'selected' : '' }}>Anuladas</option>
        </select>
        <button type="submit" class="btn-filtrar"><i class="fas fa-filter"></i> Filtrar</button>
        @if(request()->hasAny(['desde','hasta','estado']))
            <a href="{{ route('ventas.index') }}" class="btn-filtrar" style="text-decoration:none;">
                <i class="fas fa-xmark"></i> Limpiar
            </a>
        @endif
    </div>
    <button type="button" class="btn-nuevo" onclick="document.getElementById('modalNuevaVenta').classList.add('open')">
        <i class="fas fa-plus"></i> Nueva venta
    </button>
</div>
</form>

{{-- Tabla ventas --}}
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Factura</th>
                    <th>Fecha</th>
                    <th>Vendedor</th>
                    <th>Subtotal</th>
                    <th>Descuento</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventas as $venta)
                <tr>
                    <td style="font-weight:600; color:#4A1D96;">{{ $venta->factura?->numero ?? '—' }}</td>
                    <td style="color:#6B7280; font-size:.83rem;">{{ $venta->fecha_venta->format('d/m/Y') }}</td>
                    <td style="color:#6B7280;">{{ $venta->user?->nombre ?? '—' }}</td>
                    <td>$ {{ number_format($venta->subtotal, 0, ',', '.') }}</td>
                    <td>
                        @if($venta->descuento > 0)
                            <span style="color:#DC2626; font-size:.83rem;">- $ {{ number_format($venta->descuento, 0, ',', '.') }}</span>
                        @else
                            <span style="color:#9CA3AF;">—</span>
                        @endif
                    </td>
                    <td style="font-weight:700; color:#1E1B4B;">$ {{ number_format($venta->total, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge badge-{{ $venta->estado }}">
                            {{ ucfirst($venta->estado) }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex; gap:6px;">
                            <button class="btn-accion btn-ver"
                                onclick="verDetalle({{ $venta->id }})">
                                <i class="fas fa-eye"></i> Ver
                            </button>
                            @if(!$venta->estaAnulada() && Auth::user()->esAdmin())
                            <form method="POST" action="{{ route('ventas.anular', $venta) }}"
                                onsubmit="return confirm('¿Anular la venta {{ $venta->factura?->numero }}? Se restaurará el stock.')">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-accion btn-anular">
                                    <i class="fas fa-ban"></i> Anular
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="8">
                        <i class="fas fa-receipt"></i>
                        Aún no hay ventas registradas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ════════════════════════════════════════
     MODAL NUEVA VENTA (carrito)
════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalNuevaVenta">
    <div class="modal-backdrop" onclick="cerrarModal('modalNuevaVenta')"></div>
    <div class="modal-box lg">
        <button class="modal-close" onclick="cerrarModal('modalNuevaVenta')"><i class="fas fa-xmark"></i></button>
        <div class="modal-title"><i class="fas fa-cart-plus"></i> Nueva venta</div>

        {{-- Selector de producto --}}
        <div style="display:grid; grid-template-columns:1fr auto; gap:10px; margin-bottom:16px;">
            <select id="selectProducto" class="form-select">
                <option value="">Selecciona un producto...</option>
                @foreach($productos as $p)
                    <option value="{{ $p->id }}"
                        data-nombre="{{ $p->nombre }}"
                        data-precio="{{ $p->precio_venta }}"
                        data-stock="{{ $p->inventario?->cantidad_stock ?? 0 }}">
                        {{ $p->nombre }} — $ {{ number_format($p->precio_venta, 0, ',', '.') }}
                        (stock: {{ $p->inventario?->cantidad_stock ?? 0 }})
                    </option>
                @endforeach
            </select>
            <button type="button" class="btn-guardar" onclick="agregarAlCarrito()" style="white-space:nowrap;">
                <i class="fas fa-plus"></i> Agregar
            </button>
        </div>

        {{-- Carrito --}}
        <div style="background:#FAFAFA; border:1px solid #EDE9FE; border-radius:12px; padding:12px 16px; min-height:90px; margin-bottom:16px;">
            <div id="carritoVacio" class="carrito-vacio">
                <i class="fas fa-cart-shopping"></i>
                Agrega productos para comenzar la venta.
            </div>
            <div id="carritoItems"></div>
            <div id="carritoTotal" class="carrito-total-row" style="display:none;">
                <div style="font-size:.85rem; color:#6B7280;">
                    Descuento:
                    <input type="number" id="inputDescuento" min="0" step="100"
                        style="width:90px; padding:4px 8px; border:1.5px solid #E5E7EB; border-radius:7px; font-size:.85rem; margin-left:4px;"
                        placeholder="0" oninput="recalcularTotal()">
                </div>
                <div>
                    <span style="font-size:.82rem; color:#6B7280; margin-right:6px;">Total:</span>
                    <span class="carrito-total-num" id="totalDisplay">$ 0</span>
                </div>
            </div>
        </div>

        {{-- Form oculto para enviar al servidor --}}
        <form method="POST" action="{{ route('ventas.store') }}" id="formVenta">
            @csrf
            <input type="hidden" name="items" id="inputItems" value="[]">
            <input type="hidden" name="descuento" id="inputDescuentoHidden" value="0">

            <div class="modal-footer">
                <button type="button" class="btn-cancelar" onclick="cerrarModal('modalNuevaVenta')">Cancelar</button>
                <button type="submit" class="btn-guardar" id="btnConfirmar" disabled
                    style="opacity:.5; cursor:not-allowed;">
                    <i class="fas fa-check"></i> Confirmar venta
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════
     MODAL VER DETALLE
════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalDetalle">
    <div class="modal-backdrop" onclick="cerrarModal('modalDetalle')"></div>
    <div class="modal-box sm">
        <button class="modal-close" onclick="cerrarModal('modalDetalle')"><i class="fas fa-xmark"></i></button>
        <div class="modal-title"><i class="fas fa-receipt"></i> Detalle de venta</div>
        <div id="detalleContenido" style="color:#9CA3AF; text-align:center; padding:20px;">
            <i class="fas fa-spinner fa-spin" style="font-size:1.5rem;"></i>
        </div>
    </div>
</div>

<script>
    // ── Carrito ──────────────────────────────────────────────
    let carrito = [];

    function agregarAlCarrito() {
        const sel   = document.getElementById('selectProducto');
        const opt   = sel.options[sel.selectedIndex];
        if (!opt.value) return;

        const id     = parseInt(opt.value);
        const nombre = opt.dataset.nombre;
        const precio = parseFloat(opt.dataset.precio);
        const stock  = parseInt(opt.dataset.stock);

        // Si ya está en el carrito, incrementa
        const existente = carrito.find(i => i.id === id);
        if (existente) {
            if (existente.cantidad >= stock) {
                alert('No hay suficiente stock de ' + nombre);
                return;
            }
            existente.cantidad++;
        } else {
            if (stock <= 0) { alert('Sin stock disponible para ' + nombre); return; }
            carrito.push({ id, nombre, precio, cantidad: 1, stock });
        }
        renderCarrito();
        sel.value = '';
    }

    function renderCarrito() {
        const itemsDiv  = document.getElementById('carritoItems');
        const vacioDiv  = document.getElementById('carritoVacio');
        const totalRow  = document.getElementById('carritoTotal');
        const btnConf   = document.getElementById('btnConfirmar');

        if (carrito.length === 0) {
            vacioDiv.style.display = 'block';
            itemsDiv.innerHTML     = '';
            totalRow.style.display = 'none';
            btnConf.disabled       = true;
            btnConf.style.opacity  = '.5';
            btnConf.style.cursor   = 'not-allowed';
            return;
        }

        vacioDiv.style.display = 'none';
        totalRow.style.display = 'flex';
        btnConf.disabled       = false;
        btnConf.style.opacity  = '1';
        btnConf.style.cursor   = 'pointer';

        itemsDiv.innerHTML = carrito.map((it, idx) => `
            <div class="carrito-item">
                <div class="carrito-nombre">${it.nombre}</div>
                <div class="carrito-precio">$ ${fmt(it.precio)}</div>
                <input type="number" class="qty-input" value="${it.cantidad}" min="1" max="${it.stock}"
                    onchange="cambiarCantidad(${idx}, this.value)">
                <div style="font-weight:700; min-width:80px; text-align:right; color:#1E1B4B;">
                    $ ${fmt(it.precio * it.cantidad)}
                </div>
                <button class="btn-quitar" onclick="quitarItem(${idx})"><i class="fas fa-xmark"></i></button>
            </div>
        `).join('');

        recalcularTotal();
    }

    function cambiarCantidad(idx, val) {
        const cant = parseInt(val);
        if (cant < 1) { carrito[idx].cantidad = 1; }
        else if (cant > carrito[idx].stock) { carrito[idx].cantidad = carrito[idx].stock; }
        else { carrito[idx].cantidad = cant; }
        renderCarrito();
    }

    function quitarItem(idx) {
        carrito.splice(idx, 1);
        renderCarrito();
    }

    function recalcularTotal() {
        const subtotal  = carrito.reduce((s, i) => s + i.precio * i.cantidad, 0);
        const descuento = parseFloat(document.getElementById('inputDescuento').value) || 0;
        const total     = Math.max(0, subtotal - descuento);
        document.getElementById('totalDisplay').textContent = '$ ' + fmt(total);
        document.getElementById('inputDescuentoHidden').value = descuento;
        // Actualizar hidden items
        document.getElementById('inputItems').value = JSON.stringify(
            carrito.map(i => ({ id: i.id, cantidad: i.cantidad, precio: i.precio }))
        );
    }

    function fmt(n) {
        return new Intl.NumberFormat('es-CO', { minimumFractionDigits: 0 }).format(n);
    }

    // ── Modales ──────────────────────────────────────────────
    function cerrarModal(id) { document.getElementById(id).classList.remove('open'); }

    function verDetalle(id) {
        document.getElementById('detalleContenido').innerHTML =
            '<i class="fas fa-spinner fa-spin" style="font-size:1.5rem; color:#C4B5FD;"></i>';
        document.getElementById('modalDetalle').classList.add('open');

        fetch(`/ventas/${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => document.getElementById('detalleContenido').innerHTML = html)
            .catch(() => document.getElementById('detalleContenido').innerHTML =
                '<p style="color:#EF4444;">Error al cargar el detalle.</p>');
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            cerrarModal('modalNuevaVenta');
            cerrarModal('modalDetalle');
        }
    });
</script>
@endsection

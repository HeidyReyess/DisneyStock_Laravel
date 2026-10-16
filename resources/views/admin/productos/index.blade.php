@extends('layouts.sidebar')

@section('title', 'Productos')
@section('page-title', 'Productos')

@section('content')
<style>
    .toolbar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; margin-bottom:22px; }
    .filters { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
    .filter-input {
        padding:8px 12px; border:1.5px solid #E5E7EB; border-radius:9px;
        font-size:.85rem; color:#1E1B4B; background:#fff; min-width:160px;
        transition:border-color .2s;
    }
    .filter-input:focus { outline:none; border-color:#7C3AED; }
    .btn-nuevo {
        display:inline-flex; align-items:center; gap:8px;
        padding:10px 20px; background:#4A1D96; color:#fff;
        border:2px solid #4A1D96; border-radius:10px;
        font-family:'Outfit',sans-serif; font-size:.9rem; font-weight:700;
        cursor:pointer; transition:all .2s; white-space:nowrap;
    }
    .btn-nuevo:hover { background:#7C3AED; border-color:#7C3AED; }
    .btn-filtrar {
        padding:8px 16px; background:#F5F3FF; color:#5B21B6;
        border:1.5px solid #DDD6FE; border-radius:9px;
        font-size:.85rem; font-weight:600; cursor:pointer; transition:all .2s;
    }
    .btn-filtrar:hover { background:#EDE9FE; }

    /* ── Tabla ── */
    .table-wrap { overflow-x:auto; }
    table { width:100%; border-collapse:collapse; font-size:.87rem; }
    thead th {
        padding:12px 16px; text-align:left;
        font-size:.72rem; font-weight:700; letter-spacing:.08em;
        text-transform:uppercase; color:#6B7280;
        border-bottom:2px solid #F5F3FF; white-space:nowrap;
    }
    tbody td { padding:12px 16px; border-bottom:1px solid #F9F5FF; color:#374151; vertical-align:middle; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover td { background:#FAFAFF; }

    /* ── Imagen miniatura ── */
    .img-thumb {
        width:42px; height:42px; border-radius:9px; object-fit:cover;
        border:1px solid #EDE9FE;
    }
    .img-placeholder {
        width:42px; height:42px; border-radius:9px;
        background:#F5F3FF; border:1px solid #EDE9FE;
        display:flex; align-items:center; justify-content:center;
        color:#C4B5FD; font-size:1rem;
    }

    /* ── Badges ── */
    .badge {
        display:inline-flex; align-items:center; gap:4px;
        padding:3px 10px; border-radius:20px; font-size:.74rem; font-weight:600;
    }
    .badge-activo   { background:#D1FAE5; color:#065F46; }
    .badge-inactivo { background:#F3F4F6; color:#6B7280; }
    .badge-bajo     { background:#FEE2E2; color:#991B1B; }
    .badge-ok       { background:#EDE9FE; color:#5B21B6; }

    /* ── Botones acción ── */
    .btn-accion {
        display:inline-flex; align-items:center; gap:5px;
        padding:5px 11px; border-radius:8px;
        font-size:.79rem; font-weight:600; border:none; cursor:pointer;
        transition:all .2s; text-decoration:none; white-space:nowrap;
    }
    .btn-editar   { background:#EDE9FE; color:#5B21B6; }
    .btn-editar:hover   { background:#DDD6FE; }
    .btn-toggle-on  { background:#D1FAE5; color:#065F46; }
    .btn-toggle-on:hover  { background:#A7F3D0; }
    .btn-toggle-off { background:#FEF3C7; color:#92400E; }
    .btn-toggle-off:hover { background:#FDE68A; }
    .btn-eliminar { background:#FEE2E2; color:#991B1B; }
    .btn-eliminar:hover { background:#FECACA; }

    /* ── Empty state ── */
    .empty-row td { text-align:center; padding:48px 16px; color:#9CA3AF; }
    .empty-row i  { font-size:2rem; color:#C4B5FD; display:block; margin-bottom:10px; }

    /* ── Modal ── */
    .modal-overlay { display:none; position:fixed; inset:0; z-index:2000; align-items:center; justify-content:center; padding:20px; }
    .modal-overlay.open { display:flex; }
    .modal-backdrop { position:absolute; inset:0; background:rgba(30,7,71,.45); }
    .modal-box {
        position:relative; background:#fff; border-radius:16px;
        width:100%; max-width:560px; max-height:90vh; overflow-y:auto;
        padding:32px 28px; box-shadow:0 20px 60px rgba(74,29,150,.18);
    }
    .modal-close { position:absolute; top:14px; right:16px; background:none; border:none; font-size:1.2rem; color:#9CA3AF; cursor:pointer; }
    .modal-close:hover { color:#4A1D96; }
    .modal-title { font-family:'Outfit',sans-serif; font-size:1.1rem; font-weight:800; color:#1E1B4B; margin-bottom:20px; display:flex; align-items:center; gap:10px; }
    .modal-title i { color:#7C3AED; }

    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    @media(max-width:500px) { .form-grid { grid-template-columns:1fr; } }
    .form-group { margin-bottom:0; }
    .form-group.full { grid-column:1/-1; }
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
    .modal-footer { display:flex; gap:10px; justify-content:flex-end; margin-top:22px; }
    .btn-cancelar { padding:9px 18px; background:#F5F3FF; color:#5B21B6; border:1.5px solid #DDD6FE; border-radius:9px; font-size:.87rem; font-weight:600; cursor:pointer; }
    .btn-cancelar:hover { background:#EDE9FE; }
    .btn-guardar { padding:9px 22px; background:#4A1D96; color:#fff; border:2px solid #4A1D96; border-radius:9px; font-family:'Outfit',sans-serif; font-size:.88rem; font-weight:700; cursor:pointer; }
    .btn-guardar:hover { background:#7C3AED; border-color:#7C3AED; }

    /* Preview imagen */
    .img-preview { width:80px; height:80px; border-radius:10px; object-fit:cover; border:2px solid #EDE9FE; display:none; margin-top:8px; }
</style>

{{-- Toolbar --}}
<form method="GET" action="{{ route('productos.index') }}">
<div class="toolbar">
    <div class="filters">
        <input type="text" name="q" class="filter-input" placeholder="Buscar producto..." value="{{ request('q') }}">
        <select name="categoria" class="filter-input">
            <option value="">Todas las categorías</option>
            @foreach($categorias as $cat)
                <option value="{{ $cat->id }}" {{ request('categoria') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->nombre_categoria }}
                </option>
            @endforeach
        </select>
        <select name="estado" class="filter-input" style="min-width:120px;">
            <option value="">Todos</option>
            <option value="activo"   {{ request('estado') === 'activo'   ? 'selected' : '' }}>Activos</option>
            <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
        </select>
        <button type="submit" class="btn-filtrar"><i class="fas fa-filter"></i> Filtrar</button>
        @if(request()->hasAny(['q','categoria','estado']))
            <a href="{{ route('productos.index') }}" class="btn-filtrar" style="text-decoration:none;">
                <i class="fas fa-xmark"></i> Limpiar
            </a>
        @endif
    </div>
    <button type="button" class="btn-nuevo" onclick="abrirModalCrear()">
        <i class="fas fa-plus"></i> Nuevo producto
    </button>
</div>
</form>

{{-- Tabla --}}
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio venta</th>
                    <th>Precio compra</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $prod)
                <tr>
                    <td>
                        @if($prod->imagen)
                            <img src="{{ asset('storage/' . $prod->imagen) }}" class="img-thumb" alt="{{ $prod->nombre }}">
                        @else
                            <div class="img-placeholder"><i class="fas fa-image"></i></div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:600; color:#1E1B4B;">{{ $prod->nombre }}</div>
                        @if($prod->proveedor)
                            <div style="font-size:.77rem; color:#9CA3AF;">{{ $prod->proveedor }}</div>
                        @endif
                    </td>
                    <td style="color:#6B7280;">{{ $prod->categoria?->nombre_categoria ?? '—' }}</td>
                    <td style="font-weight:600;">$ {{ number_format($prod->precio_venta, 0, ',', '.') }}</td>
                    <td>$ {{ number_format($prod->precio_compra, 0, ',', '.') }}</td>
                    <td>
                        @php $stock = $prod->stockActual(); $bajo = $prod->stockBajo(); @endphp
                        <span class="badge {{ $bajo ? 'badge-bajo' : 'badge-ok' }}">
                            <i class="fas {{ $bajo ? 'fa-triangle-exclamation' : 'fa-check' }}"></i>
                            {{ $stock }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $prod->estaActivo() ? 'badge-activo' : 'badge-inactivo' }}">
                            {{ $prod->estaActivo() ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex; gap:5px; flex-wrap:wrap;">
                            {{-- Editar --}}
                            <button class="btn-accion btn-editar"
                                onclick="abrirModalEditar(
                                    {{ $prod->id }},
                                    '{{ addslashes($prod->nombre) }}',
                                    '{{ $prod->precio_venta }}',
                                    '{{ $prod->precio_compra }}',
                                    '{{ $prod->fecha_ingreso->format('Y-m-d') }}',
                                    '{{ addslashes($prod->proveedor ?? '') }}',
                                    '{{ $prod->categoria_id ?? '' }}',
                                    '{{ $prod->stockMinimo() }}',
                                    '{{ $prod->imagen ? asset('storage/' . $prod->imagen) : '' }}'
                                )">
                                <i class="fas fa-pen"></i> Editar
                            </button>

                            {{-- Toggle activo/inactivo --}}
                            <form method="POST" action="{{ route('productos.toggle', $prod) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-accion {{ $prod->estaActivo() ? 'btn-toggle-on' : 'btn-toggle-off' }}">
                                    <i class="fas {{ $prod->estaActivo() ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                    {{ $prod->estaActivo() ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>

                            {{-- Eliminar --}}
                            <form method="POST" action="{{ route('productos.destroy', $prod) }}" style="display:inline;"
                                  onsubmit="return confirmarEliminar(event, '{{ addslashes($prod->nombre) }}', {{ $prod->tieneDependencias() ? 'true' : 'false' }})">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-accion btn-eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="8">
                        <i class="fas fa-box-open"></i>
                        Aún no hay productos registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ═══════════════════════════════
     MODAL CREAR
════════════════════════════════ --}}
<div class="modal-overlay" id="modalCrear">
    <div class="modal-backdrop" onclick="cerrarModal('modalCrear')"></div>
    <div class="modal-box">
        <button class="modal-close" onclick="cerrarModal('modalCrear')"><i class="fas fa-xmark"></i></button>
        <div class="modal-title"><i class="fas fa-box-open"></i> Nuevo producto</div>

        <form method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-grid">
                <div class="form-group full">
                    <label class="form-label">Nombre <span style="color:#EF4444">*</span></label>
                    <input type="text" name="nombre" class="form-input {{ $errors->has('nombre') ? 'is-invalid' : '' }}"
                        value="{{ old('nombre') }}" placeholder="Ej: Manilla de flores" maxlength="255" autocomplete="off">
                    @error('nombre') <div class="invalid-msg">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Precio venta <span style="color:#EF4444">*</span></label>
                    <input type="number" name="precio_venta" class="form-input {{ $errors->has('precio_venta') ? 'is-invalid' : '' }}"
                        value="{{ old('precio_venta', 0) }}" min="0" step="100">
                    @error('precio_venta') <div class="invalid-msg">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Precio compra <span style="color:#EF4444">*</span></label>
                    <input type="number" name="precio_compra" class="form-input {{ $errors->has('precio_compra') ? 'is-invalid' : '' }}"
                        value="{{ old('precio_compra', 0) }}" min="0" step="100">
                    @error('precio_compra') <div class="invalid-msg">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Stock inicial <span style="color:#EF4444">*</span></label>
                    <input type="number" name="stock_inicial" class="form-input {{ $errors->has('stock_inicial') ? 'is-invalid' : '' }}"
                        value="{{ old('stock_inicial', 0) }}" min="0">
                    @error('stock_inicial') <div class="invalid-msg">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Stock mínimo <span style="color:#EF4444">*</span></label>
                    <input type="number" name="stock_minimo" class="form-input {{ $errors->has('stock_minimo') ? 'is-invalid' : '' }}"
                        value="{{ old('stock_minimo', 0) }}" min="0">
                    @error('stock_minimo') <div class="invalid-msg">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Fecha de ingreso <span style="color:#EF4444">*</span></label>
                    <input type="date" name="fecha_ingreso" class="form-input {{ $errors->has('fecha_ingreso') ? 'is-invalid' : '' }}"
                        value="{{ old('fecha_ingreso', now()->toDateString()) }}">
                    @error('fecha_ingreso') <div class="invalid-msg">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Categoría</label>
                    <select name="categoria_id" class="form-select">
                        <option value="">Sin categoría</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre_categoria }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group full">
                    <label class="form-label">Proveedor</label>
                    <input type="text" name="proveedor" class="form-input"
                        value="{{ old('proveedor') }}" placeholder="Nombre del proveedor" maxlength="200">
                </div>

                <div class="form-group full">
                    <label class="form-label">Imagen del producto</label>
                    <input type="file" name="imagen" class="form-input" accept="image/*"
                        onchange="previewImagen(this, 'previewCrear')">
                    <img id="previewCrear" class="img-preview" alt="Preview">
                    @error('imagen') <div class="invalid-msg">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancelar" onclick="cerrarModal('modalCrear')">Cancelar</button>
                <button type="submit" class="btn-guardar"><i class="fas fa-check"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════
     MODAL EDITAR
════════════════════════════════ --}}
<div class="modal-overlay" id="modalEditar">
    <div class="modal-backdrop" onclick="cerrarModal('modalEditar')"></div>
    <div class="modal-box">
        <button class="modal-close" onclick="cerrarModal('modalEditar')"><i class="fas fa-xmark"></i></button>
        <div class="modal-title"><i class="fas fa-pen"></i> Editar producto</div>

        <form method="POST" id="formEditar" action="" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="form-group full">
                    <label class="form-label">Nombre <span style="color:#EF4444">*</span></label>
                    <input type="text" id="edit_nombre" name="nombre" class="form-input" maxlength="255" autocomplete="off">
                </div>
                <div class="form-group">
                    <label class="form-label">Precio venta <span style="color:#EF4444">*</span></label>
                    <input type="number" id="edit_pventa" name="precio_venta" class="form-input" min="0" step="100">
                </div>
                <div class="form-group">
                    <label class="form-label">Precio compra <span style="color:#EF4444">*</span></label>
                    <input type="number" id="edit_pcompra" name="precio_compra" class="form-input" min="0" step="100">
                </div>
                <div class="form-group">
                    <label class="form-label">Stock mínimo <span style="color:#EF4444">*</span></label>
                    <input type="number" id="edit_minimo" name="stock_minimo" class="form-input" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha de ingreso <span style="color:#EF4444">*</span></label>
                    <input type="date" id="edit_fecha" name="fecha_ingreso" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Categoría</label>
                    <select id="edit_categoria" name="categoria_id" class="form-select">
                        <option value="">Sin categoría</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nombre_categoria }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group full">
                    <label class="form-label">Proveedor</label>
                    <input type="text" id="edit_proveedor" name="proveedor" class="form-input" maxlength="200">
                </div>
                <div class="form-group full">
                    <label class="form-label">Nueva imagen <span style="font-size:.76rem; color:#9CA3AF;">(deja vacío para conservar la actual)</span></label>
                    <input type="file" name="imagen" class="form-input" accept="image/*"
                        onchange="previewImagen(this, 'previewEditar')">
                    <img id="previewEditarActual" class="img-preview" alt="Imagen actual" style="display:block;">
                    <img id="previewEditar" class="img-preview" alt="Nueva imagen">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancelar" onclick="cerrarModal('modalEditar')">Cancelar</button>
                <button type="submit" class="btn-guardar"><i class="fas fa-check"></i> Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
    function cerrarModal(id) { document.getElementById(id).classList.remove('open'); }
    function abrirModalCrear() { document.getElementById('modalCrear').classList.add('open'); }

    function abrirModalEditar(id, nombre, pventa, pcompra, fecha, proveedor, catId, minimo, imgUrl) {
        document.getElementById('formEditar').action = '/productos/' + id;
        document.getElementById('edit_nombre').value    = nombre;
        document.getElementById('edit_pventa').value    = pventa;
        document.getElementById('edit_pcompra').value   = pcompra;
        document.getElementById('edit_fecha').value     = fecha;
        document.getElementById('edit_proveedor').value = proveedor;
        document.getElementById('edit_minimo').value    = minimo;

        // Seleccionar categoría
        const sel = document.getElementById('edit_categoria');
        for (let opt of sel.options) opt.selected = opt.value == catId;

        // Imagen actual
        const imgActual = document.getElementById('previewEditarActual');
        if (imgUrl) { imgActual.src = imgUrl; imgActual.style.display = 'block'; }
        else        { imgActual.style.display = 'none'; }

        document.getElementById('previewEditar').style.display = 'none';
        document.getElementById('modalEditar').classList.add('open');
    }

    function previewImagen(input, previewId) {
        const img = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => { img.src = e.target.result; img.style.display = 'block'; };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function confirmarEliminar(e, nombre, tieneDep) {
        if (tieneDep) {
            e.preventDefault();
            alert('No se puede eliminar "' + nombre + '": tiene ventas o movimientos registrados. Desactívalo en su lugar.');
            return false;
        }
        return confirm('¿Eliminar el producto "' + nombre + '"? Esta acción no se puede deshacer.');
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { cerrarModal('modalCrear'); cerrarModal('modalEditar'); }
    });

    @if($errors->any())
        abrirModalCrear();
    @endif
</script>
@endsection

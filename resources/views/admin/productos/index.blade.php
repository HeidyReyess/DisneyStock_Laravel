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
        border:1px solid #EDE9FE; cursor:zoom-in;
        transition:transform .2s, box-shadow .2s;
    }
    .img-thumb:hover { transform:scale(1.08); box-shadow:0 4px 16px rgba(74,29,150,.18); }
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
    @if(Auth::user()->esAdmin())
    <button type="button" class="btn-nuevo" onclick="abrirModalCrear()">
        <i class="fas fa-plus"></i> Nuevo producto
    </button>
    @endif
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
                            <img src="{{ asset('storage/' . $prod->imagen) }}"
                                 class="img-thumb"
                                 alt="{{ $prod->nombre }}"
                                 onclick="verImagen('{{ asset('storage/' . $prod->imagen) }}', '{{ addslashes($prod->nombre) }}')">
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
                            @if(Auth::user()->esAdmin())
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
                                  onsubmit="confirmarEliminar(event, '{{ addslashes($prod->nombre) }}', {{ $prod->tieneDependencias() ? 'true' : 'false' }}); return false;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-accion btn-eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @else
                            <span style="font-size:.8rem; color:#9CA3AF; font-style:italic;">Solo lectura</span>
                            @endif
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
                    {{-- [4] step="1", min="1", valor sin decimales --}}
                    <input type="number" name="precio_venta" class="form-input {{ $errors->has('precio_venta') ? 'is-invalid' : '' }}"
                        value="{{ old('precio_venta', '') }}" min="1" step="1" placeholder="Ej: 15000">
                    @error('precio_venta') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Precio compra <span style="color:#EF4444">*</span></label>
                    <input type="number" name="precio_compra" class="form-input {{ $errors->has('precio_compra') ? 'is-invalid' : '' }}"
                        value="{{ old('precio_compra', '') }}" min="1" step="1" placeholder="Ej: 8000">
                    @error('precio_compra') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Stock inicial <span style="color:#EF4444">*</span></label>
                    {{-- [6] min="0" (puede empezar en 0) --}}
                    <input type="number" name="stock_inicial" class="form-input {{ $errors->has('stock_inicial') ? 'is-invalid' : '' }}"
                        value="{{ old('stock_inicial', 0) }}" min="0" step="1">
                    @error('stock_inicial') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Stock mínimo <span style="color:#EF4444">*</span></label>
                    {{-- [6] min="1" —debe tener al menos 1 de mínimo --}}
                    <input type="number" name="stock_minimo" class="form-input {{ $errors->has('stock_minimo') ? 'is-invalid' : '' }}"
                        value="{{ old('stock_minimo', \App\Models\Configuracion::valor('stock_minimo_defecto', 1)) }}" min="1" step="1">
                    @error('stock_minimo') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Fecha de ingreso <span style="color:#EF4444">*</span></label>
                    <input type="date" name="fecha_ingreso" class="form-input {{ $errors->has('fecha_ingreso') ? 'is-invalid' : '' }}"
                        value="{{ old('fecha_ingreso', now()->toDateString()) }}">
                    @error('fecha_ingreso') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    {{-- [7] Categoría obligatoria --}}
                    <label class="form-label">Categoría <span style="color:#EF4444">*</span></label>
                    <select name="categoria_id" class="form-select {{ $errors->has('categoria_id') ? 'is-invalid' : '' }}">
                        <option value="" disabled>Selecciona una categoría...</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre_categoria }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoria_id') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
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

        <form method="POST" id="formEditar" action="{{ $productoEditando ? route('productos.update', $productoEditando) : '' }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            {{-- [8] Campo oculto para saber qué producto se estaba editando si hay error --}}
            <input type="hidden" id="edit_id" name="_edit_id" value="{{ $productoEditando?->id ?? '' }}">
            <div class="form-grid">
                <div class="form-group full">
                    <label class="form-label">Nombre <span style="color:#EF4444">*</span></label>
                    <input type="text" id="edit_nombre" name="nombre" class="form-input {{ $errors->has('nombre') ? 'is-invalid' : '' }}" maxlength="255" autocomplete="off"
                        value="{{ old('nombre', $productoEditando?->nombre ?? '') }}">
                    @error('nombre') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Precio venta <span style="color:#EF4444">*</span></label>
                    {{-- [4] step="1", valor entero en editar --}}
                    <input type="number" id="edit_pventa" name="precio_venta" class="form-input {{ $errors->has('precio_venta') ? 'is-invalid' : '' }}" min="1" step="1"
                        value="{{ old('precio_venta', $productoEditando?->precio_venta ?? '') }}">
                    @error('precio_venta') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Precio compra <span style="color:#EF4444">*</span></label>
                    <input type="number" id="edit_pcompra" name="precio_compra" class="form-input {{ $errors->has('precio_compra') ? 'is-invalid' : '' }}" min="1" step="1"
                        value="{{ old('precio_compra', $productoEditando?->precio_compra ?? '') }}">
                    @error('precio_compra') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Stock mínimo <span style="color:#EF4444">*</span></label>
                    <input type="number" id="edit_minimo" name="stock_minimo" class="form-input {{ $errors->has('stock_minimo') ? 'is-invalid' : '' }}" min="1" step="1"
                        value="{{ old('stock_minimo', $productoEditando?->stockMinimo() ?? '') }}">
                    @error('stock_minimo') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha de ingreso <span style="color:#EF4444">*</span></label>
                    <input type="date" id="edit_fecha" name="fecha_ingreso" class="form-input {{ $errors->has('fecha_ingreso') ? 'is-invalid' : '' }}"
                        value="{{ old('fecha_ingreso', $productoEditando?->fecha_ingreso?->format('Y-m-d') ?? '') }}">
                    @error('fecha_ingreso') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Categoría <span style="color:#EF4444">*</span></label>
                    <select id="edit_categoria" name="categoria_id" class="form-select {{ $errors->has('categoria_id') ? 'is-invalid' : '' }}">
                        <option value="" disabled>Selecciona una categoría...</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_id', $productoEditando?->categoria_id ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre_categoria }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoria_id') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>
                <div class="form-group full">
                    <label class="form-label">Proveedor</label>
                    <input type="text" id="edit_proveedor" name="proveedor" class="form-input" maxlength="200"
                        value="{{ old('proveedor', $productoEditando?->proveedor ?? '') }}">
                </div>
                <div class="form-group full">
                    <label class="form-label">Nueva imagen <span style="font-size:.76rem; color:#9CA3AF;">(deja vacío para conservar la actual)</span></label>
                    <input type="file" name="imagen" class="form-input" accept="image/*"
                        onchange="previewImagen(this, 'previewEditar')">
                    @if($productoEditando && $productoEditando->imagen)
                        <img id="previewEditarActual" class="img-preview" alt="Imagen actual" style="display:block;" src="{{ asset('storage/' . $productoEditando->imagen) }}">
                    @else
                        <img id="previewEditarActual" class="img-preview" alt="Imagen actual" style="display:none;">
                    @endif
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
        document.getElementById('formEditar').action = '{{ url('productos') }}/' + id;
        document.getElementById('edit_id').value       = id;    // [8] guardar id para reapertura
        document.getElementById('edit_nombre').value    = nombre;
        // [4] parseInt para mostrar valor sin decimales
        document.getElementById('edit_pventa').value    = parseInt(pventa);
        document.getElementById('edit_pcompra').value   = parseInt(pcompra);
        document.getElementById('edit_fecha').value     = fecha;
        document.getElementById('edit_proveedor').value = proveedor;
        document.getElementById('edit_minimo').value    = parseInt(minimo);

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
        e.preventDefault();
        if (tieneDep) {
            document.getElementById('modalAvisoMsg').textContent =
                'No se puede eliminar "' + nombre + '": tiene ventas o movimientos registrados. Desactívalo en su lugar.';
            document.getElementById('modalAviso').classList.add('open');
            return;
        }
        document.getElementById('modalConfirmTitle').innerHTML =
            '<i class="fas fa-trash" style="color:#DC2626;"></i> Eliminar producto';
        document.getElementById('modalConfirmMsg').textContent =
            '¿Seguro que deseas eliminar "' + nombre + '"?';
        const form = e.target.closest('form');
        document.getElementById('modalConfirmOk').onclick = function() {
            cerrarModal('modalConfirm');
            form.submit();
        };
        document.getElementById('modalConfirm').classList.add('open');
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { cerrarModal('modalCrear'); cerrarModal('modalEditar'); cerrarModal('modalConfirm'); cerrarModal('modalAviso'); }
    });

    @if($errors->any())
        @php
            // [13] Usar old('_edit_id') en vez de preg_match sobre la URL — más robusto
            $editId = old('_edit_id');
        @endphp
        @if($editId)
            {{-- Solo abre el modal; los campos ya están llenos desde Blade --}}
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('modalEditar').classList.add('open');
            });
        @else
            {{-- Reabre modal crear solo si se envió el formulario de crear (stock_inicial es exclusivo de crear) --}}
            @if(old('stock_inicial') !== null)
                abrirModalCrear();
            @endif
        @endif
    @endif
</script>

{{-- ═══════════════════════════════
     MODAL CONFIRMAR ACCIÓN
════════════════════════════════ --}}
<div class="modal-overlay" id="modalConfirm">
    <div class="modal-backdrop" onclick="cerrarModal('modalConfirm')"></div>
    <div class="modal-box" style="max-width:420px;">
        <button class="modal-close" onclick="cerrarModal('modalConfirm')"><i class="fas fa-xmark"></i></button>
        <div class="modal-title" id="modalConfirmTitle" style="margin-bottom:12px;"></div>
        <p id="modalConfirmMsg" style="font-size:.88rem;color:#374151;margin-bottom:22px;"></p>
        <div class="modal-footer" style="margin-top:0;">
            <button id="modalConfirmCancel" type="button" class="btn-cancelar" onclick="cerrarModal('modalConfirm')">Cancelar</button>
            <button id="modalConfirmOk" type="button" class="btn-guardar" style="background:#DC2626;border-color:#DC2626;">Eliminar</button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════
     MODAL AVISO (no se puede eliminar)
════════════════════════════════ --}}
<div class="modal-overlay" id="modalAviso">
    <div class="modal-backdrop" onclick="cerrarModal('modalAviso')"></div>
    <div class="modal-box" style="max-width:420px;">
        <button class="modal-close" onclick="cerrarModal('modalAviso')"><i class="fas fa-xmark"></i></button>
        <div class="modal-title" style="margin-bottom:12px;"><i class="fas fa-circle-exclamation" style="color:#DC2626;"></i> No se puede eliminar</div>
        <p id="modalAvisoMsg" style="font-size:.88rem;color:#374151;margin-bottom:22px;"></p>
        <div class="modal-footer" style="margin-top:0;">
            <button type="button" class="btn-guardar" onclick="cerrarModal('modalAviso')">Entendido</button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════
     MODAL VER IMAGEN
════════════════════════════════ --}}
<div class="modal-overlay" id="modalImagen">
    <div class="modal-backdrop" onclick="cerrarModal('modalImagen')"></div>
    <div class="modal-box" style="max-width:500px; padding:20px; text-align:center;">
        <button class="modal-close" onclick="cerrarModal('modalImagen')"><i class="fas fa-xmark"></i></button>
        <div style="font-family:'Outfit',sans-serif; font-weight:700; font-size:1rem; color:#1E1B4B; margin-bottom:14px;" id="modalImagenNombre"></div>
        <img id="modalImagenSrc" src="" alt=""
            style="max-width:100%; max-height:70vh; border-radius:12px; object-fit:contain; border:1px solid #EDE9FE; box-shadow:0 4px 24px rgba(74,29,150,.12);">
    </div>
</div>

<script>
    function verImagen(url, nombre) {
        document.getElementById('modalImagenSrc').src    = url;
        document.getElementById('modalImagenNombre').textContent = nombre;
        document.getElementById('modalImagen').classList.add('open');
    }
</script>

@endsection

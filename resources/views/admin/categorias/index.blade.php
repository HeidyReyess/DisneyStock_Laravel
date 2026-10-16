@extends('layouts.sidebar')

@section('title', 'Categorías')
@section('page-title', 'Categorías')

@section('content')
<style>
    /* ── Toolbar ── */
    .toolbar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; margin-bottom:24px; }
    .btn-nuevo {
        display:inline-flex; align-items:center; gap:8px;
        padding:10px 20px; background:#4A1D96; color:#fff;
        border:2px solid #4A1D96; border-radius:10px;
        font-family:'Outfit',sans-serif; font-size:.9rem; font-weight:700;
        text-decoration:none; cursor:pointer; transition:all .2s;
    }
    .btn-nuevo:hover { background:#7C3AED; border-color:#7C3AED; }

    /* ── Tabla ── */
    .table-wrap { overflow-x:auto; }
    table { width:100%; border-collapse:collapse; font-size:.88rem; }
    thead th {
        padding:12px 16px; text-align:left;
        font-size:.72rem; font-weight:700; letter-spacing:.08em;
        text-transform:uppercase; color:#6B7280;
        border-bottom:2px solid #F5F3FF; white-space:nowrap;
    }
    tbody td { padding:13px 16px; border-bottom:1px solid #F9F5FF; color:#374151; vertical-align:middle; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover td { background:#FAFAFF; }

    /* ── Badge contador ── */
    .badge-count {
        display:inline-flex; align-items:center; justify-content:center;
        min-width:22px; height:22px; padding:0 6px;
        background:#EDE9FE; color:#5B21B6;
        border-radius:20px; font-size:.75rem; font-weight:700;
    }
    .badge-count.zero { background:#F3F4F6; color:#9CA3AF; }

    /* ── Botones acción ── */
    .btn-accion {
        display:inline-flex; align-items:center; gap:5px;
        padding:6px 12px; border-radius:8px;
        font-size:.8rem; font-weight:600; border:none; cursor:pointer;
        transition:all .2s; text-decoration:none;
    }
    .btn-editar  { background:#EDE9FE; color:#5B21B6; }
    .btn-editar:hover  { background:#DDD6FE; }
    .btn-eliminar { background:#FEE2E2; color:#991B1B; }
    .btn-eliminar:hover { background:#FECACA; }
    .btn-eliminar:disabled { opacity:.45; cursor:not-allowed; }

    /* ── Empty state ── */
    .empty-row td { text-align:center; padding:40px 16px; color:#9CA3AF; }
    .empty-row i  { font-size:1.8rem; color:#C4B5FD; display:block; margin-bottom:8px; }

    /* ── Modal ── */
    .modal-overlay {
        display:none; position:fixed; inset:0; z-index:2000;
        align-items:center; justify-content:center; padding:24px;
    }
    .modal-overlay.open { display:flex; }
    .modal-backdrop { position:absolute; inset:0; background:rgba(30,7,71,.45); }
    .modal-box {
        position:relative; background:#fff; border-radius:16px;
        max-width:480px; width:100%; padding:36px 32px;
        box-shadow:0 20px 60px rgba(74,29,150,.18);
    }
    .modal-close {
        position:absolute; top:14px; right:16px;
        background:none; border:none; font-size:1.2rem;
        color:#9CA3AF; cursor:pointer;
    }
    .modal-close:hover { color:#4A1D96; }
    .modal-title {
        font-family:'Outfit',sans-serif; font-size:1.15rem;
        font-weight:800; color:#1E1B4B; margin-bottom:20px;
        display:flex; align-items:center; gap:10px;
    }
    .modal-title i { color:#7C3AED; }

    /* ── Form ── */
    .form-group { margin-bottom:18px; }
    .form-label { display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:6px; }
    .form-input, .form-textarea {
        width:100%; padding:10px 14px;
        border:1.5px solid #E5E7EB; border-radius:9px;
        font-size:.92rem; color:#1E1B4B; font-family:'Inter',sans-serif;
        transition:border-color .2s, box-shadow .2s; background:#FAFAFA;
        box-sizing:border-box;
    }
    .form-input:focus, .form-textarea:focus {
        outline:none; border-color:#7C3AED;
        box-shadow:0 0 0 3px rgba(124,58,237,.1); background:#fff;
    }
    .form-textarea { resize:vertical; min-height:80px; }
    .form-input.is-invalid, .form-textarea.is-invalid { border-color:#EF4444; }
    .invalid-msg { font-size:.77rem; color:#EF4444; margin-top:4px; display:flex; align-items:center; gap:4px; }

    .modal-footer { display:flex; gap:10px; justify-content:flex-end; margin-top:24px; }
    .btn-cancelar {
        padding:9px 20px; background:#F5F3FF; color:#5B21B6;
        border:1.5px solid #DDD6FE; border-radius:9px;
        font-size:.88rem; font-weight:600; cursor:pointer; transition:all .2s;
    }
    .btn-cancelar:hover { background:#EDE9FE; }
    .btn-guardar {
        padding:9px 22px; background:#4A1D96; color:#fff;
        border:2px solid #4A1D96; border-radius:9px;
        font-family:'Outfit',sans-serif; font-size:.9rem; font-weight:700;
        cursor:pointer; transition:all .2s;
    }
    .btn-guardar:hover { background:#7C3AED; border-color:#7C3AED; }
</style>

{{-- Toolbar --}}
<div class="toolbar">
    <div>
        <p style="font-size:.85rem; color:#6B7280; margin-top:4px;">
            {{ $categorias->count() }} {{ $categorias->count() === 1 ? 'categoría registrada' : 'categorías registradas' }}
        </p>
    </div>
    <button class="btn-nuevo" onclick="abrirModalCrear()">
        <i class="fas fa-plus"></i> Nueva categoría
    </button>
</div>

{{-- Tabla --}}
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Productos</th>
                    <th>Creada</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categorias as $cat)
                <tr>
                    <td style="color:#9CA3AF; font-size:.8rem;">{{ $cat->id }}</td>
                    <td style="font-weight:600; color:#1E1B4B;">{{ $cat->nombre_categoria }}</td>
                    <td style="color:#6B7280;">{{ $cat->descripcion ?? '—' }}</td>
                    <td>
                        <span class="badge-count {{ $cat->productos_count === 0 ? 'zero' : '' }}">
                            {{ $cat->productos_count }}
                        </span>
                    </td>
                    <td style="color:#9CA3AF; font-size:.82rem;">{{ $cat->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div style="display:flex; gap:6px;">
                            <button class="btn-accion btn-editar"
                                onclick="abrirModalEditar({{ $cat->id }}, '{{ addslashes($cat->nombre_categoria) }}', '{{ addslashes($cat->descripcion ?? '') }}')">
                                <i class="fas fa-pen"></i> Editar
                            </button>
                            <form method="POST" action="{{ route('categorias.destroy', $cat) }}"
                                  onsubmit="return confirmarEliminar(event, '{{ addslashes($cat->nombre_categoria) }}', {{ $cat->productos_count }})">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-accion btn-eliminar"
                                    {{ $cat->productos_count > 0 ? 'disabled title=Tiene productos asociados' : '' }}>
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="6">
                        <i class="fas fa-tags"></i>
                        Aún no hay categorías registradas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ═══════════════════════════
     MODAL CREAR
════════════════════════════ --}}
<div class="modal-overlay" id="modalCrear">
    <div class="modal-backdrop" onclick="cerrarModal('modalCrear')"></div>
    <div class="modal-box">
        <button class="modal-close" onclick="cerrarModal('modalCrear')"><i class="fas fa-xmark"></i></button>
        <div class="modal-title"><i class="fas fa-tags"></i> Nueva categoría</div>

        <form method="POST" action="{{ route('categorias.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label" for="nombre_crear">Nombre <span style="color:#EF4444">*</span></label>
                <input type="text" id="nombre_crear" name="nombre_categoria"
                    class="form-input {{ $errors->has('nombre_categoria') ? 'is-invalid' : '' }}"
                    value="{{ old('nombre_categoria') }}"
                    placeholder="Ej: Manillas" maxlength="100" autocomplete="off">
                @error('nombre_categoria')
                    <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="desc_crear">Descripción</label>
                <textarea id="desc_crear" name="descripcion"
                    class="form-textarea {{ $errors->has('descripcion') ? 'is-invalid' : '' }}"
                    placeholder="Descripción opcional..." maxlength="200">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                @enderror
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancelar" onclick="cerrarModal('modalCrear')">Cancelar</button>
                <button type="submit" class="btn-guardar"><i class="fas fa-check"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════
     MODAL EDITAR
════════════════════════════ --}}
<div class="modal-overlay" id="modalEditar">
    <div class="modal-backdrop" onclick="cerrarModal('modalEditar')"></div>
    <div class="modal-box">
        <button class="modal-close" onclick="cerrarModal('modalEditar')"><i class="fas fa-xmark"></i></button>
        <div class="modal-title"><i class="fas fa-pen"></i> Editar categoría</div>

        <form method="POST" id="formEditar" action="">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label" for="nombre_editar">Nombre <span style="color:#EF4444">*</span></label>
                <input type="text" id="nombre_editar" name="nombre_categoria"
                    class="form-input" placeholder="Nombre de la categoría"
                    maxlength="100" autocomplete="off">
            </div>
            <div class="form-group">
                <label class="form-label" for="desc_editar">Descripción</label>
                <textarea id="desc_editar" name="descripcion"
                    class="form-textarea" placeholder="Descripción opcional..."
                    maxlength="200"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancelar" onclick="cerrarModal('modalEditar')">Cancelar</button>
                <button type="submit" class="btn-guardar"><i class="fas fa-check"></i> Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
    // ── Abrir / cerrar modales ──────────────────────────────
    function abrirModalCrear() {
        document.getElementById('modalCrear').classList.add('open');
    }
    function abrirModalEditar(id, nombre, desc) {
        document.getElementById('formEditar').action = '/categorias/' + id;
        document.getElementById('nombre_editar').value = nombre;
        document.getElementById('desc_editar').value   = desc;
        document.getElementById('modalEditar').classList.add('open');
    }
    function cerrarModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    // Cerrar con Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            cerrarModal('modalCrear');
            cerrarModal('modalEditar');
        }
    });

    // Abrir modal crear si hubo error de validación (al volver de POST)
    @if($errors->any())
        abrirModalCrear();
    @endif

    // ── Confirmar eliminación ───────────────────────────────
    function confirmarEliminar(e, nombre, count) {
        if (count > 0) { e.preventDefault(); return false; }
        return confirm('¿Eliminar la categoría "' + nombre + '"? Esta acción no se puede deshacer.');
    }
</script>
@endsection

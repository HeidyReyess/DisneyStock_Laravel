@extends('layouts.sidebar')

@section('title', 'Usuarios')
@section('page-title', 'Usuarios')

@section('content')
<style>
    .toolbar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; margin-bottom:22px; }
    .btn-nuevo { display:inline-flex; align-items:center; gap:8px; padding:10px 20px; background:#4A1D96; color:#fff; border:2px solid #4A1D96; border-radius:10px; font-family:'Outfit',sans-serif; font-size:.9rem; font-weight:700; cursor:pointer; transition:all .2s; white-space:nowrap; }
    .btn-nuevo:hover { background:#7C3AED; border-color:#7C3AED; }

    /* ── Tabla ── */
    .table-wrap { overflow-x:auto; }
    table { width:100%; border-collapse:collapse; font-size:.87rem; }
    thead th { padding:12px 16px; text-align:left; font-size:.71rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#6B7280; border-bottom:2px solid #F5F3FF; white-space:nowrap; }
    tbody td { padding:12px 16px; border-bottom:1px solid #F9F5FF; color:#374151; vertical-align:middle; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover td { background:#FAFAFF; }

    /* ── Avatar ── */
    .avatar { width:36px; height:36px; border-radius:50%; background:#4A1D96; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:.9rem; flex-shrink:0; }

    /* ── Badges ── */
    .badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:.74rem; font-weight:600; }
    .badge-activo      { background:#D1FAE5; color:#065F46; }
    .badge-inactivo    { background:#F3F4F6; color:#6B7280; }
    .badge-admin       { background:#EDE9FE; color:#5B21B6; }
    .badge-empleado    { background:#F0FDF4; color:#166534; }

    /* ── Botones acción ── */
    .btn-accion { display:inline-flex; align-items:center; gap:5px; padding:5px 11px; border-radius:8px; font-size:.79rem; font-weight:600; border:none; cursor:pointer; transition:all .2s; text-decoration:none; white-space:nowrap; }
    .btn-editar    { background:#EDE9FE; color:#5B21B6; }
    .btn-editar:hover    { background:#DDD6FE; }
    .btn-pass      { background:#FEF3C7; color:#92400E; }
    .btn-pass:hover      { background:#FDE68A; }
    .btn-toggle-on  { background:#D1FAE5; color:#065F46; }
    .btn-toggle-on:hover  { background:#A7F3D0; }
    .btn-toggle-off { background:#F3F4F6; color:#6B7280; }
    .btn-toggle-off:hover { background:#E5E7EB; }
    .btn-eliminar  { background:#FEE2E2; color:#991B1B; }
    .btn-eliminar:hover  { background:#FECACA; }

    /* ── Propia cuenta ── */
    .own-row td { background:#FAFAFA; }

    /* ── Empty ── */
    .empty-row td { text-align:center; padding:40px; color:#9CA3AF; }
    .empty-row i  { font-size:1.8rem; color:#C4B5FD; display:block; margin-bottom:8px; }

    /* ── Modales ── */
    .modal-overlay { display:none; position:fixed; inset:0; z-index:2000; align-items:center; justify-content:center; padding:20px; }
    .modal-overlay.open { display:flex; }
    .modal-backdrop { position:absolute; inset:0; background:rgba(30,7,71,.45); }
    .modal-box { position:relative; background:#fff; border-radius:16px; width:100%; max-width:480px; padding:32px 28px; box-shadow:0 20px 60px rgba(74,29,150,.18); }
    .modal-close { position:absolute; top:14px; right:16px; background:none; border:none; font-size:1.2rem; color:#9CA3AF; cursor:pointer; }
    .modal-close:hover { color:#4A1D96; }
    .modal-title { font-family:'Outfit',sans-serif; font-size:1.1rem; font-weight:800; color:#1E1B4B; margin-bottom:20px; display:flex; align-items:center; gap:10px; }
    .modal-title i { color:#7C3AED; }
    .form-group { margin-bottom:16px; }
    .form-label { display:block; font-size:.81rem; font-weight:600; color:#374151; margin-bottom:5px; }
    .form-input, .form-select { width:100%; padding:9px 12px; border:1.5px solid #E5E7EB; border-radius:9px; font-size:.9rem; color:#1E1B4B; font-family:'Inter',sans-serif; transition:border-color .2s; background:#FAFAFA; box-sizing:border-box; }
    .form-input:focus, .form-select:focus { outline:none; border-color:#7C3AED; box-shadow:0 0 0 3px rgba(124,58,237,.1); background:#fff; }
    .form-input.is-invalid { border-color:#EF4444; }
    .invalid-msg { font-size:.76rem; color:#EF4444; margin-top:3px; }
    .input-wrap { position:relative; }
    .input-wrap i.ico { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#9CA3AF; font-size:.88rem; pointer-events:none; }
    .input-wrap .form-input { padding-left:36px; }
    .btn-eye { position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; color:#9CA3AF; cursor:pointer; font-size:.9rem; padding:4px; }
    .btn-eye:hover { color:#7C3AED; }
    .modal-footer { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; }
    .btn-cancelar { padding:9px 18px; background:#F5F3FF; color:#5B21B6; border:1.5px solid #DDD6FE; border-radius:9px; font-size:.87rem; font-weight:600; cursor:pointer; }
    .btn-cancelar:hover { background:#EDE9FE; }
    .btn-guardar { padding:9px 22px; background:#4A1D96; color:#fff; border:2px solid #4A1D96; border-radius:9px; font-family:'Outfit',sans-serif; font-size:.88rem; font-weight:700; cursor:pointer; }
    .btn-guardar:hover { background:#7C3AED; border-color:#7C3AED; }

    /* Info de cuenta propia */
    .own-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:.73rem; font-weight:600; background:#EDE9FE; color:#5B21B6; }
</style>

{{-- Toolbar --}}
<div class="toolbar">
    <p style="font-size:.85rem; color:#6B7280;">
        {{ $usuarios->count() }} {{ $usuarios->count() === 1 ? 'usuario registrado' : 'usuarios registrados' }}
    </p>
    <button class="btn-nuevo" onclick="abrirModal('modalCrear')">
        <i class="fas fa-user-plus"></i> Nuevo usuario
    </button>
</div>

{{-- Tabla --}}
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th colspan="2">Usuario</th>
                    <th>Nombre de usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Registrado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $u)
                <tr class="{{ $u->id === Auth::id() ? 'own-row' : '' }}">
                    <td style="width:44px; padding-right:0;">
                        <div class="avatar">{{ strtoupper(substr($u->nombre, 0, 1)) }}</div>
                    </td>
                    <td>
                        <div style="font-weight:600; color:#1E1B4B;">
                            {{ $u->nombre }}
                            @if($u->id === Auth::id())
                                <span class="own-badge" style="margin-left:6px;"><i class="fas fa-user"></i> Tú</span>
                            @endif
                        </div>
                    </td>
                    <td style="color:#6B7280; font-family:monospace; font-size:.85rem;">{{ $u->usuario }}</td>
                    <td>
                        <span class="badge {{ $u->rol?->nombre === 'administrador' ? 'badge-admin' : 'badge-empleado' }}">
                            <i class="fas {{ $u->rol?->nombre === 'administrador' ? 'fa-shield-halved' : 'fa-user' }}"></i>
                            {{ ucfirst($u->rol?->nombre ?? '—') }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $u->estado === 'activo' ? 'badge-activo' : 'badge-inactivo' }}">
                            {{ ucfirst($u->estado) }}
                        </span>
                    </td>
                    <td style="color:#9CA3AF; font-size:.8rem;">{{ $u->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div style="display:flex; gap:5px; flex-wrap:wrap;">
                            {{-- Editar --}}
                            <button class="btn-accion btn-editar"
                                onclick="abrirModalEditar({{ $u->id }}, '{{ addslashes($u->nombre) }}', '{{ addslashes($u->usuario) }}', {{ $u->rol_id }})">
                                <i class="fas fa-pen"></i> Editar
                            </button>

                            {{-- Restablecer contraseña --}}
                            <button class="btn-accion btn-pass"
                                onclick="abrirModalPass({{ $u->id }}, '{{ addslashes($u->usuario) }}')">
                                <i class="fas fa-key"></i>
                            </button>

                            @if($u->id !== Auth::id())
                                {{-- Toggle activo/inactivo --}}
                                <form method="POST" action="{{ route('usuarios.toggle', $u) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-accion {{ $u->estado === 'activo' ? 'btn-toggle-on' : 'btn-toggle-off' }}">
                                        <i class="fas {{ $u->estado === 'activo' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                    </button>
                                </form>

                                {{-- Eliminar --}}
                                <form method="POST" action="{{ route('usuarios.destroy', $u) }}" style="display:inline;"
                                    onsubmit="confirmarEliminarUsuario(event, '{{ addslashes($u->usuario) }}'); return false;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-accion btn-eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="7">
                        <i class="fas fa-users"></i>
                        Aún no hay usuarios registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ════════════════════════
     MODAL CREAR
════════════════════════ --}}
<div class="modal-overlay" id="modalCrear">
    <div class="modal-backdrop" onclick="cerrarModal('modalCrear')"></div>
    <div class="modal-box">
        <button class="modal-close" onclick="cerrarModal('modalCrear')"><i class="fas fa-xmark"></i></button>
        <div class="modal-title"><i class="fas fa-user-plus"></i> Nuevo usuario</div>

        <form method="POST" action="{{ route('usuarios.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nombre completo <span style="color:#EF4444">*</span></label>
                <div class="input-wrap">
                    <i class="fas fa-id-card ico"></i>
                    <input type="text" name="nombre" class="form-input {{ $errors->has('nombre') ? 'is-invalid' : '' }}"
                        value="{{ old('nombre') }}" placeholder="Ej: Ana García" maxlength="150" autocomplete="off">
                </div>
                @error('nombre') <div class="invalid-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nombre de usuario <span style="color:#EF4444">*</span></label>
                <div class="input-wrap">
                    <i class="fas fa-at ico"></i>
                    <input type="text" name="usuario" class="form-input {{ $errors->has('usuario') ? 'is-invalid' : '' }}"
                        value="{{ old('usuario') }}" placeholder="Ej: ana.garcia" maxlength="80" autocomplete="off">
                </div>
                @error('usuario') <div class="invalid-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Rol <span style="color:#EF4444">*</span></label>
                <select name="rol_id" class="form-select {{ $errors->has('rol_id') ? 'is-invalid' : '' }}">
                    <option value="">Selecciona un rol...</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}" {{ old('rol_id') == $rol->id ? 'selected' : '' }}>
                            {{ ucfirst($rol->nombre) }}
                        </option>
                    @endforeach
                </select>
                @error('rol_id') <div class="invalid-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Contraseña <span style="color:#EF4444">*</span></label>
                <div class="input-wrap">
                    <i class="fas fa-lock ico"></i>
                    <input type="password" name="password" id="pwdCrear" class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="Mínimo 8 caracteres">
                    <button type="button" class="btn-eye" onclick="togglePwd('pwdCrear', 'eyeCrear')">
                        <i class="fas fa-eye" id="eyeCrear"></i>
                    </button>
                </div>
                @error('password') <div class="invalid-msg">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Confirmar contraseña <span style="color:#EF4444">*</span></label>
                <div class="input-wrap">
                    <i class="fas fa-lock ico"></i>
                    <input type="password" name="password_confirmation" class="form-input"
                        placeholder="Repite la contraseña">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancelar" onclick="cerrarModal('modalCrear')">Cancelar</button>
                <button type="submit" class="btn-guardar"><i class="fas fa-check"></i> Crear usuario</button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════
     MODAL EDITAR
════════════════════════ --}}
<div class="modal-overlay" id="modalEditar">
    <div class="modal-backdrop" onclick="cerrarModal('modalEditar')"></div>
    <div class="modal-box">
        <button class="modal-close" onclick="cerrarModal('modalEditar')"><i class="fas fa-xmark"></i></button>
        <div class="modal-title"><i class="fas fa-pen"></i> Editar usuario</div>

        <form method="POST" id="formEditar" action="">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nombre completo <span style="color:#EF4444">*</span></label>
                <div class="input-wrap">
                    <i class="fas fa-id-card ico"></i>
                    <input type="text" id="edit_nombre" name="nombre" class="form-input" maxlength="150" autocomplete="off">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Nombre de usuario <span style="color:#EF4444">*</span></label>
                <div class="input-wrap">
                    <i class="fas fa-at ico"></i>
                    <input type="text" id="edit_usuario" name="usuario" class="form-input" maxlength="80" autocomplete="off">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Rol <span style="color:#EF4444">*</span></label>
                <select id="edit_rol" name="rol_id" class="form-select">
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}">{{ ucfirst($rol->nombre) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancelar" onclick="cerrarModal('modalEditar')">Cancelar</button>
                <button type="submit" class="btn-guardar"><i class="fas fa-check"></i> Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════
     MODAL RESTABLECER CONTRASEÑA
════════════════════════ --}}
<div class="modal-overlay" id="modalPass">
    <div class="modal-backdrop" onclick="cerrarModal('modalPass')"></div>
    <div class="modal-box">
        <button class="modal-close" onclick="cerrarModal('modalPass')"><i class="fas fa-xmark"></i></button>
        <div class="modal-title"><i class="fas fa-key"></i> Restablecer contraseña</div>
        <p style="font-size:.86rem; color:#6B7280; margin-bottom:18px;">
            Estás restableciendo la contraseña de <strong id="passUsuarioNombre"></strong>.
        </p>

        <form method="POST" id="formPass" action="">
            @csrf @method('PATCH')
            <div class="form-group">
                <label class="form-label">Nueva contraseña <span style="color:#EF4444">*</span></label>
                <div class="input-wrap">
                    <i class="fas fa-lock ico"></i>
                    <input type="password" name="password" id="pwdReset" class="form-input"
                        placeholder="Mínimo 8 caracteres">
                    <button type="button" class="btn-eye" onclick="togglePwd('pwdReset', 'eyeReset')">
                        <i class="fas fa-eye" id="eyeReset"></i>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Confirmar contraseña <span style="color:#EF4444">*</span></label>
                <div class="input-wrap">
                    <i class="fas fa-lock ico"></i>
                    <input type="password" name="password_confirmation" class="form-input"
                        placeholder="Repite la contraseña">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancelar" onclick="cerrarModal('modalPass')">Cancelar</button>
                <button type="submit" class="btn-guardar"><i class="fas fa-check"></i> Restablecer</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal confirmar eliminar usuario --}}
<div class="modal-overlay" id="modalConfirmUsuario">
    <div class="modal-backdrop" onclick="cerrarModal('modalConfirmUsuario')"></div>
    <div class="modal-box" style="max-width:420px;">
        <button class="modal-close" onclick="cerrarModal('modalConfirmUsuario')"><i class="fas fa-xmark"></i></button>
        <div class="modal-title" style="margin-bottom:12px;"><i class="fas fa-trash" style="color:#DC2626;"></i> Eliminar usuario</div>
        <p id="modalConfirmUsuarioMsg" style="font-size:.88rem;color:#374151;margin-bottom:22px;"></p>
        <div class="modal-footer" style="margin-top:0;">
            <button type="button" class="btn-cancelar" onclick="cerrarModal('modalConfirmUsuario')">Cancelar</button>
            <button id="modalConfirmUsuarioOk" type="button" class="btn-guardar" style="background:#DC2626;border-color:#DC2626;">Eliminar</button>
        </div>
    </div>
</div>

<script>
    function abrirModal(id)  { document.getElementById(id).classList.add('open'); }
    function cerrarModal(id) { document.getElementById(id).classList.remove('open'); }

    function confirmarEliminarUsuario(e, usuario) {
        e.preventDefault();
        document.getElementById('modalConfirmUsuarioMsg').textContent =
            '¿Seguro que deseas eliminar al usuario "' + usuario + '"?';
        const form = e.target.closest('form');
        document.getElementById('modalConfirmUsuarioOk').onclick = function() {
            cerrarModal('modalConfirmUsuario');
            form.submit();
        };
        abrirModal('modalConfirmUsuario');
    }

    function abrirModalEditar(id, nombre, usuario, rolId) {
        document.getElementById('formEditar').action = '{{ url('usuarios') }}/' + id;
        document.getElementById('edit_nombre').value  = nombre;
        document.getElementById('edit_usuario').value = usuario;
        const sel = document.getElementById('edit_rol');
        for (let opt of sel.options) opt.selected = opt.value == rolId;
        abrirModal('modalEditar');
    }

    function abrirModalPass(id, usuario) {
        document.getElementById('formPass').action = '{{ url('usuarios') }}/' + id + '/reset-password';
        document.getElementById('passUsuarioNombre').textContent = usuario;
        document.getElementById('pwdReset').value = '';
        abrirModal('modalPass');
    }

    function togglePwd(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            ['modalCrear','modalEditar','modalPass','modalConfirmUsuario'].forEach(cerrarModal);
        }
    });

    @if($errors->any())
        abrirModal('modalCrear');
    @endif
</script>
@endsection

@extends('layouts.sidebar')

@section('title', 'Configuración')
@section('page-title', 'Configuración del sistema')

@section('content')
<style>
    .config-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 700px) { .config-grid { grid-template-columns: 1fr; } }

    .form-section-title {
        font-family: 'Outfit', sans-serif; font-size: .8rem; font-weight: 700;
        letter-spacing: .09em; text-transform: uppercase; color: #7C3AED;
        padding: 0 0 10px; margin-bottom: 16px;
        border-bottom: 1.5px solid #EDE9FE;
        grid-column: 1 / -1;
    }
    .form-group { display: flex; flex-direction: column; gap: 5px; }
    .form-label { font-size: .82rem; font-weight: 600; color: #374151; }
    .form-label .hint { font-weight: 400; color: #9CA3AF; margin-left: 4px; font-size: .77rem; }
    .form-input, .form-select {
        width: 100%; padding: 9px 13px; border: 1.5px solid #E5E7EB; border-radius: 9px;
        font-size: .9rem; color: #1E1B4B; background: #FAFAFA;
        font-family: 'Inter', sans-serif; transition: border-color .2s; box-sizing: border-box;
    }
    .form-input:focus, .form-select:focus {
        outline: none; border-color: #7C3AED;
        box-shadow: 0 0 0 3px rgba(124,58,237,.1); background: #fff;
    }
    .form-input.is-invalid { border-color: #EF4444; }
    .invalid-msg { font-size: .76rem; color: #EF4444; margin-top: 2px; }
    .form-hint { font-size: .75rem; color: #9CA3AF; margin-top: 3px; }

    .btn-guardar {
        padding: 10px 28px; background: #4A1D96; color: #fff;
        border: 2px solid #4A1D96; border-radius: 10px;
        font-family: 'Outfit', sans-serif; font-size: .92rem; font-weight: 700;
        cursor: pointer; transition: all .2s;
    }
    .btn-guardar:hover { background: #7C3AED; border-color: #7C3AED; }

    /* ── Tabla de copias de seguridad ── */
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: .87rem; }
    thead th {
        padding: 12px 16px; text-align: left;
        font-size: .71rem; font-weight: 700; letter-spacing: .08em;
        text-transform: uppercase; color: #6B7280;
        border-bottom: 2px solid #F5F3FF; white-space: nowrap;
    }
    tbody td { padding: 12px 16px; border-bottom: 1px solid #F9F5FF; color: #374151; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover td { background: #FAFAFF; }
    .btn-accion {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 12px; border-radius: 8px;
        font-size: .8rem; font-weight: 600; border: none; cursor: pointer;
        transition: all .2s; text-decoration: none; white-space: nowrap;
    }
    .btn-descargar { background: #EDE9FE; color: #5B21B6; }
    .btn-descargar:hover { background: #DDD6FE; }
    .btn-eliminar { background: #FEE2E2; color: #991B1B; }
    .btn-eliminar:hover { background: #FECACA; }
    .modal-overlay { display: none; position: fixed; inset: 0; z-index: 2000; align-items: center; justify-content: center; padding: 20px; }
    .modal-overlay.open { display: flex; }
    .modal-backdrop { position: absolute; inset: 0; background: rgba(30, 7, 71, .45); }
    .modal-box { position: relative; background: #fff; border-radius: 16px; width: 100%; max-width: 440px; padding: 28px 24px; box-shadow: 0 20px 60px rgba(74, 29, 150, .18); }
    .modal-close { position: absolute; top: 14px; right: 16px; background: none; border: none; font-size: 1.2rem; color: #9CA3AF; cursor: pointer; }
    .modal-close:hover { color: #4A1D96; }
    .modal-title { font-family: 'Outfit', sans-serif; font-size: 1.1rem; font-weight: 800; color: #1E1B4B; margin-bottom: 14px; display: flex; align-items: center; gap: 10px; }
    .modal-footer { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
    .btn-cancelar { padding: 9px 18px; background: #F5F3FF; color: #5B21B6; border: 1.5px solid #DDD6FE; border-radius: 9px; font-size: .87rem; font-weight: 600; cursor: pointer; }
    .btn-cancelar:hover { background: #EDE9FE; }
</style>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-gear" style="color:#7C3AED;margin-right:8px;"></i>Parámetros del sistema</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('configuracion.update') }}">
            @csrf

            <div class="config-grid">

                {{-- ── Datos del negocio ── --}}
                <div class="form-section-title"><i class="fas fa-store" style="margin-right:6px;"></i>Datos del negocio</div>

                <div class="form-group">
                    <label class="form-label">
                        Nombre del negocio <span style="color:#EF4444">*</span>
                    </label>
                    <input type="text" name="nombre_negocio"
                        class="form-input {{ $errors->has('nombre_negocio') ? 'is-invalid' : '' }}"
                        value="{{ old('nombre_negocio', $config['nombre_negocio'] ?? '') }}"
                        maxlength="150" placeholder="Ej: Variedades Disney" autocomplete="off">
                    @error('nombre_negocio') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        NIT / Identificación tributaria
                        <span class="hint">(opcional)</span>
                    </label>
                    <input type="text" name="nit_negocio"
                        class="form-input {{ $errors->has('nit_negocio') ? 'is-invalid' : '' }}"
                        value="{{ old('nit_negocio', $config['nit_negocio'] ?? '') }}"
                        maxlength="50" placeholder="Ej: 900.123.456-7" autocomplete="off">
                    @error('nit_negocio') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Dirección
                        <span class="hint">(opcional)</span>
                    </label>
                    <input type="text" name="direccion_negocio"
                        class="form-input {{ $errors->has('direccion_negocio') ? 'is-invalid' : '' }}"
                        value="{{ old('direccion_negocio', $config['direccion_negocio'] ?? '') }}"
                        maxlength="200" placeholder="Ej: Calle 10 # 5-32" autocomplete="off">
                    @error('direccion_negocio') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Teléfono
                        <span class="hint">(opcional)</span>
                    </label>
                    <input type="text" name="telefono_negocio"
                        class="form-input {{ $errors->has('telefono_negocio') ? 'is-invalid' : '' }}"
                        value="{{ old('telefono_negocio', $config['telefono_negocio'] ?? '') }}"
                        maxlength="50" placeholder="Ej: 310 555 1234" autocomplete="off">
                    @error('telefono_negocio') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>

                {{-- ── Facturación ── --}}
                <div class="form-section-title" style="margin-top:10px;"><i class="fas fa-file-invoice" style="margin-right:6px;"></i>Comprobantes y ventas</div>

                <div class="form-group">
                    <label class="form-label">
                        Prefijo de comprobante <span style="color:#EF4444">*</span>
                    </label>
                    <input type="text" name="prefijo_factura"
                        class="form-input {{ $errors->has('prefijo_factura') ? 'is-invalid' : '' }}"
                        value="{{ old('prefijo_factura', $config['prefijo_factura'] ?? 'DS-') }}"
                        maxlength="10" placeholder="Ej: DS-" autocomplete="off" style="max-width:160px;">
                    <span class="form-hint">El número se completa automáticamente: <strong>DS-000001</strong></span>
                    @error('prefijo_factura') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Porcentaje de IVA <span style="color:#EF4444">*</span>
                        <span class="hint">(%)</span>
                    </label>
                    <input type="number" name="porcentaje_iva"
                        class="form-input {{ $errors->has('porcentaje_iva') ? 'is-invalid' : '' }}"
                        value="{{ old('porcentaje_iva', $config['porcentaje_iva'] ?? '0') }}"
                        min="0" max="100" step="0.01" style="max-width:160px;">
                    <span class="form-hint">Use 0 para no aplicar IVA. Se calcula sobre (subtotal − descuento).</span>
                    @error('porcentaje_iva') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Descuento máximo permitido <span style="color:#EF4444">*</span>
                        <span class="hint">(%)</span>
                    </label>
                    <input type="number" name="descuento_maximo_porcentaje"
                        class="form-input {{ $errors->has('descuento_maximo_porcentaje') ? 'is-invalid' : '' }}"
                        value="{{ old('descuento_maximo_porcentaje', $config['descuento_maximo_porcentaje'] ?? '100') }}"
                        min="0" max="100" step="1" style="max-width:160px;">
                    <span class="form-hint">Porcentaje del subtotal. 100 = sin límite de descuento.</span>
                    @error('descuento_maximo_porcentaje') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>

                {{-- ── Inventario ── --}}
                <div class="form-section-title" style="margin-top:10px;"><i class="fas fa-boxes-stacked" style="margin-right:6px;"></i>Inventario</div>

                <div class="form-group">
                    <label class="form-label">
                        Stock mínimo por defecto <span style="color:#EF4444">*</span>
                    </label>
                    <input type="number" name="stock_minimo_defecto"
                        class="form-input {{ $errors->has('stock_minimo_defecto') ? 'is-invalid' : '' }}"
                        value="{{ old('stock_minimo_defecto', $config['stock_minimo_defecto'] ?? '1') }}"
                        min="1" step="1" style="max-width:160px;">
                    <span class="form-hint">Valor inicial del campo "Stock mínimo" al crear un producto nuevo.</span>
                    @error('stock_minimo_defecto') <div class="invalid-msg"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div> @enderror
                </div>

            </div>

            <div style="display:flex; justify-content:flex-end; margin-top:28px;">
                <button type="submit" class="btn-guardar">
                    <i class="fas fa-check"></i> Guardar configuración
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── SECCIÓN COPIAS DE SEGURIDAD (SOLO ADMINISTRADOR) ── --}}
@if(Auth::user()->esAdmin())
<div class="card" style="margin-top: 28px;">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <span class="card-title"><i class="fas fa-database" style="color:#7C3AED; margin-right:8px;"></i>Copias de seguridad</span>
            <div style="font-size:.8rem; color:#6B7280; margin-top:3px;">
                Genera respaldos completos de la base de datos (estructura y datos en formato SQL).
            </div>
        </div>
        <form method="POST" action="{{ route('configuracion.backups.generar') }}" style="margin:0;">
            @csrf
            <button type="submit" class="btn-guardar" style="display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-cloud-arrow-down"></i> Generar copia de seguridad
            </button>
        </form>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Archivo</th>
                        <th>Fecha de creación</th>
                        <th>Tamaño</th>
                        <th style="text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $b)
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:8px; font-weight:600; color:#1E1B4B;">
                                <i class="fas fa-file-lines" style="color:#7C3AED;"></i>
                                <span>{{ $b['nombre'] }}</span>
                            </div>
                        </td>
                        <td style="color:#6B7280; font-size:.84rem;">{{ $b['fecha'] }}</td>
                        <td>
                            <span style="display:inline-flex; padding:3px 9px; border-radius:12px; background:#F5F3FF; color:#5B21B6; font-size:.76rem; font-weight:700;">
                                {{ $b['tamano'] }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:8px; justify-content:flex-end;">
                                <a href="{{ route('configuracion.backups.descargar', $b['nombre']) }}"
                                   class="btn-accion btn-descargar" title="Descargar archivo .sql">
                                    <i class="fas fa-download"></i> Descargar
                                </a>
                                <form method="POST" action="{{ route('configuracion.backups.eliminar', $b['nombre']) }}" style="display:inline;"
                                      onsubmit="confirmarEliminarBackup(event, '{{ $b['nombre'] }}'); return false;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-accion btn-eliminar" title="Eliminar copia">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:36px; color:#9CA3AF;">
                            <i class="fas fa-database" style="font-size:2rem; color:#C4B5FD; display:block; margin-bottom:8px;"></i>
                            Aún no se han generado copias de seguridad.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal confirmar eliminar copia de seguridad --}}
<div class="modal-overlay" id="modalConfirmBackup">
    <div class="modal-backdrop" onclick="cerrarModal('modalConfirmBackup')"></div>
    <div class="modal-box">
        <button class="modal-close" onclick="cerrarModal('modalConfirmBackup')"><i class="fas fa-xmark"></i></button>
        <div class="modal-title" style="color:#DC2626; margin-bottom:12px;">
            <i class="fas fa-triangle-exclamation"></i> ¿Eliminar copia de seguridad?
        </div>
        <p id="modalConfirmBackupMsg" style="font-size:.88rem; color:#374151; margin-bottom:22px; line-height:1.5;"></p>
        <div class="modal-footer" style="margin-top:0;">
            <button type="button" class="btn-cancelar" onclick="cerrarModal('modalConfirmBackup')">Cancelar</button>
            <button id="modalConfirmBackupOk" type="button" class="btn-guardar" style="background:#DC2626; border-color:#DC2626;">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </div>
    </div>
</div>

<script>
    function cerrarModal(id) { document.getElementById(id).classList.remove('open'); }

    function confirmarEliminarBackup(e, nombreArchivo) {
        e.preventDefault();
        const form = e.target.closest('form');
        document.getElementById('modalConfirmBackupMsg').textContent =
            `¿Seguro que deseas eliminar permanentemente la copia "${nombreArchivo}"? Esta acción no se puede deshacer.`;
        document.getElementById('modalConfirmBackupOk').onclick = function() {
            cerrarModal('modalConfirmBackup');
            form.submit();
        };
        document.getElementById('modalConfirmBackup').classList.add('open');
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            const m = document.getElementById('modalConfirmBackup');
            if (m) m.classList.remove('open');
        }
    });
</script>
@endif
@endsection

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
@endsection

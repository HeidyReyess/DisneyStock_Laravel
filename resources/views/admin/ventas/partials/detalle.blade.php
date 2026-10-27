{{-- Partial: detalle de venta para modal AJAX --}}
@php
    $nombreNegocio  = \App\Models\Configuracion::valor('nombre_negocio', 'Variedades Disney');
    $nitNegocio     = \App\Models\Configuracion::valor('nit_negocio', '');
    $direccionNeg   = \App\Models\Configuracion::valor('direccion_negocio', '');
    $telefonoNeg    = \App\Models\Configuracion::valor('telefono_negocio', '');
    $pctIva         = (float) \App\Models\Configuracion::valor('porcentaje_iva', 0);
@endphp
<div style="font-size:.88rem; color:#374151;">

    {{-- Encabezado del negocio --}}
    <div style="text-align:center; padding-bottom:14px; margin-bottom:16px; border-bottom:1px solid #F5F3FF;">
        <div style="font-family:'Outfit',sans-serif; font-size:1.05rem; font-weight:800; color:#1E1B4B;">
            {{ $nombreNegocio }}
        </div>
        @if($nitNegocio)
            <div style="font-size:.78rem; color:#6B7280; margin-top:2px;">NIT: {{ $nitNegocio }}</div>
        @endif
        @if($direccionNeg)
            <div style="font-size:.78rem; color:#6B7280;">{{ $direccionNeg }}</div>
        @endif
        @if($telefonoNeg)
            <div style="font-size:.78rem; color:#6B7280;">Tel: {{ $telefonoNeg }}</div>
        @endif
    </div>

    {{-- Cabecera --}}
    <div style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:18px; padding-bottom:16px; border-bottom:1px solid #F5F3FF;">
        <div>
            <div style="font-family:'Outfit',sans-serif; font-size:1.1rem; font-weight:800; color:#1E1B4B;">
                {{ $venta->factura?->numero ?? 'Sin comprobante' }}
            </div>
            <div style="font-size:.8rem; color:#9CA3AF; margin-top:2px;">
                {{ $venta->fecha_venta->format('d/m/Y') }} · {{ $venta->user?->nombre ?? 'Sin vendedor' }}
            </div>
        </div>
        @php
            $badgeClass = match($venta->estado) {
                'completada' => 'background:#D1FAE5;color:#065F46;',
                'anulada'    => 'background:#FEE2E2;color:#991B1B;',
                default      => 'background:#FEF3C7;color:#92400E;',
            };
        @endphp
        <span style="display:inline-flex;align-items:center;padding:4px 12px;border-radius:20px;font-size:.78rem;font-weight:700;{{ $badgeClass }}">
            {{ ucfirst($venta->estado) }}
        </span>
    </div>

    {{-- Tabla de ítems --}}
    <table style="width:100%; border-collapse:collapse; margin-bottom:16px;">
        <thead>
            <tr style="border-bottom:1px solid #F5F3FF;">
                <th style="text-align:left; padding:6px 8px; font-size:.72rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#9CA3AF;">Producto</th>
                <th style="text-align:center; padding:6px 8px; font-size:.72rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#9CA3AF;">Cant.</th>
                <th style="text-align:right; padding:6px 8px; font-size:.72rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#9CA3AF;">Precio</th>
                <th style="text-align:right; padding:6px 8px; font-size:.72rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#9CA3AF;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $det)
            <tr style="border-bottom:1px solid #FAFAFA;">
                <td style="padding:8px 8px; color:#1E1B4B; font-weight:500;">{{ $det->producto?->nombre ?? '—' }}</td>
                <td style="padding:8px 8px; text-align:center; color:#6B7280;">{{ $det->cantidad }}</td>
                <td style="padding:8px 8px; text-align:right; color:#6B7280;">$ {{ number_format($det->precio_unitario, 0, ',', '.') }}</td>
                <td style="padding:8px 8px; text-align:right; font-weight:600; color:#1E1B4B;">$ {{ number_format($det->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totales --}}
    <div style="background:#F9F5FF; border-radius:10px; padding:14px 16px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:6px; color:#6B7280;">
            <span>Subtotal</span>
            <span>$ {{ number_format($venta->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($venta->descuento > 0)
        <div style="display:flex; justify-content:space-between; margin-bottom:6px; color:#DC2626;">
            <span>Descuento</span>
            <span>- $ {{ number_format($venta->descuento, 0, ',', '.') }}</span>
        </div>
        @endif
        @if($venta->impuesto > 0)
        <div style="display:flex; justify-content:space-between; margin-bottom:6px; color:#6B7280;">
            <span>IVA ({{ $pctIva }}%)</span>
            <span>$ {{ number_format($venta->impuesto, 0, ',', '.') }}</span>
        </div>
        @endif
        <div style="display:flex; justify-content:space-between; font-family:'Outfit',sans-serif; font-size:1.05rem; font-weight:800; color:#1E1B4B; border-top:1px solid #EDE9FE; padding-top:10px; margin-top:4px;">
            <span>Total</span>
            <span>$ {{ number_format($venta->total, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Botón reimprimir comprobante POS --}}
    <div style="margin-top:16px; text-align:center;">
        <a href="{{ route('ventas.comprobante', $venta) }}" target="_blank"
           class="btn-guardar" style="display:inline-flex; align-items:center; justify-content:center; gap:8px; text-decoration:none; width:100%; box-sizing:border-box; padding:10px 20px; font-size:.88rem;">
            <i class="fas fa-print"></i> Imprimir comprobante POS
        </a>
    </div>
</div>

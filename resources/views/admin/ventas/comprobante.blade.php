<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante {{ $venta->factura?->numero ?? ('DS-' . str_pad($venta->id, 6, '0', STR_PAD_LEFT)) }} — DisneyStock</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Courier New', Courier, monospace, sans-serif;
            font-size: 12px;
            line-height: 1.35;
            color: #000;
            background: #F3F4F6;
        }
        .no-print {
            text-align: center;
            padding: 14px 10px 0;
        }
        .btn-print {
            padding: 7px 18px;
            font-size: 12px;
            background: #4A1D96;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-family: inherit;
        }
        .btn-print:hover {
            background: #7C3AED;
        }
        /* Contenedor del ticket con ancho fijo de 72 mm centrado tanto en pantalla como en impresión */
        .ticket {
            width: 72mm;
            min-width: 72mm;
            max-width: 72mm;
            margin: 14px auto;
            padding: 12px 6px;
            background: #fff;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
            box-sizing: border-box;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .separator {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .negocio-nombre {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .negocio-info {
            font-size: 11px;
            line-height: 1.3;
        }
        .titulo-doc {
            font-size: 13px;
            font-weight: bold;
            margin-top: 2px;
        }
        .numero-doc {
            font-size: 13px;
            font-weight: bold;
        }
        .info-venta {
            font-size: 11px;
            margin-top: 2px;
            line-height: 1.35;
        }
        .tabla-items {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin: 4px 0;
        }
        .tabla-items th {
            border-bottom: 1px dashed #000;
            padding: 4px 0;
            font-size: 11px;
        }
        .tabla-items td {
            padding: 3px 0;
            vertical-align: top;
        }
        .tabla-totales {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 4px;
        }
        .tabla-totales td {
            padding: 2px 0;
        }
        .total-row td {
            font-size: 13px;
            font-weight: bold;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }
        .anulada-banner {
            font-weight: bold;
            font-size: 12px;
            text-align: center;
            margin: 8px 0;
            padding: 4px;
            border: 1px solid #000;
        }
        .footer-texto {
            text-align: center;
            margin-top: 12px;
        }
        .legal {
            font-size: 9px;
            margin-top: 4px;
            color: #333;
            line-height: 1.25;
        }

        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }
            html, body {
                width: 80mm;
                margin: 0;
                padding: 0;
                background: #fff;
            }
            .no-print {
                display: none !important;
            }
            .ticket {
                width: 72mm;
                min-width: 72mm;
                max-width: 72mm;
                margin: 0 auto;
                padding: 4mm 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button type="button" class="btn-print" onclick="window.print()">
            Imprimir comprobante
        </button>
    </div>

    {{-- Contenedor del ticket: ancho fijo 72mm centrado en pantalla e impresión --}}
    <div class="ticket">

        {{-- 1. Nombre del negocio, NIT, dirección y teléfono (desde Configuración) --}}
        <div class="text-center">
            <div class="negocio-nombre">{{ $config['nombre'] }}</div>
            @if(!empty(trim($config['nit'] ?? '')))
                <div class="negocio-info">NIT: {{ trim($config['nit']) }}</div>
            @endif
            @if(!empty(trim($config['direccion'] ?? '')))
                <div class="negocio-info">{{ trim($config['direccion']) }}</div>
            @endif
            @if(!empty(trim($config['telefono'] ?? '')))
                <div class="negocio-info">Tel: {{ trim($config['telefono']) }}</div>
            @endif
        </div>

        {{-- 2. Línea separadora --}}
        <div class="separator"></div>

        {{-- 3. "COMPROBANTE DE VENTA POS" y el número --}}
        <div class="text-center">
            <div class="titulo-doc">COMPROBANTE DE VENTA POS</div>
            <div class="numero-doc">{{ $venta->factura?->numero ?? ('DS-' . str_pad($venta->id, 6, '0', STR_PAD_LEFT)) }}</div>
        </div>

        {{-- 4. Fecha y hora, y vendedor --}}
        <div class="info-venta text-center">
            <div>Fecha: {{ $venta->created_at ? $venta->created_at->format('d/m/Y H:i') : $venta->fecha_venta->format('d/m/Y') }}</div>
            <div>Vendedor: {{ $venta->user?->nombre ?? 'Sistema' }}</div>
        </div>

        {{-- 5. Línea separadora --}}
        <div class="separator"></div>

        {{-- 6. Productos: cantidad x nombre, precio unitario y subtotal --}}
        <table class="tabla-items">
            <thead>
                <tr>
                    <th class="text-left" style="width: 55%;">Cant x Producto</th>
                    <th class="text-right" style="width: 20%;">Precio</th>
                    <th class="text-right" style="width: 25%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $det)
                <tr>
                    <td class="text-left">
                        {{ $det->cantidad }} x {{ $det->producto?->nombre ?? 'Producto' }}
                    </td>
                    <td class="text-right" style="white-space: nowrap;">
                        ${{ number_format($det->precio_unitario, 0, ',', '.') }}
                    </td>
                    <td class="text-right" style="white-space: nowrap;">
                        ${{ number_format($det->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- 7. Línea separadora --}}
        <div class="separator"></div>

        {{-- 8. Subtotal, descuento, IVA (solo si es mayor que 0) y TOTAL en negrita --}}
        <table class="tabla-totales">
            <tr>
                <td class="text-left">Subtotal:</td>
                <td class="text-right">${{ number_format($venta->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($venta->descuento > 0)
            <tr>
                <td class="text-left">Descuento:</td>
                <td class="text-right">- ${{ number_format($venta->descuento, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($venta->impuesto > 0)
            <tr>
                <td class="text-left">IVA ({{ $config['pct_iva'] }}%):</td>
                <td class="text-right">${{ number_format($venta->impuesto, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td class="text-left bold">TOTAL:</td>
                <td class="text-right bold">${{ number_format($venta->total, 0, ',', '.') }}</td>
            </tr>
        </table>

        {{-- 9. Si la venta está anulada: "*** VENTA ANULADA ***" --}}
        @if($venta->estaAnulada())
        <div class="anulada-banner">
            *** VENTA ANULADA ***
        </div>
        @endif

        {{-- 10. "¡Gracias por su compra!" y en letra pequeña: "Documento interno, no válido como factura electrónica DIAN" --}}
        <div class="footer-texto">
            <div class="bold">¡Gracias por su compra!</div>
            <div class="legal">Documento interno, no válido como factura electrónica DIAN</div>
        </div>

    </div>

    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>
</html>

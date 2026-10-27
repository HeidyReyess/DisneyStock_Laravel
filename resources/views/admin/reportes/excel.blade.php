<table>
    <thead>
        <tr>
            <th colspan="7" style="font-size: 14pt; font-weight: bold;">{{ $nombreNegocio }}</th>
        </tr>
        <tr>
            <th colspan="7" style="font-size: 12pt; font-weight: bold;">{{ $tituloReporte }}</th>
        </tr>
        <tr>
            <th colspan="7">Período: {{ $textoPeriodo }} | Generado el: {{ $fechaGeneracion }}</th>
        </tr>
        <tr>
            <th colspan="7"></th>
        </tr>
    </thead>
    <tbody>
    @if($datos->isEmpty())
        <tr>
            <td colspan="7" style="text-align: center; font-weight: bold; color: #6B7280;">
                No hay información para el período seleccionado
            </td>
        </tr>
    @else

        {{-- ══════════════════════════════ VENTAS ══════════════════════════════ --}}
        @if($tipo === 'ventas')
            <tr>
                <th style="font-weight: bold; background-color: #EDE9FE;">Comprobante</th>
                <th style="font-weight: bold; background-color: #EDE9FE;">Vendedor</th>
                <th style="font-weight: bold; background-color: #EDE9FE; text-align: right;">Subtotal</th>
                <th style="font-weight: bold; background-color: #EDE9FE; text-align: right;">Descuento</th>
                <th style="font-weight: bold; background-color: #EDE9FE; text-align: right;">Total</th>
                <th style="font-weight: bold; background-color: #EDE9FE; text-align: center;">Estado</th>
                <th style="font-weight: bold; background-color: #EDE9FE; text-align: center;">Fecha</th>
            </tr>
            @foreach($datos as $v)
            <tr>
                <td>{{ $v->factura?->numero ?? ('DS-' . str_pad($v->id, 6, '0', STR_PAD_LEFT)) }}</td>
                <td>{{ $v->user?->nombre ?? '—' }}</td>
                <td style="text-align: right;">{{ (float) $v->subtotal }}</td>
                <td style="text-align: right;">{{ (float) ($v->descuento ?? 0) }}</td>
                <td style="text-align: right; font-weight: bold;">{{ (float) $v->total }}</td>
                <td style="text-align: center;">{{ ucfirst($v->estado) }}</td>
                <td style="text-align: center;">{{ $v->fecha_venta->format('d/m/Y') }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2" style="font-weight: bold; background-color: #F5F3FF; text-align: right;">TOTALES (sin anuladas):</td>
                <td style="font-weight: bold; background-color: #F5F3FF; text-align: right;">
                    {{ (float) $datos->where('estado', '!=', 'anulada')->sum('subtotal') }}
                </td>
                <td style="font-weight: bold; background-color: #F5F3FF; text-align: right;">
                    {{ (float) $datos->where('estado', '!=', 'anulada')->sum('descuento') }}
                </td>
                <td style="font-weight: bold; background-color: #F5F3FF; text-align: right;">
                    {{ (float) $datos->where('estado', '!=', 'anulada')->sum('total') }}
                </td>
                <td colspan="2" style="background-color: #F5F3FF;"></td>
            </tr>

            @if($ventasPorCategoria && $ventasPorCategoria->count() > 0)
            <tr><td colspan="7"></td></tr>
            <tr>
                <th colspan="3" style="font-weight: bold; background-color: #EDE9FE;">Ventas por categoría</th>
                <th colspan="4"></th>
            </tr>
            <tr>
                <th style="font-weight: bold; background-color: #F5F3FF;">Categoría</th>
                <th style="font-weight: bold; background-color: #F5F3FF; text-align: center;">Unidades vendidas</th>
                <th style="font-weight: bold; background-color: #F5F3FF; text-align: right;">Total</th>
                <th colspan="4"></th>
            </tr>
            @foreach($ventasPorCategoria as $vc)
            <tr>
                <td>{{ $vc->nombre_categoria }}</td>
                <td style="text-align: center;">{{ (int) $vc->unidades }}</td>
                <td style="text-align: right;">{{ (float) $vc->total }}</td>
                <td colspan="4"></td>
            </tr>
            @endforeach
            <tr>
                <td style="font-weight: bold; background-color: #EDE9FE; text-align: right;">Total</td>
                <td style="font-weight: bold; background-color: #EDE9FE; text-align: center;">
                    {{ (int) $ventasPorCategoria->sum('unidades') }}
                </td>
                <td style="font-weight: bold; background-color: #EDE9FE; text-align: right;">
                    {{ (float) $ventasPorCategoria->sum('total') }}
                </td>
                <td colspan="4"></td>
            </tr>
            @endif

        {{-- ══════════════════════════════ INVENTARIO ══════════════════════════════ --}}
        @elseif($tipo === 'inventario')
            <tr>
                <th style="font-weight: bold; background-color: #EDE9FE;">Producto</th>
                <th style="font-weight: bold; background-color: #EDE9FE;">Categoría</th>
                <th style="font-weight: bold; background-color: #EDE9FE; text-align: center;">Stock</th>
                <th style="font-weight: bold; background-color: #EDE9FE; text-align: center;">Mínimo</th>
                <th style="font-weight: bold; background-color: #EDE9FE; text-align: center;">Estado stock</th>
                <th style="font-weight: bold; background-color: #EDE9FE; text-align: right;">Valor costo</th>
                <th style="font-weight: bold; background-color: #EDE9FE; text-align: right;">Valor venta</th>
            </tr>
            @foreach($datos as $prod)
            @php
                $stock  = $prod->inventario?->cantidad_stock ?? 0;
                $minimo = $prod->inventario?->stock_minimo   ?? 0;
                $bajo   = $minimo > 0 && $stock <= $minimo;
            @endphp
            <tr>
                <td>{{ $prod->nombre }}</td>
                <td>{{ $prod->categoria?->nombre_categoria ?? '—' }}</td>
                <td style="text-align: center;">{{ (int) $stock }}</td>
                <td style="text-align: center;">{{ (int) $minimo }}</td>
                <td style="text-align: center;">{{ $bajo ? 'Bajo' : 'Normal' }}</td>
                <td style="text-align: right;">{{ (float) ($stock * $prod->precio_compra) }}</td>
                <td style="text-align: right;">{{ (float) ($stock * $prod->precio_venta) }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="5" style="font-weight: bold; background-color: #EDE9FE; text-align: right;">TOTAL:</td>
                <td style="font-weight: bold; background-color: #EDE9FE; text-align: right;">
                    {{ (float) $datos->sum(fn($p) => ($p->inventario?->cantidad_stock ?? 0) * $p->precio_compra) }}
                </td>
                <td style="font-weight: bold; background-color: #EDE9FE; text-align: right;">
                    {{ (float) $datos->sum(fn($p) => ($p->inventario?->cantidad_stock ?? 0) * $p->precio_venta) }}
                </td>
            </tr>

        {{-- ══════════════════════════════ STOCK BAJO ══════════════════════════════ --}}
        @elseif($tipo === 'stock_bajo')
            <tr>
                <th style="font-weight: bold; background-color: #EDE9FE;">Producto</th>
                <th style="font-weight: bold; background-color: #EDE9FE;">Categoría</th>
                <th style="font-weight: bold; background-color: #EDE9FE; text-align: center;">Stock actual</th>
                <th style="font-weight: bold; background-color: #EDE9FE; text-align: center;">Stock mínimo</th>
                <th style="font-weight: bold; background-color: #EDE9FE; text-align: center;">Faltante</th>
                <th style="font-weight: bold; background-color: #EDE9FE;">Proveedor</th>
            </tr>
            @foreach($datos as $prod)
            @php
                $stock    = $prod->inventario?->cantidad_stock ?? 0;
                $minimo   = $prod->inventario?->stock_minimo   ?? 0;
                $faltante = max(0, $minimo - $stock);
            @endphp
            <tr>
                <td>{{ $prod->nombre }}</td>
                <td>{{ $prod->categoria?->nombre_categoria ?? '—' }}</td>
                <td style="text-align: center; color: #DC2626; font-weight: bold;">{{ (int) $stock }}</td>
                <td style="text-align: center;">{{ (int) $minimo }}</td>
                <td style="text-align: center;">{{ (int) $faltante }}</td>
                <td>{{ $prod->proveedor ?? '—' }}</td>
            </tr>
            @endforeach

        {{-- ══════════════════════════════ MOVIMIENTOS ══════════════════════════════ --}}
        @elseif($tipo === 'movimientos')
            <tr>
                <th style="font-weight: bold; background-color: #EDE9FE;">Producto</th>
                <th style="font-weight: bold; background-color: #EDE9FE; text-align: center;">Tipo</th>
                <th style="font-weight: bold; background-color: #EDE9FE; text-align: center;">Cantidad</th>
                <th style="font-weight: bold; background-color: #EDE9FE;">Descripción</th>
                <th style="font-weight: bold; background-color: #EDE9FE;">Proveedor</th>
                <th style="font-weight: bold; background-color: #EDE9FE;">Usuario</th>
                <th style="font-weight: bold; background-color: #EDE9FE; text-align: center;">Fecha</th>
            </tr>
            @foreach($datos as $mov)
            <tr>
                <td>{{ $mov->producto?->nombre ?? '—' }}</td>
                <td style="text-align: center;">{{ ucfirst($mov->tipo_movimiento) }}</td>
                <td style="text-align: center;">{{ $mov->tipo_movimiento === 'salida' ? -$mov->cantidad : (int)$mov->cantidad }}</td>
                <td>{{ $mov->descripcion ?? '—' }}</td>
                <td>{{ $mov->proveedor ?? '—' }}</td>
                <td>{{ $mov->user?->nombre ?? 'Sistema' }}</td>
                <td style="text-align: center;">{{ $mov->fecha?->format('d/m/Y') }}</td>
            </tr>
            @endforeach

        @endif

    @endif
    </tbody>
</table>

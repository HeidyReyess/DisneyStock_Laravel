<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\DetalleVenta;
use App\Models\Factura;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    // GET /ventas
    public function index(Request $request)
    {
        $query = Venta::with(['factura', 'user'])
            ->orderByDesc('fecha_venta')
            ->orderByDesc('id');

        if ($request->filled('desde')) {
            $query->whereDate('fecha_venta', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha_venta', '<=', $request->hasta);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $ventas    = $query->get();
        $productos = Producto::with('inventario')
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        // Métricas rápidas
        $ventasHoy   = Venta::whereDate('fecha_venta', today())
            ->where('estado', '!=', 'anulada')->count();
        $ingresosHoy = Venta::whereDate('fecha_venta', today())
            ->where('estado', '!=', 'anulada')->sum('total');

        return view('admin.ventas.index', compact(
            'ventas', 'productos', 'ventasHoy', 'ingresosHoy'
        ));
    }

    // POST /ventas
    public function store(Request $request)
    {
        $request->validate([
            'items'     => ['required', 'json'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
        ], [
            'items.required' => 'Agrega al menos un producto a la venta.',
        ]);

        $items     = json_decode($request->items, true);
        $descuento = (float) ($request->descuento ?? 0);

        if (empty($items)) {
            return back()->with('error', 'Agrega al menos un producto a la venta.');
        }

        try {
            DB::transaction(function () use ($items, $descuento) {
                $subtotal = 0;
                foreach ($items as $it) {
                    $subtotal += (float)$it['precio'] * (int)$it['cantidad'];
                }
                $total = max(0, $subtotal - $descuento);

                // Crear cabecera de venta
                $venta = Venta::create([
                    'fecha_venta' => today(),
                    'subtotal'    => $subtotal,
                    'impuesto'    => 0,
                    'descuento'   => $descuento,
                    'total'       => $total,
                    'estado'      => 'completada',
                    'user_id'     => Auth::id(),
                ]);

                $numFac = 'DS-' . str_pad($venta->id, 6, '0', STR_PAD_LEFT);

                // Procesar cada ítem
                foreach ($items as $it) {
                    $pid    = (int) $it['id'];
                    $cant   = (int) $it['cantidad'];
                    $precio = (float) $it['precio'];

                    $producto = Producto::with('inventario')->lockForUpdate()->findOrFail($pid);
                    $inv      = $producto->inventario;

                    if (!$inv || $inv->cantidad_stock < $cant) {
                        throw new \Exception("Stock insuficiente para: {$producto->nombre}");
                    }

                    // Línea de detalle
                    DetalleVenta::create([
                        'venta_id'        => $venta->id,
                        'producto_id'     => $pid,
                        'cantidad'        => $cant,
                        'precio_unitario' => $precio,
                        'subtotal'        => $precio * $cant,
                    ]);

                    // Descontar stock
                    $inv->decrement('cantidad_stock', $cant);
                    $inv->update(['fecha_actualizacion' => today()]);

                    // Movimiento de salida
                    MovimientoInventario::create([
                        'tipo_movimiento' => 'salida',
                        'cantidad'        => $cant,
                        'fecha'           => today(),
                        'descripcion'     => "Venta {$numFac}",
                        'producto_id'     => $pid,
                        'user_id'         => Auth::id(),
                        'venta_id'        => $venta->id,
                    ]);

                    // Generar alerta si el stock quedó bajo el mínimo
                    $inv->refresh();
                    if ($inv->stock_minimo > 0 && $inv->cantidad_stock <= $inv->stock_minimo) {
                        $yaExiste = Alerta::where('producto_id', $pid)
                            ->where('estado', 'activa')->exists();
                        if (!$yaExiste) {
                            Alerta::create([
                                'tipo_alerta'  => 'stock_bajo',
                                'mensaje'      => "Stock bajo tras venta {$numFac}: {$producto->nombre} tiene {$inv->cantidad_stock} unidades (mínimo: {$inv->stock_minimo})",
                                'fecha_alerta' => today(),
                                'estado'       => 'activa',
                                'producto_id'  => $pid,
                            ]);
                        }
                    }
                }

                // Crear factura
                Factura::create([
                    'numero'        => $numFac,
                    'fecha_emision' => today(),
                    'total'         => $total,
                    'formato'       => 'PDF',
                    'venta_id'      => $venta->id,
                ]);
            });

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('ventas.index')
            ->with('success', 'Venta registrada correctamente.');
    }

    // GET /ventas/{venta} — detalle en modal (AJAX)
    public function show(Venta $venta)
    {
        $venta->load(['factura', 'user', 'detalles.producto']);
        return view('admin.ventas.partials.detalle', compact('venta'));
    }

    // PATCH /ventas/{venta}/anular
    public function anular(Venta $venta)
    {
        if ($venta->estaAnulada()) {
            return redirect()->route('ventas.index')
                ->with('error', 'Esta venta ya está anulada.');
        }

        try {
            DB::transaction(function () use ($venta) {
                $venta->load('detalles');

                foreach ($venta->detalles as $detalle) {
                    $producto = Producto::with('inventario')->find($detalle->producto_id);
                    if ($producto?->inventario) {
                        $producto->inventario->increment('cantidad_stock', $detalle->cantidad);
                        $producto->inventario->update(['fecha_actualizacion' => today()]);

                        // Movimiento de entrada por anulación
                        MovimientoInventario::create([
                            'tipo_movimiento' => 'entrada',
                            'cantidad'        => $detalle->cantidad,
                            'fecha'           => today(),
                            'descripcion'     => "Anulación de venta #{$venta->id}",
                            'producto_id'     => $detalle->producto_id,
                            'user_id'         => Auth::id(),
                            'venta_id'        => $venta->id,
                        ]);

                        // Resolver alertas si el stock volvió a la normalidad
                        $inv = $producto->inventario->fresh();
                        if ($inv->stock_minimo > 0 && $inv->cantidad_stock > $inv->stock_minimo) {
                            Alerta::where('producto_id', $detalle->producto_id)
                                ->where('estado', 'activa')
                                ->update([
                                    'estado'           => 'resuelta',
                                    'fecha_resolucion' => today(),
                                ]);
                        }
                    }
                }

                $venta->update(['estado' => 'anulada']);
            });

        } catch (\Exception $e) {
            return redirect()->route('ventas.index')
                ->with('error', 'Error al anular la venta: ' . $e->getMessage());
        }

        return redirect()->route('ventas.index')
            ->with('success', "Venta #{$venta->id} anulada. El stock fue restaurado.");
    }
}

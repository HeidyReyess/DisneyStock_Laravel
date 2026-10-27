<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Configuracion;
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
        // Filtro por empleado (solo admin puede usarlo)
        if ($request->filled('empleado_id') && Auth::user()->esAdmin()) {
            $query->where('user_id', $request->empleado_id);
        }

        $ventas    = $query->get();
        $productos = Producto::with('inventario')
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        // Lista de empleados para el filtro (solo se usa en la vista si es admin)
        $empleados = Auth::user()->esAdmin()
            ? \App\Models\User::orderBy('nombre')->get()
            : collect();

        // Descuento máximo para validación JS en el modal de ventas
        $descuentoMaxPct = (float) \App\Models\Configuracion::valor('descuento_maximo_porcentaje', 100);

        // Métricas rápidas
        $ventasHoy   = Venta::whereDate('fecha_venta', today())
            ->where('estado', '!=', 'anulada')->count();
        $ingresosHoy = Venta::whereDate('fecha_venta', today())
            ->where('estado', '!=', 'anulada')->sum('total');

        return view('admin.ventas.index', compact(
            'ventas', 'productos', 'ventasHoy', 'ingresosHoy', 'empleados', 'descuentoMaxPct'
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

        // Calcular subtotal anticipado usando precios de la BD para validar el descuento
        $subtotalPreview = 0;
        foreach ($items as $it) {
            $prod = Producto::find((int)$it['id']);
            if ($prod) {
                $subtotalPreview += (float)$prod->precio_venta * (int)$it['cantidad'];
            }
        }

        // Validar descuento máximo desde configuración
        $descMaxPct = (float) Configuracion::valor('descuento_maximo_porcentaje', 100);
        $descMaxValor = $subtotalPreview * ($descMaxPct / 100);

        if ($descuento > $subtotalPreview) {
            return back()->withInput()->with('error', 'El descuento no puede ser mayor que el subtotal de la venta.');
        }
        if ($descMaxPct < 100 && $descuento > $descMaxValor) {
            return back()->withInput()->with('error', "El descuento no puede superar el {$descMaxPct}% del subtotal (máximo $ " . number_format($descMaxValor, 0, ',', '.') . ").");
        }

        // Capturar ID antes del closure — getAuthIdentifier() devuelve 'usuario' no el id numérico
        $userId = Auth::user()->id;

        try {
            DB::transaction(function () use ($items, $descuento, $userId) {
                $subtotal = 0;
                $preciosBD = [];

                // Obtener precios desde la BD antes de calcular — no confiar en el precio del formulario
                foreach ($items as $it) {
                    $pid    = (int) $it['id'];
                    $prod   = Producto::findOrFail($pid);
                    $precio = (float) $prod->precio_venta;  // precio autorizado
                    $preciosBD[$pid] = $precio;
                    $subtotal += $precio * (int)$it['cantidad'];
                }

                // IVA desde configuración (sobre subtotal - descuento)
                $pctIva   = (float) Configuracion::valor('porcentaje_iva', 0);
                $base     = max(0, $subtotal - $descuento);
                $impuesto = $pctIva > 0 ? round($base * ($pctIva / 100), 2) : 0;
                $total    = $base + $impuesto;

                // Prefijo de factura desde configuración
                $prefijo = Configuracion::valor('prefijo_factura', 'DS-');

                // Crear cabecera de venta
                $venta = Venta::create([
                    'fecha_venta' => today(),
                    'subtotal'    => $subtotal,
                    'impuesto'    => $impuesto,
                    'descuento'   => $descuento,
                    'total'       => $total,
                    'estado'      => 'completada',
                    'user_id'     => $userId,
                ]);

                $numFac = $prefijo . str_pad($venta->id, 6, '0', STR_PAD_LEFT);

                // Procesar cada ítem
                foreach ($items as $it) {
                    $pid    = (int) $it['id'];
                    $cant   = (int) $it['cantidad'];
                    $precio = $preciosBD[$pid];   // precio de la BD, no del formulario

                    $producto = Producto::with('inventario')->lockForUpdate()->findOrFail($pid);
                    $inv      = $producto->inventario;

                    if (!$inv || $inv->cantidad_stock < $cant) {
                        throw new \Exception("Stock insuficiente para: {$producto->nombre}");
                    }

                    // Línea de detalle con precio de la BD
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
                        'user_id'         => $userId,
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
        // [4] Doble protección: ruta ya está en grupo admin, pero verificamos también aquí
        if (!Auth::user()->esAdmin()) abort(403);

        if ($venta->estaAnulada()) {
            return redirect()->route('ventas.index')
                ->with('error', 'Esta venta ya está anulada.');
        }

        // Capturar ID antes del closure
        $userId = Auth::user()->id;

        try {
            DB::transaction(function () use ($venta, $userId) {
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
                            'user_id'         => $userId,
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

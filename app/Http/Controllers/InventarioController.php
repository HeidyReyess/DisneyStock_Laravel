<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    // GET /inventario
    public function index(Request $request)
    {
        $query = Producto::with(['inventario', 'categoria'])
            ->where('estado', 'activo')
            ->orderBy('nombre');

        // Filtro stock bajo
        if ($request->filtro === 'bajo') {
            $query->whereHas('inventario', function ($q) {
                $q->whereColumn('cantidad_stock', '<=', 'stock_minimo')
                  ->where('stock_minimo', '>', 0);
            });
        }

        // Filtro por producto específico (para el panel de movimientos)
        if ($request->filled('producto_id')) {
            $query->where('id', $request->producto_id);
        }

        $productos   = $query->get();
        $totalBajo   = Producto::whereHas('inventario', function ($q) {
            $q->whereColumn('cantidad_stock', '<=', 'stock_minimo')
              ->where('stock_minimo', '>', 0);
        })->where('estado', 'activo')->count();

        // Últimos 12 movimientos (filtrados por producto si se indicó)
        $movQuery = MovimientoInventario::with(['producto', 'user'])
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(12);

        if ($request->filled('producto_id')) {
            $movQuery->where('producto_id', $request->producto_id);
        }

        $movimientos     = $movQuery->get();
        $productosSelect = Producto::with('inventario')->where('estado', 'activo')->orderBy('nombre')->get();

        return view('admin.inventario.index', compact(
            'productos', 'totalBajo', 'movimientos', 'productosSelect'
        ));
    }

    // GET /inventario/historial
    public function historial(Request $request)
    {
        $query = MovimientoInventario::with(['producto', 'user'])
            ->orderByDesc('fecha')
            ->orderByDesc('id');

        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }
        if ($request->filled('tipo')) {
            $query->where('tipo_movimiento', $request->tipo);
        }
        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->hasta);
        }

        $movimientos     = $query->paginate(20)->withQueryString();
        $productosSelect = Producto::orderBy('nombre')->get();

        return view('admin.inventario.historial', compact(
            'movimientos', 'productosSelect'
        ));
    }

    // POST /inventario/movimiento  — solo entradas manuales
    public function movimiento(Request $request)
    {
        // [3] Doble protección: ruta ya está en grupo admin, pero verificamos también aquí
        if (!Auth::user()->esAdmin()) abort(403);
        $request->validate([
            'producto_id' => ['required', 'exists:productos,id'],
            'cantidad'    => ['required', 'integer', 'min:1'],
            'descripcion' => ['required', 'string', 'min:5', 'max:200'],
            'proveedor'   => ['nullable', 'string', 'max:100'],
        ], [
            'producto_id.required' => 'Selecciona un producto.',
            'cantidad.required'    => 'La cantidad es obligatoria.',
            'cantidad.min'         => 'La cantidad debe ser al menos 1.',
            'descripcion.required' => 'La descripción o motivo es obligatorio.',
            'descripcion.min'      => 'La descripción debe tener al menos 5 caracteres.',
        ]);

        $producto = Producto::with('inventario')->findOrFail($request->producto_id);

        if (!$producto->inventario) {
            return redirect()->route('inventario.index')
                ->with('error', 'Este producto no tiene registro de inventario.');
        }

        // Capturar el ID antes del closure — Auth::id() retorna el campo 'usuario' (string) porque
        // getAuthIdentifierName() devuelve 'usuario'. Hay que usar ->id explícito para el PK numérico.
        $userId = Auth::user()->id;

        DB::transaction(function () use ($request, $producto, $userId) {
            // Registrar movimiento
            MovimientoInventario::create([
                'tipo_movimiento' => 'entrada',
                'cantidad'        => $request->cantidad,
                'fecha'           => now()->toDateString(),
                'descripcion'     => $request->descripcion,
                'proveedor'       => $request->proveedor,
                'producto_id'     => $producto->id,
                'user_id'         => $userId,
            ]);

            // Sumar stock
            $producto->inventario->increment('cantidad_stock', $request->cantidad);
            $producto->inventario->update(['fecha_actualizacion' => now()->toDateString()]);

            // Resolver alertas activas si el stock superó el mínimo
            $inv = $producto->inventario->fresh();
            if ($inv->stock_minimo > 0 && $inv->cantidad_stock > $inv->stock_minimo) {
                \App\Models\Alerta::where('producto_id', $producto->id)
                    ->where('estado', 'activa')
                    ->update([
                        'estado'            => 'resuelta',
                        'fecha_resolucion'  => now()->toDateString(),
                    ]);
            }
        });

        return redirect()->route('inventario.index')
            ->with('success', "Entrada de {$request->cantidad} unidades registrada para \"{$producto->nombre}\".");
    }
}

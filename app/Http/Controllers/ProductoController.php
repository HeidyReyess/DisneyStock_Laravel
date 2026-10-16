<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Inventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    // GET /productos
    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'inventario'])
            ->orderBy('nombre');

        if ($request->filled('q')) {
            $query->where('nombre', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $productos   = $query->get();
        $categorias  = Categoria::orderBy('nombre_categoria')->get();

        return view('admin.productos.index', compact('productos', 'categorias'));
    }

    // POST /productos
    public function store(Request $request)
    {
        $request->validate([
            'nombre'        => ['required', 'string', 'max:255'],
            'precio_venta'  => ['required', 'numeric', 'min:0'],
            'precio_compra' => ['required', 'numeric', 'min:0'],
            'stock_inicial' => ['required', 'integer', 'min:0'],
            'stock_minimo'  => ['required', 'integer', 'min:0'],
            'fecha_ingreso' => ['required', 'date'],
            'proveedor'     => ['nullable', 'string', 'max:200'],
            'categoria_id'  => ['nullable', 'exists:categorias,id'],
            'imagen'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'nombre.required'       => 'El nombre del producto es obligatorio.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'precio_venta.min'      => 'El precio de venta no puede ser negativo.',
            'stock_inicial.min'     => 'El stock inicial no puede ser negativo.',
            'imagen.image'          => 'El archivo debe ser una imagen.',
            'imagen.max'            => 'La imagen no puede superar 2MB.',
        ]);

        $imagenNombre = null;
        if ($request->hasFile('imagen')) {
            $imagenNombre = $request->file('imagen')->store('productos', 'public');
        }

        DB::transaction(function () use ($request, $imagenNombre) {
            $producto = Producto::create([
                'nombre'        => $request->nombre,
                'precio_venta'  => $request->precio_venta,
                'precio_compra' => $request->precio_compra,
                'fecha_ingreso' => $request->fecha_ingreso,
                'proveedor'     => $request->proveedor,
                'imagen'        => $imagenNombre,
                'categoria_id'  => $request->categoria_id,
                'estado'        => 'activo',
            ]);

            Inventario::create([
                'producto_id'        => $producto->id,
                'cantidad_stock'     => $request->stock_inicial,
                'stock_minimo'       => $request->stock_minimo,
                'fecha_actualizacion'=> now()->toDateString(),
            ]);
        });

        return redirect()->route('productos.index')
            ->with('success', "Producto \"{$request->nombre}\" creado correctamente.");
    }

    // PUT /productos/{producto}
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre'        => ['required', 'string', 'max:255'],
            'precio_venta'  => ['required', 'numeric', 'min:0'],
            'precio_compra' => ['required', 'numeric', 'min:0'],
            'stock_minimo'  => ['required', 'integer', 'min:0'],
            'fecha_ingreso' => ['required', 'date'],
            'proveedor'     => ['nullable', 'string', 'max:200'],
            'categoria_id'  => ['nullable', 'exists:categorias,id'],
            'imagen'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $imagenNombre = $producto->imagen;
        if ($request->hasFile('imagen')) {
            // Borrar imagen anterior
            if ($imagenNombre) {
                Storage::disk('public')->delete($imagenNombre);
            }
            $imagenNombre = $request->file('imagen')->store('productos', 'public');
        }

        DB::transaction(function () use ($request, $producto, $imagenNombre) {
            $producto->update([
                'nombre'        => $request->nombre,
                'precio_venta'  => $request->precio_venta,
                'precio_compra' => $request->precio_compra,
                'fecha_ingreso' => $request->fecha_ingreso,
                'proveedor'     => $request->proveedor,
                'imagen'        => $imagenNombre,
                'categoria_id'  => $request->categoria_id,
            ]);

            if ($producto->inventario) {
                $producto->inventario->update([
                    'stock_minimo'        => $request->stock_minimo,
                    'fecha_actualizacion' => now()->toDateString(),
                ]);
            }
        });

        return redirect()->route('productos.index')
            ->with('success', "Producto \"{$producto->nombre}\" actualizado correctamente.");
    }

    // PATCH /productos/{producto}/toggle
    public function toggle(Producto $producto)
    {
        $producto->update([
            'estado' => $producto->estado === 'activo' ? 'inactivo' : 'activo',
        ]);

        $estado = $producto->estado === 'activo' ? 'activado' : 'desactivado';

        return redirect()->route('productos.index')
            ->with('success', "Producto \"{$producto->nombre}\" {$estado}.");
    }

    // DELETE /productos/{producto}
    public function destroy(Producto $producto)
    {
        if ($producto->tieneDependencias()) {
            return redirect()->route('productos.index')
                ->with('error', "No se puede eliminar \"{$producto->nombre}\": tiene ventas o movimientos registrados. Desactívalo en su lugar.");
        }

        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $nombre = $producto->nombre;
        $producto->delete(); // cascadea a inventario y alertas

        return redirect()->route('productos.index')
            ->with('success', "Producto \"{$nombre}\" eliminado correctamente.");
    }
}

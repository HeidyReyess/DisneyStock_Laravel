<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Inventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;   // [fix] faltaba este use — causaba "Class Auth not found"
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

        $productos  = $query->get();
        $categorias = Categoria::orderBy('nombre_categoria')->get();

        // Si viene de un error de validación al editar, buscar el producto
        $productoEditando = null;
        if (old('_edit_id')) {
            $productoEditando = Producto::with('inventario')->find(old('_edit_id'));
        }

        return view('admin.productos.index', compact('productos', 'categorias', 'productoEditando'));
    }

    // POST /productos
    public function store(Request $request)
    {
        if (!Auth::user()->esAdmin()) abort(403);

        // [4][6][7] Validaciones actualizadas: enteros, mínimos, categoría obligatoria
        $request->validate([
            'nombre'        => ['required', 'string', 'max:255', 'unique:productos,nombre'],
            'precio_venta'  => ['required', 'integer', 'min:1'],
            'precio_compra' => ['required', 'integer', 'min:1'],
            'stock_inicial' => ['required', 'integer', 'min:0'],
            'stock_minimo'  => ['required', 'integer', 'min:1'],
            'fecha_ingreso' => ['required', 'date'],
            'proveedor'     => ['nullable', 'string', 'max:200'],
            'categoria_id'  => ['required', 'exists:categorias,id'],
            'imagen'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'nombre.required'         => 'El nombre del producto es obligatorio.',
            'nombre.max'              => 'El nombre no puede superar 255 caracteres.',
            'nombre.unique'           => 'Ya existe un producto con ese nombre.',
            'precio_venta.required'   => 'El precio de venta es obligatorio.',
            'precio_venta.integer'    => 'El precio de venta debe ser un número entero.',
            'precio_venta.min'        => 'El precio de venta debe ser al menos 1.',
            'precio_compra.required'  => 'El precio de compra es obligatorio.',
            'precio_compra.integer'   => 'El precio de compra debe ser un número entero.',
            'precio_compra.min'       => 'El precio de compra debe ser al menos 1.',
            'stock_inicial.required'  => 'El stock inicial es obligatorio.',
            'stock_inicial.integer'   => 'El stock inicial debe ser un número entero.',
            'stock_inicial.min'       => 'El stock inicial no puede ser negativo.',
            'stock_minimo.required'   => 'El stock mínimo es obligatorio.',
            'stock_minimo.integer'    => 'El stock mínimo debe ser un número entero.',
            'stock_minimo.min'        => 'El stock mínimo debe ser al menos 1.',
            'fecha_ingreso.required'  => 'La fecha de ingreso es obligatoria.',
            'categoria_id.required'   => 'La categoría es obligatoria.',
            'categoria_id.exists'     => 'La categoría seleccionada no existe.',
            'imagen.image'            => 'El archivo debe ser una imagen.',
            'imagen.max'              => 'La imagen no puede superar 2MB.',
        ]);

        // [5] Precio de venta debe ser mayor que precio de compra
        if ((int)$request->precio_venta <= (int)$request->precio_compra) {
            return back()
                ->withErrors(['precio_venta' => 'El precio de venta debe ser mayor que el precio de compra.'])
                ->withInput();
        }

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
                'producto_id'         => $producto->id,
                'cantidad_stock'      => $request->stock_inicial,
                'stock_minimo'        => $request->stock_minimo,
                'fecha_actualizacion' => now()->toDateString(),
            ]);
        });

        return redirect()->route('productos.index')
            ->with('success', "Producto \"{$request->nombre}\" creado correctamente.");
    }

    // PUT /productos/{producto}
    public function update(Request $request, Producto $producto)
    {
        if (!Auth::user()->esAdmin()) abort(403);

        // [4][6][7] Validaciones actualizadas
        $request->validate([
            'nombre'        => ['required', 'string', 'max:255', "unique:productos,nombre,{$producto->id}"],
            'precio_venta'  => ['required', 'integer', 'min:1'],
            'precio_compra' => ['required', 'integer', 'min:1'],
            'stock_minimo'  => ['required', 'integer', 'min:1'],
            'fecha_ingreso' => ['required', 'date'],
            'proveedor'     => ['nullable', 'string', 'max:200'],
            'categoria_id'  => ['required', 'exists:categorias,id'],
            'imagen'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'nombre.required'        => 'El nombre del producto es obligatorio.',
            'nombre.max'             => 'El nombre no puede superar 255 caracteres.',
            'precio_venta.required'  => 'El precio de venta es obligatorio.',
            'precio_venta.integer'   => 'El precio de venta debe ser un número entero.',
            'precio_venta.min'       => 'El precio de venta debe ser al menos 1.',
            'precio_compra.required' => 'El precio de compra es obligatorio.',
            'precio_compra.integer'  => 'El precio de compra debe ser un número entero.',
            'precio_compra.min'      => 'El precio de compra debe ser al menos 1.',
            'stock_minimo.required'  => 'El stock mínimo es obligatorio.',
            'stock_minimo.integer'   => 'El stock mínimo debe ser un número entero.',
            'stock_minimo.min'       => 'El stock mínimo debe ser al menos 1.',
            'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
            'categoria_id.required'  => 'La categoría es obligatoria.',
            'categoria_id.exists'    => 'La categoría seleccionada no existe.',
            'imagen.image'           => 'El archivo debe ser una imagen.',
            'imagen.max'             => 'La imagen no puede superar 2MB.',
        ]);

        // [5] Precio de venta debe ser mayor que precio de compra
        if ((int)$request->precio_venta <= (int)$request->precio_compra) {
            return back()
                ->withErrors(['precio_venta' => 'El precio de venta debe ser mayor que el precio de compra.'])
                ->withInput();
        }

        $imagenNombre = $producto->imagen;
        if ($request->hasFile('imagen')) {
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
        if (!Auth::user()->esAdmin()) abort(403);

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
        if (!Auth::user()->esAdmin()) abort(403);

        if ($producto->tieneDependencias()) {
            return redirect()->route('productos.index')
                ->with('error', "No se puede eliminar \"{$producto->nombre}\": tiene ventas o movimientos registrados. Desactívalo en su lugar.");
        }

        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $nombre = $producto->nombre;
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', "Producto \"{$nombre}\" eliminado correctamente.");
    }
}

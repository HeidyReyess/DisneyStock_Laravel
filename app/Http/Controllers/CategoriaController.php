<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    // GET /categorias
    public function index()
    {
        $categorias = Categoria::withCount('productos')
            ->orderBy('nombre_categoria')
            ->get();

        return view('admin.categorias.index', compact('categorias'));
    }

    // POST /categorias
    public function store(Request $request)
    {
        $request->validate([
            'nombre_categoria' => ['required', 'string', 'max:100', 'unique:categorias,nombre_categoria'],
            'descripcion'      => ['nullable', 'string', 'max:200'],
        ], [
            'nombre_categoria.required' => 'El nombre es obligatorio.',
            'nombre_categoria.unique'   => 'Ya existe una categoría con ese nombre.',
            'nombre_categoria.max'      => 'El nombre no puede superar 100 caracteres.',
        ]);

        Categoria::create($request->only('nombre_categoria', 'descripcion'));

        return redirect()->route('categorias.index')
            ->with('success', "Categoría \"{$request->nombre_categoria}\" creada correctamente.");
    }

    // PUT /categorias/{categoria}
    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre_categoria' => ['required', 'string', 'max:100', "unique:categorias,nombre_categoria,{$categoria->id}"],
            'descripcion'      => ['nullable', 'string', 'max:200'],
        ], [
            'nombre_categoria.required' => 'El nombre es obligatorio.',
            'nombre_categoria.unique'   => 'Ya existe una categoría con ese nombre.',
            'nombre_categoria.max'      => 'El nombre no puede superar 100 caracteres.',
        ]);

        $categoria->update($request->only('nombre_categoria', 'descripcion'));

        return redirect()->route('categorias.index')
            ->with('success', "Categoría \"{$categoria->nombre_categoria}\" actualizada correctamente.");
    }

    // DELETE /categorias/{categoria}
    public function destroy(Categoria $categoria)
    {
        if ($categoria->tieneProductos()) {
            return redirect()->route('categorias.index')
                ->with('error', 'No se puede eliminar una categoría que tiene productos asociados.');
        }

        $nombre = $categoria->nombre_categoria;
        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('success', "Categoría \"{$nombre}\" eliminada correctamente.");
    }
}

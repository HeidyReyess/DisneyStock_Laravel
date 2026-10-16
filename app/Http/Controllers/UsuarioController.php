<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    // GET /usuarios
    public function index()
    {
        $usuarios = User::with('rol')->orderBy('nombre')->get();
        $roles    = Rol::orderBy('nombre')->get();

        return view('admin.usuarios.index', compact('usuarios', 'roles'));
    }

    // POST /usuarios
    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => ['required', 'string', 'max:150'],
            'usuario'  => ['required', 'string', 'max:80', 'unique:users,usuario'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'rol_id'   => ['required', 'exists:roles,id'],
        ], [
            'nombre.required'    => 'El nombre es obligatorio.',
            'usuario.required'   => 'El nombre de usuario es obligatorio.',
            'usuario.unique'     => 'Ese nombre de usuario ya está en uso.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'rol_id.required'    => 'Selecciona un rol.',
        ]);

        User::create([
            'nombre'   => $request->nombre,
            'usuario'  => $request->usuario,
            'password' => Hash::make($request->password),
            'rol_id'   => $request->rol_id,
            'estado'   => 'activo',
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario \"{$request->usuario}\" creado correctamente.");
    }

    // PUT /usuarios/{usuario}
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'nombre'  => ['required', 'string', 'max:150'],
            'usuario' => ['required', 'string', 'max:80', "unique:users,usuario,{$usuario->id}"],
            'rol_id'  => ['required', 'exists:roles,id'],
        ], [
            'nombre.required'  => 'El nombre es obligatorio.',
            'usuario.required' => 'El nombre de usuario es obligatorio.',
            'usuario.unique'   => 'Ese nombre de usuario ya está en uso.',
            'rol_id.required'  => 'Selecciona un rol.',
        ]);

        $usuario->update([
            'nombre'  => $request->nombre,
            'usuario' => $request->usuario,
            'rol_id'  => $request->rol_id,
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario \"{$usuario->usuario}\" actualizado correctamente.");
    }

    // PATCH /usuarios/{usuario}/reset-password
    public function resetPassword(Request $request, User $usuario)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required'  => 'La nueva contraseña es obligatoria.',
            'password.min'       => 'Debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $usuario->update(['password' => Hash::make($request->password)]);

        return redirect()->route('usuarios.index')
            ->with('success', "Contraseña de \"{$usuario->usuario}\" restablecida correctamente.");
    }

    // PATCH /usuarios/{usuario}/toggle
    public function toggle(User $usuario)
    {
        // No se puede desactivar la propia cuenta
        if ($usuario->id === Auth::id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $usuario->update([
            'estado' => $usuario->estado === 'activo' ? 'inactivo' : 'activo',
        ]);

        $estado = $usuario->estado === 'activo' ? 'activado' : 'desactivado';

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario \"{$usuario->usuario}\" {$estado}.");
    }

    // DELETE /usuarios/{usuario}
    public function destroy(User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $nombre = $usuario->usuario;
        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario \"{$nombre}\" eliminado correctamente.");
    }
}

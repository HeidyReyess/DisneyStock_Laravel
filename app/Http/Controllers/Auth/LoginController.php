<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'usuario'  => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'usuario.required'  => 'El campo usuario es obligatorio.',
            'password.required' => 'El campo contraseña es obligatorio.',
        ]);

        // Intentar autenticar con usuario y contraseña
        if (Auth::attempt(['usuario' => $credentials['usuario'], 'password' => $credentials['password']], $request->boolean('remember'))) {

            // [2] Verificar que el usuario esté activo después de autenticar
            if (Auth::user()->estado !== 'activo') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'usuario' => 'Tu usuario está desactivado. Comunícate con la administradora del sistema.',
                ])->onlyInput('usuario');
            }

            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'usuario' => 'Usuario o contraseña incorrectos.',
        ])->onlyInput('usuario');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}

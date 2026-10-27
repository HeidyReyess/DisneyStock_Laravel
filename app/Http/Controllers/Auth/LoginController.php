<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

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

        // ── Throttle: máximo 5 intentos por usuario+IP en 60 segundos ──
        $throttleKey = Str::lower($credentials['usuario']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'usuario' => "Demasiados intentos fallidos. Intenta de nuevo en {$seconds} " .
                             ($seconds === 1 ? 'segundo' : 'segundos') . '.',
            ])->onlyInput('usuario');
        }

        // Intentar autenticar con usuario y contraseña
        if (Auth::attempt(['usuario' => $credentials['usuario'], 'password' => $credentials['password']], $request->boolean('remember'))) {

            // Verificar que el usuario esté activo después de autenticar
            if (Auth::user()->estado !== 'activo') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'usuario' => 'Tu usuario está desactivado. Comunícate con la administradora del sistema.',
                ])->onlyInput('usuario');
            }

            // Login correcto: limpiar el contador de intentos
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // Incrementar el contador de intentos fallidos (decay: 60 segundos)
        RateLimiter::hit($throttleKey, 60);

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

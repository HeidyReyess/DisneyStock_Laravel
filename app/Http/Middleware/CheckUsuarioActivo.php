<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUsuarioActivo
{
    // [2] Expulsa al usuario si fue desactivado mientras tenía la sesión abierta
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->estado !== 'activo') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'usuario' => 'Tu usuario está desactivado. Comunícate con la administradora del sistema.',
            ]);
        }

        return $next($request);
    }
}

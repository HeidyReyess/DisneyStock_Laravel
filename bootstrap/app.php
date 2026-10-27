<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'     => \App\Http\Middleware\CheckRol::class,
            'no-cache' => \App\Http\Middleware\NoCache::class,
            'activo'   => \App\Http\Middleware\CheckUsuarioActivo::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Redirigir al login con mensaje personalizado cuando la sesión expira
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if (!$request->expectsJson()) {
                $sessionCookie = config('session.cookie');
                $teniaSesionPrevia = $request->hasCookie($sessionCookie) || $request->hasCookie(session()->getName());

                $redirect = redirect()->guest(route('login'));

                if ($teniaSesionPrevia) {
                    $redirect->with('error', 'Su sesión ha expirado. Inicia sesión nuevamente.');
                }

                return $redirect;
            }
        });
    })->create();

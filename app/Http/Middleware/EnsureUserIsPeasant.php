<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsPeasant
{
    /**
     * Manejar una solicitud entrante.
     * Verifica que el usuario sea campesino
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isPeasant()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No autorizado'], 403);
            }
            
            abort(403, 'Acceso no autorizado.');
        }

        return $next($request);
    }
}


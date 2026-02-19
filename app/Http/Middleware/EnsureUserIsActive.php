<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Manejar una solicitud entrante.
     * Verifica que el usuario esté activo
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->isActive()) {
            auth()->logout();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tu cuenta está inactiva. Por favor contacta al administrador.'
                ], 403);
            }
            
            return redirect()->route('login')
                ->with('error', 'Tu cuenta está inactiva. Por favor contacta al administrador.');
        }

        return $next($request);
    }
}


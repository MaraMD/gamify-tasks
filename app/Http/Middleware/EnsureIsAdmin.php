<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar que el usuario esté autenticado
        if (!auth()->check()) {
            abort(403, 'No autenticado.');
        }

        // Verificar que el usuario tenga privilegios de administrador
        if (!auth()->user()->is_admin) {
            abort(403, 'Acceso denegado. Se requieren privilegios de administrador.');
        }

        return $next($request);
    }
}

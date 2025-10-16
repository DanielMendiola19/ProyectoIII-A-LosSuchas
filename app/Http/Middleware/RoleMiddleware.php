<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        $rolUsuario = $user->rol->nombre;

        // Si el rol del usuario no está en la lista permitida, lo saca
        if (!in_array($rolUsuario, $roles)) {
            abort(403, 'No tienes permiso para acceder a esta página');
        }

        return $next($request);
    }
}
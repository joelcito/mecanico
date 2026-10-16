<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $usuario = $request->user();

        abort_unless(
            $usuario &&
            in_array((string) $usuario->rol_id, $roles, true),
            403,
            'No tienes permiso para acceder a esta sección.'
        );

        return $next($request);
    }
}
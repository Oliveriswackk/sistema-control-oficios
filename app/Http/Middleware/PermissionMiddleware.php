<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        if (!$user->hasPermission($permission)) {
            abort(403, 'No autorizado por permiso');
        }

        return $next($request);
    }
}
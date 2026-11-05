<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        // Administrator / Server bypass semua role
        if (method_exists($user, 'isAdministrator') && $user->isAdministrator()) {
            return $next($request);
        }

        if (empty($roles)) {
            return $next($request);
        }

        if ($user->hasAnyRole($roles)) {
            return $next($request);
        }

        abort(403, 'Akses ditolak: Anda tidak memiliki role yang diperlukan.');
    }
}


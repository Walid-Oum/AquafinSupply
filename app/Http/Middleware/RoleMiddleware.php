<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
   public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check of de gebruiker is ingelogd en of zijn rol in de toegestane lijst staat
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            abort(403, 'Onbevoegde toegang.');
        }

        return $next($request);
    }

    // Kleine helper om te checken of de rol matcht
    private function userHasRole($user, $roles)
    {
        return in_array($user->role, $roles);
    }
}
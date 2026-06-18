<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware voor rolgebaseerde toegang.
 *
 * Deze middleware controleert of een ingelogde gebruiker
 * één van de toegestane rollen heeft voor een bepaalde route.
 *
 * Wordt gebruikt om routes af te schermen voor rollen zoals:
 * - admin
 * - technieker
 * - magazijn
 */
class RoleMiddleware
{
    /**
     * Controleer of de gebruiker ingelogd is en een toegestane rol heeft.
     *
     * Wanneer de gebruiker niet is ingelogd of zijn rol niet voorkomt in
     * de lijst van toegestane rollen, wordt de request afgebroken met
     * een 403-foutmelding.
     *
     * @param Request $request De inkomende HTTP-request.
     * @param Closure $next De volgende stap in de middleware-keten.
     * @param string ...$roles De toegestane rollen voor deze route.
     * @return Response De HTTP-response na toegangcontrole.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check of de gebruiker is ingelogd en of zijn rol in de toegestane lijst staat
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            abort(403, 'Onbevoegde toegang.');
        }

        return $next($request);
    }

    /**
     * Controleer of een gebruiker één van de opgegeven rollen heeft.
     *
     * Deze helper kan gebruikt worden om rolcontrole apart uit te voeren.
     *
     * @param mixed $user De gebruiker die gecontroleerd wordt.
     * @param array $roles De toegestane rollen.
     * @return bool True als de rol overeenkomt, anders false.
     */
    private function userHasRole($user, $roles)
    {
        return in_array($user->role, $roles);
    }
}

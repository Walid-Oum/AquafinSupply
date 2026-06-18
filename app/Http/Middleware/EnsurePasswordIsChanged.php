<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    /**
     * Behandel de binnenkomende aanvraag (Request).
     * * Deze middleware controleert of een ingelogde gebruiker verplicht zijn 
     * wachtwoord moet wijzigen voordat hij toegang krijgt tot de rest van de applicatie.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Haal de momenteel ingelogde gebruiker op
        $user = $request->user();
// Controleer op de verplichte wachtwoordwijziging
        if (
            $user &&
            $user->must_change_password &&
            ! $request->routeIs('password.change') &&
            ! $request->routeIs('password.change.update') &&
            ! $request->routeIs('logout')
        ) {
            // Als de gebruiker zijn wachtwoord móét wijzigen en ergens anders naartoe navigeert,
            // sturen we hem direct door naar de pagina voor wachtwoordwijziging.
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/**
 * Laravel Application Bootstrapper
 * 
 * Configureert de Laravel applicatie met routing, middleware en exception handling.
 * Registreert custom middleware: 'role' voor rolgebaseerde toegang en
 * 'password.changed' voor wachtwoordwijzigingscontrole.
 *
 * @author 
 * @version 1.0
 */

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Registreer 'role' middleware voor rolgebaseerde autorisatie
        $middleware->alias([
            'role' => App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withMiddleware(function (Middleware $middleware) {
        // Registreer 'password.changed' middleware om te controleren of gebruiker wachtwoord heeft gewijzigd
        $middleware->alias([
            'password.changed' => \App\Http\Middleware\EnsurePasswordIsChanged::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

    })->create();
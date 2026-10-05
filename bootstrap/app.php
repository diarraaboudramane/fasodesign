<?php

use App\Http\Middleware\EnTetesDeSecurite;
use App\Http\Middleware\VerifierHumain;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /*
         * La documentation se lit sans compte ni formulaire : elle n'a
         * besoin ni de session, ni de jeton anti-falsification, ni de
         * cookie. Les retirer, c'est aussi n'avoir aucun traceur à
         * déclarer, et rien à demander à l'usager avant de lire.
         */
        $session = [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            PreventRequestForgery::class,
        ];
        $middleware->web(remove: $session);

        /* Le formulaire de contact, lui, en a besoin : la session et le
           jeton ne sont rendus qu'aux routes qui le déclarent. */
        $middleware->group('formulaire', $session);

        /* La vérification anti-robot lit un cookie chiffré : il faut le
           déchiffrer avant de le lire. Aucune session n'est ouverte. */
        $middleware->group('humain', [
            EncryptCookies::class,
            VerifierHumain::class,
        ]);

        /* Global, et non dans le groupe web : une adresse qui ne
           correspond à aucune route reçoit aussi les en-têtes. */
        $middleware->append(EnTetesDeSecurite::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

<?php

namespace App\Providers;

use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

/**
 * Ce que l'application doit savoir de l'hébergement pour se comporter
 * en ligne comme sur le poste du développeur.
 */
class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        /*
         * Derrière un proxy qui termine le TLS, l'application reçoit du
         * http : elle écrirait des liens en http, que le navigateur
         * bloque dans une page https, et la page s'afficherait sans
         * styles. L'adresse publique déclarée fait foi.
         */
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        /*
         * Sans mandataire de confiance déclaré, tous les visiteurs ont
         * l'adresse du proxy : la limitation des envois les compterait
         * comme un seul, et un vrai moteur de recherche ne serait plus
         * reconnu. L'en-tête X-Forwarded-For n'est cru que s'il vient de
         * ces adresses.
         */
        $mandataires = trim((string) config('charte.mandataires'));
        if ($mandataires !== '') {
            TrustProxies::at($mandataires === '*'
                ? '*'
                : preg_split('/[\s,]+/', $mandataires, -1, PREG_SPLIT_NO_EMPTY));
        }
    }
}

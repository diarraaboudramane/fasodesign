<?php

namespace App\Http\Middleware;

use App\Support\Audience;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Compte la page vue, si le visiteur est passé par la case anti-robot.
 * Placé après VerifierHumain dans le groupe « humain » : un visiteur non
 * vérifié est renvoyé avant d'arriver ici, et un moteur de recherche,
 * admis sans case, n'a pas le cookie et n'est pas compté.
 */
class MesurerAudience
{
    public function handle(Request $request, Closure $next): Response
    {
        if (VerifierHumain::estVerifie($request)) {
            Audience::enregistrer($request);
        }

        return $next($request);
    }
}

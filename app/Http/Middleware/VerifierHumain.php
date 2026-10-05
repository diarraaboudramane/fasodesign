<?php

namespace App\Http\Middleware;

use App\Support\MoteurDeRecherche;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Renvoie vers la case reCAPTCHA quiconque n'a pas encore prouvé, sur cet
 * appareil, qu'il n'est pas un robot.
 *
 * La preuve est un cookie chiffré portant l'heure de la vérification :
 * l'application ne garde rien, et le cookie ne peut être ni forgé ni
 * prolongé sans la clé de l'application.
 */
class VerifierHumain
{
    public const COOKIE = 'charte_humain';

    public function __construct(private readonly MoteurDeRecherche $moteurs) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('charte.verification.active')
            || self::estVerifie($request)
            || $this->moteurs->authentique($request)) {
            return $next($request);
        }

        return redirect()->route('verification', ['retour' => $request->getRequestUri()]);
    }

    public static function estVerifie(Request $request): bool
    {
        $depuis = $request->cookie(self::COOKIE);

        return is_string($depuis) && ctype_digit($depuis)
            && (int) $depuis + 60 * config('charte.verification.duree') > time()
            && (int) $depuis <= time() + 60;
    }
}

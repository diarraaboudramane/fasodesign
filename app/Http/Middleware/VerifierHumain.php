<?php

namespace App\Http\Middleware;

use App\Support\MoteurDeRecherche;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Renvoie vers la case reCAPTCHA quiconque n'a pas encore prouvé, sur cet
 * appareil, qu'il n'est pas un robot.
 *
 * La preuve est un cookie chiffré portant l'heure de la dernière page vue :
 * l'application ne garde rien, et le cookie ne peut être ni forgé ni
 * prolongé sans la clé de l'application.
 *
 * Le délai reprend à chaque page : la case n'est redemandée qu'après
 * config('charte.verification.duree') minutes sans aucune page vue, et
 * jamais en pleine lecture.
 */
class VerifierHumain
{
    public const COOKIE = 'charte_humain';

    public function __construct(private readonly MoteurDeRecherche $moteurs) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('charte.verification.active')) {
            return $next($request);
        }

        if (self::estVerifie($request)) {
            $reponse = $next($request);
            /* L'activité prolonge la vérification. Le cookie est écrit en
               clair ici et chiffré au retour par EncryptCookies, qui
               enveloppe ce middleware dans le groupe « humain ». */
            $reponse->headers->setCookie(self::cookie());

            return $reponse;
        }

        if ($this->moteurs->authentique($request)) {
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

    /** Le cookie de vérification, daté de maintenant. */
    public static function cookie(): Cookie
    {
        return cookie(
            self::COOKIE,
            (string) time(),
            config('charte.verification.duree'),
            secure: config('session.secure'),
            httpOnly: true,
            sameSite: 'lax',
        );
    }
}

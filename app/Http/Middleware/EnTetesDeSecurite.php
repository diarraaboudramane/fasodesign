<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Les en-têtes que hebergement/apache.htaccess pose sur le site
 * statique, posés ici par l'application elle-même : ils valent alors
 * quel que soit le serveur placé devant, et en développement aussi.
 */
class EnTetesDeSecurite
{
    /*
     * img-src data: est indispensable : les icônes de la charte sont des
     * masques CSS encodés en data:. Sans cette autorisation, elles
     * disparaissent sans autre symptôme.
     *
     * style-src-attr : les pages de la documentation placent leurs
     * exemples par des attributs style (largeur d'une scène, taille d'un
     * emblème, teinte d'un nuancier). Sans cette autorisation, ces
     * attributs sont ignorés et les démonstrations se déforment. Elle ne
     * vaut que pour les attributs : ni <style>, ni script en ligne. La
     * charte elle-même n'en a pas besoin, et la page Intégration le dit.
     */
    public const CSP = "default-src 'self'; img-src 'self' data:; font-src 'self'; style-src 'self'; "
        ."style-src-attr 'unsafe-inline'; "
        ."script-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'";

    /*
     * Les pages de contact et de vérification chargent reCAPTCHA : son
     * script vient de Google, et la case s'affiche dans un cadre servi
     * par Google. Ces origines ne sont ouvertes que sur ces pages.
     */
    public const CSP_RECAPTCHA = "default-src 'self'; img-src 'self' data:; font-src 'self'; style-src 'self'; "
        ."style-src-attr 'unsafe-inline'; "
        ."script-src 'self' https://www.google.com/recaptcha/ https://www.gstatic.com/recaptcha/; "
        .'frame-src https://www.google.com/recaptcha/ https://recaptcha.google.com/recaptcha/; '
        ."frame-ancestors 'none'; base-uri 'self'; form-action 'self'";

    public function handle(Request $request, Closure $next): Response
    {
        $reponse = $next($request);
        $entetes = $reponse->headers;

        $entetes->set('X-Content-Type-Options', 'nosniff');
        $entetes->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $entetes->set('X-Frame-Options', 'DENY');

        /*
         * Réservation des droits de fouille de textes et de données, au
         * sens du protocole TDM du W3C. Sa portée est juridique : elle ne
         * bloque rien, elle établit que la reprise n'a pas été consentie.
         *
         * « noindex » n'y figure pas, et ne doit pas y figurer : le site
         * doit rester trouvable par les moteurs de recherche.
         */
        $entetes->set('X-Robots-Tag', config('charte.indexable')
            ? 'noai, noimageai'
            : 'noindex, nofollow, noai, noimageai');
        $entetes->set('TDM-Reservation', '1');

        /* La page d'erreur détaillée du mode débogage est faite de styles
           et de scripts en ligne : la politique la rendrait illisible au
           moment précis où l'on en a besoin. */
        if (! (config('app.debug') && $reponse->getStatusCode() >= 500)) {
            $entetes->set('Content-Security-Policy',
                $request->routeIs('contact', 'contact.*', 'verification', 'verification.*')
                    ? self::CSP_RECAPTCHA : self::CSP);
        }

        /* Les pages peuvent changer : elles se revalident. */
        if (str_starts_with((string) $entetes->get('Content-Type'), 'text/html')) {
            $entetes->set('Cache-Control', 'public, max-age=0, must-revalidate');
        }

        return $reponse;
    }
}

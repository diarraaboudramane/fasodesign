<?php

namespace App\Http\Controllers;

use App\Http\Middleware\VerifierHumain;
use App\Rules\Recaptcha;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class VerificationController extends Controller
{
    public function afficher(Request $request): View|RedirectResponse
    {
        $retour = self::retour($request->query('retour'));

        /* Déjà vérifié : rien à cocher. */
        if (VerifierHumain::estVerifie($request)) {
            return redirect($retour);
        }

        return view('verification', [
            'cleSite' => config('services.recaptcha.site_key'),
            'retour' => $retour,
        ]);
    }

    public function valider(Request $request): RedirectResponse
    {
        $request->validate([
            'g-recaptcha-response' => [new Recaptcha($request->ip())],
        ]);

        Cookie::queue(VerifierHumain::cookie());

        return redirect(self::retour($request->input('retour')));
    }

    /**
     * Le retour ne mène que vers une adresse de ce site : un chemin, jamais
     * « //ailleurs.example » ni « https://… ». Sans cela, la page de
     * vérification servirait de relais vers n'importe quel site.
     */
    private static function retour(mixed $chemin): string
    {
        return is_string($chemin) && preg_match('#^/(?![/\\\\])#', $chemin) && ! str_contains($chemin, "\n")
            ? $chemin
            : '/';
    }
}

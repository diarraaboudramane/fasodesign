<?php

namespace App\Http\Controllers;

use App\Http\Middleware\VerifierHumain;
use App\Support\Audience;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AudienceController extends Controller
{
    /**
     * Les chiffres, pour la mise à jour en direct du pied de l'accueil.
     *
     * Hors du groupe « humain » : ces appels réguliers ne doivent pas
     * prolonger la case anti-robot, sans quoi un onglet ouvert la
     * garderait valable indéfiniment. Ils maintiennent en revanche la
     * présence d'un visiteur vérifié dont l'onglet est visible.
     */
    public function chiffres(Request $request): JsonResponse
    {
        if (VerifierHumain::estVerifie($request)) {
            Audience::enregistrer($request);
        }

        return response()->json(Audience::chiffres())
            ->header('Cache-Control', 'no-store');
    }
}

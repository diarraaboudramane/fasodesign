<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * L'outil n'est ouvert qu'aux comptes confirmés et actifs. Un compte en
 * attente de validation, ou suspendu, voit pourquoi il ne peut pas encore
 * lancer d'analyse.
 */
class CompteActif
{
    public function handle(Request $request, Closure $next): Response
    {
        $compte = $request->user();

        if (! $compte->hasVerifiedEmail()) {
            return redirect()->route('conformite.courriel');
        }
        if ($compte->statut !== 'actif') {
            return response()->view('conformite.compte.attente', ['compte' => $compte], 403);
        }

        return $next($request);
    }
}

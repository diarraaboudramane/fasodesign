<?php

namespace App\Http\Controllers;

use App\Support\Recherche;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RechercheController extends Controller
{
    public function chercher(Request $request): View
    {
        $requete = Str::limit(trim((string) $request->query('q', '')), 200, '');

        return view('recherche', [
            'requete' => $requete,
            'resultats' => $requete === '' ? [] : Recherche::chercher($requete),
        ]);
    }
}

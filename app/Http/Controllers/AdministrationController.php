<?php

namespace App\Http\Controllers;

use App\Models\Signalement;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/** Comptes et écarts signalés : les deux tâches de l'équipe de la charte, chacune sur sa page. */
class AdministrationController extends Controller
{
    public function comptes(Request $request): View
    {
        abort_unless($request->user()->estAdministrateur(), 404);

        return view('conformite.comptes', [
            'enAttente' => User::where('statut', 'en_attente')->whereNotNull('email_verified_at')->oldest()->get(),
            'comptes' => User::where('statut', '!=', 'en_attente')->orderBy('entite')->orderBy('name')->get(),
        ]);
    }

    public function modifier(Request $request, User $compte): RedirectResponse
    {
        abort_unless($request->user()->estAdministrateur(), 404);
        $donnees = $request->validate([
            'statut' => ['required', 'in:actif,suspendu'],
            'role' => ['required', 'in:'.implode(',', User::ROLES)],
        ]);

        /* Un administrateur ne se retire pas lui-même ses droits : il resterait
           peut-être sans personne pour les lui rendre. */
        if ($compte->is($request->user()) && ($donnees['statut'] !== 'actif' || $donnees['role'] !== 'administrateur')) {
            return back()->withErrors(['compte' => 'Vous ne pouvez pas suspendre votre propre compte ni retirer vos droits.']);
        }

        $compte->forceFill($donnees)->save();
        Log::info('Compte modifié', ['compte' => $compte->id, 'par' => $request->user()->id] + $donnees);

        return back()->with('statut', "Compte de {$compte->name} mis à jour.");
    }

    public function signalements(Request $request): View
    {
        abort_unless($request->user()->estAdministrateur(), 404);
        $statut = $request->query('statut', 'nouveau');
        abort_unless($statut === 'tous' || isset(Signalement::STATUTS[$statut]), 404);

        $requete = Signalement::with(['analyse', 'auteur', 'traitePar'])->latest();
        if ($statut !== 'tous') {
            $requete->where('statut', $statut);
        }

        return view('conformite.signalements', [
            'signalements' => $requete->paginate(20)->withQueryString(),
            'statut' => $statut,
            'comptes' => Signalement::selectRaw('statut, count(*) as nombre')->groupBy('statut')->pluck('nombre', 'statut'),
            /* Les règles les plus contestées sont celles à revoir en premier. */
            'parRegle' => Signalement::where('statut', 'nouveau')->selectRaw('regle, count(*) as nombre')
                ->groupBy('regle')->orderByDesc('nombre')->limit(5)->pluck('nombre', 'regle'),
        ]);
    }

    public function traiter(Request $request, Signalement $signalement): RedirectResponse
    {
        abort_unless($request->user()->estAdministrateur(), 404);
        $donnees = $request->validate([
            'statut' => ['required', 'in:corrige,maintenu'],
            'reponse' => ['required_if:statut,maintenu', 'nullable', 'string', 'max:1000'],
        ], ['reponse.required_if' => 'Expliquez à l\'usager pourquoi l\'écart est maintenu.']);

        $signalement->forceFill($donnees + ['traite_par' => $request->user()->id, 'traite_le' => now()])->save();

        return back()->with('statut', 'Signalement traité : '.mb_strtolower(Signalement::STATUTS[$donnees['statut']]).'.');
    }
}

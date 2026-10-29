<?php

namespace App\Http\Controllers;

use App\Conformite\BilanMarkdown;
use App\Conformite\Collecte\CollecteRendu;
use App\Conformite\Reference;
use App\Jobs\AnalyserProjet;
use App\Models\Analyse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * L'outil de contrôle de conformité : lancer une analyse par adresse ou par
 * archive, suivre son état, lire et télécharger le bilan.
 */
class ConformiteController extends Controller
{
    /** Présentation de l'outil, ouverte à tous. */
    public function presentation(): View
    {
        return view('conformite.presentation');
    }

    public function espace(Request $request): View
    {
        $compte = $request->user();
        $analyses = $compte->analyses()->latest('id')->paginate(20);

        return view('conformite.espace', [
            'analyses' => $analyses,
            'restantes' => max(0, config('charte.conformite.analyses_par_jour') - $this->analysesDuJour($request)),
            'archiveMaxMo' => intdiv(config('charte.conformite.archive_max_ko'), 1024),
            'renduDisponible' => CollecteRendu::disponible(),
        ]);
    }

    public function lancer(Request $request): RedirectResponse|JsonResponse
    {
        if ($this->analysesDuJour($request) >= config('charte.conformite.analyses_par_jour')) {
            return $this->refuser($request, 'mode', 'Vous avez atteint le nombre d\'analyses autorisé pour aujourd\'hui. Réessayez demain.');
        }

        $donnees = $request->validate([
            'mode' => ['required', 'in:adresse,archive'],
            'adresse' => ['required_if:mode,adresse', 'nullable', 'url:http,https', 'max:500'],
            'archive' => ['required_if:mode,archive', 'nullable', 'file', 'extensions:zip', 'max:'.config('charte.conformite.archive_max_ko')],
            'rendu' => ['nullable', 'boolean'],
        ], [
            'adresse.required_if' => 'Indiquez l\'adresse du site à analyser.',
            'adresse.url' => 'L\'adresse doit commencer par https:// ou http://, par exemple https://eservices.gov.bf',
            'archive.required_if' => 'Choisissez l\'archive ZIP du projet.',
            'archive.extensions' => 'Le fichier doit être une archive ZIP.',
            'archive.max' => 'L\'archive dépasse '.intdiv(config('charte.conformite.archive_max_ko'), 1024).' Mo.',
        ]);

        $analyse = new Analyse([
            'mode' => $donnees['mode'],
            'cible' => $donnees['mode'] === 'adresse'
                ? $donnees['adresse']
                : mb_substr($request->file('archive')->getClientOriginalName(), 0, 190),
            'version_charte' => Reference::version(),
        ]);
        $analyse->user()->associate($request->user());

        /* Le rendu JavaScript ne vaut que pour un site en ligne, et seulement
           si le serveur a de quoi le faire. */
        if ($request->boolean('rendu') && $donnees['mode'] === 'adresse') {
            if (! CollecteRendu::disponible()) {
                return $this->refuser($request, 'rendu', "Le rendu JavaScript n'est pas encore installé sur ce serveur. Lancez l'analyse sans lui.");
            }
            $analyse->rendu = true;
        }
        $analyse->save();

        if ($donnees['mode'] === 'archive') {
            $dossier = storage_path(AnalyserProjet::DOSSIER);
            is_dir($dossier) || mkdir($dossier, 0770, true);
            $analyse->archive = $analyse->id.'-'.bin2hex(random_bytes(12)).'.zip';
            $request->file('archive')->move($dossier, $analyse->archive);
            $analyse->save();
        }

        Log::info('Analyse demandée', ['analyse' => $analyse->id, 'compte' => $analyse->user_id, 'mode' => $analyse->mode]);
        AnalyserProjet::dispatch($analyse->id);

        /* L'envoi d'une archive avec sa progression passe par le script de la
           page, qui attend une réponse JSON ; sans script, une redirection. */
        return $request->expectsJson()
            ? response()->json(['adresse' => route('conformite.bilan', $analyse)])
            : redirect()->route('conformite.bilan', $analyse);
    }

    private function refuser(Request $request, string $champ, string $message): RedirectResponse|JsonResponse
    {
        return $request->expectsJson()
            ? response()->json(['message' => $message, 'errors' => [$champ => [$message]]], 422)
            : back()->withInput()->withErrors([$champ => $message]);
    }

    public function bilan(Request $request, Analyse $analyse): View
    {
        abort_unless($request->user()->peutLire($analyse), 404);

        $precedente = $analyse->etat === 'terminee' ? $analyse->precedente() : null;
        $comparaison = null;
        if ($precedente) {
            $avant = $precedente->clesEcarts();
            $apres = $analyse->clesEcarts();
            $comparaison = [
                'precedente' => $precedente,
                'corriges' => array_values(array_diff_key($avant, $apres)),
                'nouveaux' => array_values(array_diff_key($apres, $avant)),
            ];
        }

        return view('conformite.bilan', ['analyse' => $analyse, 'comparaison' => $comparaison]);
    }

    public function markdown(Request $request, Analyse $analyse): Response
    {
        abort_unless($request->user()->peutLire($analyse) && $analyse->etat === 'terminee', 404);
        $nom = 'bilan-conformite-'.$analyse->id.'.md';

        return response(BilanMarkdown::ecrire($analyse), 200, [
            'Content-Type' => 'text/markdown; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$nom.'"',
        ]);
    }

    /** Un écart que l'usager estime erroné : il sert à corriger la règle. */
    public function signaler(Request $request, Analyse $analyse): RedirectResponse
    {
        abort_unless($request->user()->peutLire($analyse), 404);
        $donnees = $request->validate([
            'regle' => ['required', 'string', 'max:80'],
            'ou' => ['required', 'string', 'max:255'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
        ], ['commentaire.max' => 'Le commentaire dépasse 1 000 caractères.']);

        /* Le constat est repris du bilan lui-même, et non du formulaire : on
           garde ce que l'outil a réellement affiché. */
        $resultat = collect($analyse->resultats ?? [])->firstWhere('id', $donnees['regle']);
        $ecart = collect($resultat['ecarts'] ?? [])->first(fn ($e) => mb_substr($e['ou'], 0, 255) === $donnees['ou']);
        abort_if($ecart === null, 404);
        if ($analyse->signalements()->where('regle', $donnees['regle'])->where('ou', $donnees['ou'])->exists()) {
            return back()->with('statut', 'Cet écart est déjà signalé : la réponse de l\'équipe apparaîtra ici.');
        }

        $analyse->signalements()->create($donnees + [
            'user_id' => $request->user()->id,
            'extrait' => $ecart['extrait'],
        ]);

        return back()->with('statut', 'Merci. Le signalement est transmis à l\'équipe de la charte, qui revoit la règle et vous répondra dans votre bilan.');
    }

    private function analysesDuJour(Request $request): int
    {
        return $request->user()->analyses()->where('created_at', '>=', now()->startOfDay())->count();
    }
}

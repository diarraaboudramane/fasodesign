<?php

use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\AudienceController;
use App\Http\Controllers\CompteController;
use App\Http\Controllers\ConformiteController;
use App\Http\Middleware\CompteActif;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\FichierController;
use App\Http\Controllers\RechercheController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\VerificationController;
use App\Support\PaquetNpm;
use Illuminate\Support\Facades\Route;

/*
 * Toutes les pages HTML passent par la vérification anti-robot (groupe
 * « humain », bootstrap/app.php) : la case reCAPTCHA est demandée à la
 * première visite, puis mémorisée sur l'appareil.
 */
Route::middleware('humain')->group(function () {

    /* --------------------------------------------------- les pages */

    foreach (config('charte.pages') as $nom => $page) {
        Route::get($page['chemin'], [DocumentationController::class, 'page'])
            ->defaults('nom', $nom)
            ->name($nom);
    }

    /* ------------------------------------------------ la recherche */

    /* Comme le contact, hors de config/charte.php : une recherche demande
       un serveur, et le site exporté n'en a pas. */
    Route::get('recherche', [RechercheController::class, 'chercher'])
        ->middleware('throttle:60,1')
        ->name('recherche');

    /* -------------------------------------------------- le contact */

    /* Le jeton anti-falsification et le retour des erreurs de saisie
       demandent une session. Hors de config/charte.php, la page n'est ni
       dans les menus, ni dans le plan, ni dans l'export. */
    Route::middleware('formulaire')->group(function () {
        Route::get('contact', [ContactController::class, 'afficher'])->name('contact');
        Route::post('contact', [ContactController::class, 'envoyer'])
            ->middleware('throttle:5,1')
            ->name('contact.envoyer');
    });

    /* ---------------------------------- le contrôle de conformité */

    /* Réservé aux comptes : la session est ouverte ici, et nulle part
       ailleurs dans la documentation. */
    Route::middleware('formulaire')->prefix('conformite')->group(function () {
        Route::get('/', [ConformiteController::class, 'presentation'])->name('conformite');

        Route::middleware('guest')->group(function () {
            Route::get('inscription', [CompteController::class, 'inscription'])->name('inscription');
            Route::post('inscription', [CompteController::class, 'inscrire'])
                ->middleware('throttle:5,1')->name('inscription.enregistrer');
            Route::get('connexion', [CompteController::class, 'connexion'])->name('connexion');
            Route::post('connexion', [CompteController::class, 'connecter'])
                ->middleware('throttle:10,1')->name('connexion.ouvrir');
            Route::get('mot-de-passe-oublie', [CompteController::class, 'oubli'])->name('oubli');
            Route::post('mot-de-passe-oublie', [CompteController::class, 'envoyerLien'])
                ->middleware('throttle:3,1')->name('oubli.envoyer');
            Route::get('reinitialisation/{jeton}', [CompteController::class, 'reinitialisation'])->name('password.reset');
            Route::post('reinitialisation', [CompteController::class, 'reinitialiser'])
                ->middleware('throttle:5,1')->name('reinitialisation.enregistrer');
        });

        Route::middleware('auth')->group(function () {
            Route::post('deconnexion', [CompteController::class, 'deconnecter'])->name('deconnexion');
            Route::get('confirmation', [CompteController::class, 'courriel'])->name('conformite.courriel');
            Route::post('confirmation', [CompteController::class, 'renvoyer'])
                ->middleware('throttle:3,1')->name('conformite.courriel.renvoyer');
            Route::get('confirmation/{id}/{empreinte}', [CompteController::class, 'confirmer'])
                ->middleware('signed')->whereNumber('id')->name('conformite.courriel.confirmer');

            Route::middleware(CompteActif::class)->group(function () {
                Route::get('analyses', [ConformiteController::class, 'espace'])->name('conformite.espace');
                Route::post('analyses', [ConformiteController::class, 'lancer'])
                    ->middleware('throttle:6,1')->name('conformite.lancer');
                Route::get('analyses/{analyse}', [ConformiteController::class, 'bilan'])
                    ->whereNumber('analyse')->name('conformite.bilan');
                Route::get('analyses/{analyse}/bilan.md', [ConformiteController::class, 'markdown'])
                    ->whereNumber('analyse')->name('conformite.markdown');
                Route::post('analyses/{analyse}/signalements', [ConformiteController::class, 'signaler'])
                    ->middleware('throttle:20,1')->whereNumber('analyse')->name('conformite.signaler');

                Route::get('comptes', [AdministrationController::class, 'comptes'])->name('conformite.comptes');
                Route::post('comptes/{compte}', [AdministrationController::class, 'modifier'])
                    ->whereNumber('compte')->name('conformite.comptes.modifier');
                Route::get('signalements', [AdministrationController::class, 'signalements'])->name('conformite.signalements');
                Route::post('signalements/{signalement}', [AdministrationController::class, 'traiter'])
                    ->whereNumber('signalement')->name('conformite.signalements.traiter');
            });
        });
    });
});

/* Les liens écrits avant l'application, depuis un autre site ou un
   favori, portent l'extension .html : ils mènent à la même page, où la
   vérification s'applique. */
Route::get('{nom}.html', [DocumentationController::class, 'ancienneAdresse'])
    ->whereIn('nom', array_keys(config('charte.pages')));

/* ------------------------------------------------------ l'audience */

/* Hors du groupe « humain » : la mise à jour en direct ne doit pas
   prolonger la case anti-robot. Le cookie est seulement lu, pour ne
   compter que les visiteurs vérifiés. */
Route::get('audience', [AudienceController::class, 'chiffres'])
    ->middleware([\Illuminate\Cookie\Middleware\EncryptCookies::class, 'throttle:30,1'])
    ->name('audience');

/* ---------------------------------------------- la vérification */

Route::middleware('formulaire')->group(function () {
    Route::get('verification', [VerificationController::class, 'afficher'])->name('verification');
    Route::post('verification', [VerificationController::class, 'valider'])
        ->middleware('throttle:10,1')
        ->name('verification.valider');
});

/* ------------------------------------------------ à la racine du site */

Route::get('robots.txt', [SiteController::class, 'robots']);
Route::get('sitemap.xml', [SiteController::class, 'plan']);
Route::get('VERSION.txt', [SiteController::class, 'version']);

/* Toute la charte en Markdown. Hors vérification : un développeur la
   télécharge avec curl, un agent la lit par son adresse. */
Route::get('charte.md', [SiteController::class, 'markdown'])->name('markdown');

/* ----------------------------------------------- les fichiers servis */

/* Hors vérification : les services des ministères chargent ces fichiers
   depuis leurs propres pages, où personne ne peut cocher de case. */

/* Les ressources de la charte, telles que le dépôt les contient. */
Route::get('{dossier}/{chemin}', [FichierController::class, 'dossier'])
    ->whereIn('dossier', config('charte.dossiers'))
    ->where('chemin', '.+');

/* Les archives npm : npm install https://<serveur>/npm/govbf-charte-graphique-3.0.0.tgz */
Route::get('npm/{nom}-{version}.tgz', [FichierController::class, 'paquet'])
    ->whereIn('nom', array_keys(PaquetNpm::PAQUETS))
    ->where('version', '\d+\.\d+\.\d+');

/* La diffusion à adresse figée : /3.0.0/css/faso.css. */
Route::get('{version}/{type}/{chemin}', [FichierController::class, 'diffusion'])
    ->where('version', '\d+\.\d+\.\d+')
    ->whereIn('type', config('charte.diffusion'))
    ->where('chemin', '.+');

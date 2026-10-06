<?php

use App\Http\Controllers\AudienceController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\FichierController;
use App\Http\Controllers\RechercheController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\VerificationController;
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

/* ----------------------------------------------- les fichiers servis */

/* Hors vérification : les services des ministères chargent ces fichiers
   depuis leurs propres pages, où personne ne peut cocher de case. */

/* Les ressources de la charte, telles que le dépôt les contient. */
Route::get('{dossier}/{chemin}', [FichierController::class, 'dossier'])
    ->whereIn('dossier', config('charte.dossiers'))
    ->where('chemin', '.+');

/* La diffusion à adresse figée : /2.0.0/css/faso.css. */
Route::get('{version}/{type}/{chemin}', [FichierController::class, 'diffusion'])
    ->where('version', '\d+\.\d+\.\d+')
    ->whereIn('type', config('charte.diffusion'))
    ->where('chemin', '.+');

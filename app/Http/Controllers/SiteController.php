<?php

namespace App\Http\Controllers;

use App\Support\Documentation;
use Illuminate\Http\Response;

/**
 * Les trois fichiers attendus à la racine du domaine.
 */
class SiteController extends Controller
{
    /**
     * Le robots.txt est engendré par outils/robots.js, comme pour le site
     * statique : il n'y en a qu'un, servi tel quel.
     */
    public function robots(): Response
    {
        return response(file_get_contents(base_path('hebergement/robots.txt')))
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }

    /**
     * Les moteurs restent autorisés parce qu'un système de conception
     * que personne ne trouve ne sert personne ; leur donner la liste des
     * pages est la contrepartie de ce choix.
     */
    public function plan(): Response
    {
        $lignes = ['<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];

        foreach (array_keys(Documentation::pages()) as $nom) {
            $modifiee = date('Y-m-d', filemtime(resource_path("views/pages/{$nom}.blade.php")));
            $lignes[] = '  <url>';
            $lignes[] = '    <loc>'.e(route($nom)).'</loc>';
            $lignes[] = "    <lastmod>{$modifiee}</lastmod>";
            $lignes[] = '  </url>';
        }

        $lignes[] = '</urlset>';

        return response(implode("\n", $lignes)."\n")
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function version(): Response
    {
        $version = Documentation::version();
        $adresses = array_map(fn ($f) => "  /{$version}/{$f}\n", [
            'css/tokens.css', 'css/faso.css', 'css/icones.css', 'js/faso.js', 'js/faso-amorce.js',
        ]);

        return response(
            "Charte graphique de l'administration burkinabè\n".
            "Version {$version}\n\n".
            "Adresses figées, à employer par les services :\n".
            implode('', $adresses)
        )->header('Content-Type', 'text/plain; charset=utf-8');
    }
}

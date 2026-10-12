<?php

namespace App\Http\Controllers;

use App\Support\CharteMarkdown;
use App\Support\Documentation;
use Illuminate\Http\Response;

/**
 * Les fichiers servis à la racine du domaine.
 */
class SiteController extends Controller
{
    /**
     * Le robots.txt est engendré par outils/robots.js, comme pour le site
     * statique : il n'y en a qu'un. Seule la ligne Sitemap suit le domaine
     * qui répond, pour qu'un serveur de test n'envoie pas les moteurs vers
     * le domaine officiel.
     *
     * Sur un domaine non indexable (CHARTE_INDEXABLE=false), le groupe
     * « * » refuse tout et le plan disparaît : un serveur de test ne doit
     * pas concurrencer le site officiel dans les résultats de recherche.
     * Les groupes nommés restent, et avec eux /charte.md, ouvert aux
     * assistants : un groupe nommé l'emporte sur « * ».
     */
    public function robots(): Response
    {
        $genere = file_get_contents(base_path('hebergement/robots.txt'));

        if (! config('charte.indexable')) {
            $texte = preg_replace('/(?:^#.*\n)+\n^User-agent: \*$[\s\S]*/m', '', $genere)
                ."# Serveur de test : rien n'est à indexer ici.\n"
                ."# Le site officiel est servi sur un autre domaine.\n\n"
                ."User-agent: *\nDisallow: /\n";
        } else {
            $texte = preg_replace('/^Sitemap: .*$/m', 'Sitemap: '.url('sitemap.xml'), $genere);
        }

        return response($texte)->header('Content-Type', 'text/plain; charset=utf-8');
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

    /**
     * Toute la charte en Markdown, pour les développeurs et leurs agents.
     * Servie en ligne (inline) : un outil la lit par son adresse, un
     * lien « download » de la documentation la fait enregistrer.
     */
    public function markdown(): Response
    {
        return response(CharteMarkdown::document())
            ->header('Content-Type', 'text/markdown; charset=utf-8')
            ->header('Content-Disposition', 'inline; filename="'.CharteMarkdown::nomFichier().'"')
            ->header('Cache-Control', 'public, max-age=3600');
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

<?php

namespace App\Conformite;

use App\Support\Documentation;

/**
 * Ce que l'outil sait de la charte, lu dans le dépôt lui-même : les
 * classes et les jetons qu'elle définit, les empreintes de ses fichiers
 * publiés. Une évolution de la charte met ainsi l'outil à jour sans
 * rien ressaisir.
 */
final class Reference
{
    private static ?array $classes = null;

    private static ?array $empreintes = null;

    public static function version(): string
    {
        return Documentation::version();
    }

    /** Les classes fs-… que la charte définit. @return array<string, true> */
    public static function classes(): array
    {
        if (self::$classes === null) {
            $css = file_get_contents(base_path('assets/css/faso.css')).file_get_contents(base_path('assets/css/icones.css'));
            preg_match_all('/\.(fs-[a-z0-9_-]+)/i', $css, $m);
            self::$classes = array_fill_keys(array_map('strtolower', $m[1]), true);
        }

        return self::$classes;
    }

    /**
     * Empreinte sha1 de faso.css et tokens.css pour chaque version publiée,
     * courante et gelées : un fichier qui ne correspond à aucune a été modifié.
     *
     * @return array<string, string> empreinte => « faso.css 3.0.0 »
     */
    public static function empreintes(): array
    {
        if (self::$empreintes === null) {
            self::$empreintes = [];
            $racines = [self::version() => base_path()];
            foreach (glob(base_path('archives/*'), GLOB_ONLYDIR) as $dossier) {
                if (preg_match('/^\d+\.\d+\.\d+$/', basename($dossier))) {
                    $racines[basename($dossier)] = $dossier;
                }
            }
            foreach ($racines as $version => $racine) {
                foreach (['faso.css', 'tokens.css', 'icones.css'] as $nom) {
                    $fichier = "{$racine}/assets/css/{$nom}";
                    if (is_file($fichier)) {
                        /* Les fins de ligne peuvent changer en route (Git, éditeur) : elles ne comptent pas. */
                        self::$empreintes[self::empreinte(file_get_contents($fichier))] = "{$nom} {$version}";
                    }
                }
            }
        }

        return self::$empreintes;
    }

    /** La version d'un fichier publié de la charte, reconnu à son empreinte. */
    public static function versionDe(string $contenu): ?string
    {
        $libelle = self::empreintes()[self::empreinte($contenu)] ?? null;

        return $libelle === null ? null : substr($libelle, strrpos($libelle, ' ') + 1);
    }

    public static function empreinte(string $contenu): string
    {
        return sha1(str_replace("\r\n", "\n", $contenu));
    }

    /** Les teintes du drapeau d'avant la 3.0, à remplacer. @return array<string, string> */
    public static function anciennesTeintes(): array
    {
        return ['#e30613' => 'rouge', '#00843b' => 'vert', '#ffed00' => 'jaune'];
    }

    /** Hôtes qui servent la charte : ses fichiers ne sont pas jugés comme ceux du projet. @return list<string> */
    public static function hotesCharte(): array
    {
        return array_values(array_unique(array_filter([
            parse_url((string) config('charte.serveur'), PHP_URL_HOST),
            parse_url((string) config('app.url'), PHP_URL_HOST),
            'chartegraphique.gov.bf',
        ])));
    }

    /**
     * L'adresse de la section de la charte qui explique une règle.
     * « fondations#couleurs » → https://<serveur>/fondations#couleurs
     */
    public static function adresse(string $lien, bool $absolue = false): string
    {
        [$page, $ancre] = array_pad(explode('#', $lien, 2), 2, null);
        $chemin = config("charte.pages.{$page}.chemin", '/');
        $base = $absolue ? rtrim((string) config('charte.serveur'), '/').$chemin : page($page);

        return $base.($ancre ? '#'.$ancre : '');
    }

    /** Libellés de lien ou de bouton que la charte proscrit. @return list<string> */
    public static function libellesProscrits(): array
    {
        return ['cliquez ici', 'cliquer ici', 'ici', 'valider', 'oui', 'continuer', 'lire la suite', 'en savoir plus'];
    }
}

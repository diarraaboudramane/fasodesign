<?php

namespace App\Support;

/**
 * Les archives npm de la charte, servies par l'application elle-même :
 *
 *   npm install https://<serveur>/npm/govbf-charte-graphique-3.0.0.tgz
 *
 * Tant que l'espace de noms @govbf n'est sur aucun registre, c'est ainsi
 * que les projets installent la charte. L'archive est construite depuis
 * le dépôt, avec les règles de « npm pack » : le package.json, puis ce
 * que déclare son champ « files ». Rien n'est recopié dans public/.
 *
 * L'archive est déterministe (ordre des fichiers, dates et droits
 * fixes) : la même source donne toujours les mêmes octets, donc la même
 * empreinte dans le package-lock.json des projets.
 */
final class PaquetNpm
{
    /** Nom de l'archive, sans version => dossier du package.json. */
    public const PAQUETS = [
        'govbf-charte-graphique' => '',
        'govbf-create-charte-graphique' => 'amorce',
    ];

    /** La date que npm pose sur chaque entrée : 26 octobre 1985, 08:15 UTC. */
    private const DATE = 499162500;

    /** Adresse publique de l'archive, à la version courante. */
    public static function adresse(string $nom): string
    {
        return config('charte.serveur').'/npm/'.$nom.'-'.Documentation::version().'.tgz';
    }

    /**
     * Chemin de l'archive construite, ou null si ce nom n'est pas servi.
     * Elle est gardée dans storage/ sous l'empreinte de son contenu : une
     * source modifiée donne un autre fichier, jamais une archive périmée.
     */
    public static function archive(string $nom, ?string $depot = null): ?string
    {
        if (! array_key_exists($nom, self::PAQUETS)) {
            return null;
        }

        /* $depot : la racine d'une version gelée (archives/<version>/),
           ou le dépôt lui-même pour la version courante. */
        $racine = rtrim($depot ?? base_path(), '/\\').'/'.self::PAQUETS[$nom];
        $manifeste = json_decode(file_get_contents($racine.'/package.json'), true, flags: JSON_THROW_ON_ERROR);
        $tar = self::tar($racine, self::fichiers($racine, $manifeste), $manifeste['bin'] ?? []);

        $dossier = storage_path('framework/npm');
        $cible = $dossier.'/'.$nom.'-'.substr(sha1($tar), 0, 16).'.tgz';

        if (! is_file($cible)) {
            is_dir($dossier) || mkdir($dossier, 0775, true);
            $provisoire = $cible.'.'.bin2hex(random_bytes(4));
            file_put_contents($provisoire, gzencode($tar, 9));
            rename($provisoire, $cible);
        }

        return $cible;
    }

    /**
     * Les fichiers que « npm pack » emporte, relatifs à la racine du
     * paquet et triés.
     *
     * @param  array<string, mixed>  $manifeste
     * @return list<string>
     */
    public static function fichiers(string $racine, array $manifeste): array
    {
        $liste = ['package.json'];

        foreach ($manifeste['files'] ?? [] as $entree) {
            $chemin = $racine.'/'.$entree;

            if (is_file($chemin)) {
                $liste[] = $entree;
            } elseif (is_dir($chemin)) {
                $iterateur = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($chemin, \FilesystemIterator::SKIP_DOTS)
                );
                foreach ($iterateur as $fichier) {
                    $relatif = $entree.'/'.str_replace('\\', '/', substr($fichier->getPathname(), strlen($chemin) + 1));
                    $liste[] = $relatif;
                }
            }
        }

        /* npm ne publie jamais ce qui commence par un point. */
        $liste = array_filter($liste, fn ($f) => ! preg_match('#(^|/)\.#', $f));
        $liste = array_values(array_unique(array_diff($liste, ['package.json'])));
        sort($liste, SORT_STRING);

        /* Le manifeste en tête, comme le place npm. */
        return ['package.json', ...$liste];
    }

    /**
     * @param  list<string>  $fichiers
     * @param  array<string, string>  $commandes  le champ « bin » : ces fichiers sont exécutables
     */
    private static function tar(string $racine, array $fichiers, array $commandes): string
    {
        $executables = array_map(fn ($c) => preg_replace('#^\./#', '', $c), array_values($commandes));
        $tar = '';

        foreach ($fichiers as $fichier) {
            $contenu = file_get_contents($racine.'/'.$fichier);
            $tar .= self::entete('package/'.$fichier, strlen($contenu), in_array($fichier, $executables, true) ? 0755 : 0644);
            $tar .= $contenu.str_repeat("\0", (512 - strlen($contenu) % 512) % 512);
        }

        /* Deux blocs vides ferment l'archive. */
        return $tar.str_repeat("\0", 1024);
    }

    /** En-tête ustar d'un fichier ordinaire. */
    private static function entete(string $nom, int $taille, int $mode): string
    {
        $prefixe = '';
        if (strlen($nom) > 100) {
            $coupure = strrpos(substr($nom, 0, 156), '/');
            $prefixe = substr($nom, 0, $coupure);
            $nom = substr($nom, $coupure + 1);
        }

        $octal = fn (int $valeur, int $largeur) => str_pad(decoct($valeur), $largeur - 1, '0', STR_PAD_LEFT)."\0";

        $entete = str_pad($nom, 100, "\0")
            .$octal($mode, 8)
            .$octal(0, 8)
            .$octal(0, 8)
            .$octal($taille, 12)
            .$octal(self::DATE, 12)
            .'        '
            .'0'
            .str_repeat("\0", 100)
            ."ustar\0".'00'
            .str_repeat("\0", 32 + 32 + 8 + 8)
            .str_pad($prefixe, 155, "\0")
            .str_repeat("\0", 12);

        $somme = array_sum(array_map('ord', str_split($entete)));

        return substr_replace($entete, str_pad(decoct($somme), 6, '0', STR_PAD_LEFT)."\0 ", 148, 8);
    }
}

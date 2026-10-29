<?php

namespace App\Conformite\Collecte;

use App\Conformite\Corpus;
use App\Conformite\Document;
use App\Conformite\LecteurZip;
use App\Conformite\Reference;
use Throwable;

/**
 * Lit le code source d'un projet dans son archive ZIP, en mémoire. Rien
 * n'est extrait sur le disque, rien n'est exécuté ni installé.
 */
class CollecteArchive
{
    /** Dossiers de dépendances, de compilation ou d'outils : rien à juger. */
    private const DOSSIERS_IGNORES = [
        'node_modules', 'vendor', '.git', 'dist', 'build', '.next', '.nuxt', 'out',
        'coverage', 'storage', '.angular', '.cache', 'bower_components',
    ];

    public function collecter(string $octets, string $nom): Corpus
    {
        $lecteur = new LecteurZip($octets);
        $corpus = new Corpus('archive', $nom);

        foreach ($lecteur->fichiers() as $fichier) {
            $chemin = $fichier['nom'];
            $segments = explode('/', $chemin);

            if (array_intersect(array_slice($segments, 0, -1), self::DOSSIERS_IGNORES)) {
                $corpus->ignorer('fichiers de dépendances ou compilés (node_modules, vendor, dist…)');

                continue;
            }

            $type = $this->type($chemin);
            if ($type === null) {
                $corpus->ignorer('fichiers d\'un autre type (images, polices, données…)');

                continue;
            }
            if ($fichier['taille'] > LecteurZip::FICHIER_MAX) {
                $corpus->ignorer('fichiers de plus de 2 Mo');

                continue;
            }

            try {
                $contenu = $lecteur->lire($chemin);
            } catch (Throwable $e) {
                $corpus->nonAtteints[] = $e->getMessage();

                continue;
            }

            /* Un fichier copié de la charte n'est reconnu comme tel que s'il
               correspond octet pour octet à une version publiée. */
            $version = $type === 'css' ? Reference::versionDe($contenu) : null;
            $corpus->ajouter(new Document($chemin, $type, $contenu, strlen($contenu), $version !== null));
            if ($version !== null && $corpus->versionDeclaree === null) {
                $corpus->versionDeclaree = $version;
                $corpus->versionLue = $chemin;
            }

            if (basename($chemin) === 'package.json') {
                $this->lireVersion($corpus, $chemin, $contenu);
            }
        }

        return $corpus;
    }

    private function type(string $chemin): ?string
    {
        $nom = strtolower(basename($chemin));

        return match (true) {
            $nom === 'package.json' => 'json',
            str_ends_with($nom, '.blade.php'), str_ends_with($nom, '.vue'), str_ends_with($nom, '.twig'),
            str_ends_with($nom, '.svelte') => 'gabarit',
            (bool) preg_match('/\.html?$/', $nom) => 'html',
            (bool) preg_match('/\.(jsx|tsx)$/', $nom) => 'jsx',
            (bool) preg_match('/\.(css|scss|sass|less)$/', $nom) => 'css',
            (bool) preg_match('/\.(js|mjs|ts)$/', $nom) => 'js',
            default => null,
        };
    }

    private function lireVersion(Corpus $corpus, string $chemin, string $contenu): void
    {
        $manifeste = json_decode($contenu, true);
        $declaree = $manifeste['dependencies']['@govbf/charte-graphique']
            ?? $manifeste['devDependencies']['@govbf/charte-graphique'] ?? null;
        if (! is_string($declaree) || $corpus->versionDeclaree !== null) {
            return;
        }

        /* « ^3.0.0 », « 3.0.0 » ou l'adresse de l'archive « …-3.0.0.tgz ». */
        if (preg_match('/(\d+\.\d+\.\d+)/', $declaree, $m)) {
            $corpus->versionDeclaree = $m[1];
            $corpus->versionLue = $chemin;
        }
    }
}

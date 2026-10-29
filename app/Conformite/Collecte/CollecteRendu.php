<?php

namespace App\Conformite\Collecte;

use App\Conformite\Corpus;
use App\Conformite\Document;
use App\Conformite\Reference;
use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Parcourt un site rendu par un navigateur : Chrome exécute le JavaScript
 * de chaque page, et l'analyse porte sur la page telle qu'elle s'affiche.
 *
 * Le travail est fait par outils/rendu/rendu.js, qui filtre tout ce que
 * Chrome demande au réseau. Ici, on le lance sous délai, on lit sa sortie
 * et on en fait un corpus comme les autres.
 */
class CollecteRendu
{
    public function __construct(private GardeReseau $garde) {}

    /** Le rendu est-il possible sur ce serveur ? */
    public static function disponible(): bool
    {
        $chrome = (string) config('charte.conformite.rendu.chrome');

        return $chrome !== '' && is_file($chrome)
            && is_dir(base_path('outils/rendu/node_modules/puppeteer-core'));
    }

    public function collecter(string $adresse): Corpus
    {
        if (! self::disponible()) {
            throw new RuntimeException("Le rendu JavaScript n'est pas installé sur ce serveur.");
        }

        /* Même garde que l'analyse sans rendu, avant même de lancer Chrome. */
        $cible = $this->garde->verifier($adresse);

        return $this->depuisSortie($this->executer([
            'adresse' => $adresse,
            'chrome' => config('charte.conformite.rendu.chrome'),
            'pagesMax' => (int) config('charte.conformite.pages_max'),
            'delaiPage' => (int) config('charte.conformite.rendu.delai_page'),
            'dureeMax' => (int) config('charte.conformite.rendu.duree_max'),
            /* Sur un poste de développement, la cible locale déjà admise par
               la garde l'est aussi par le navigateur ; jamais en production. */
            'autoriser' => config('charte.conformite.reseau_local') && ! app()->isProduction()
                ? ["{$cible['hote']}:{$cible['port']}"] : [],
        ]), $adresse);
    }

    /** @return array<string, mixed> la sortie du script */
    protected function executer(array $demande): array
    {
        $processus = new Process(
            [config('charte.conformite.rendu.node'), base_path('outils/rendu/rendu.js')],
            base_path(),
            null,
            json_encode($demande),
            (int) config('charte.conformite.rendu.duree_max') + 60,
        );
        $processus->run();

        $sortie = json_decode($processus->getOutput(), true);
        if (! is_array($sortie)) {
            throw new RuntimeException('Le navigateur n\'a pas pu rendre le site.');
        }
        if (isset($sortie['erreur'])) {
            throw new RuntimeException($sortie['erreur']);
        }

        return $sortie;
    }

    /** @param array<string, mixed> $sortie */
    public function depuisSortie(array $sortie, string $adresse): Corpus
    {
        $corpus = new Corpus('adresse', $adresse);
        $corpus->rendu = true;

        foreach ($sortie['pages'] ?? [] as $page) {
            $corpus->ajouter(new Document($page['adresse'], 'html', $page['html'], strlen($page['html'])));
        }
        if (! $corpus->documents('html')) {
            throw new RuntimeException('Aucune page n\'a pu être rendue.');
        }

        foreach ($sortie['ressources'] ?? [] as $ressource) {
            $corpus->ressources[] = ['adresse' => $ressource['adresse'], 'depuis' => $ressource['depuis']];
            $version = Reference::versionDe($ressource['corps']);
            $charte = $version !== null || in_array(Adresses::hote($ressource['adresse']), Reference::hotesCharte(), true);
            $corpus->ajouter(new Document($ressource['adresse'], $ressource['type'], $ressource['corps'], strlen($ressource['corps']), $charte));

            if ($corpus->versionDeclaree === null) {
                if ($version !== null) {
                    $corpus->versionDeclaree = $version;
                    $corpus->versionLue = $ressource['adresse'];
                } elseif (preg_match('#/(\d+\.\d+\.\d+)/css/(faso|tokens)\.css#', $ressource['adresse'], $m)) {
                    $corpus->versionDeclaree = $m[1];
                    $corpus->versionLue = $ressource['adresse'];
                }
            }
        }

        $corpus->poidsPremierePage = $sortie['poidsPremierePage'] ?? null;
        $corpus->contrastes = $sortie['contrastes'] ?? null;
        $corpus->nonAtteints = array_merge($sortie['nonAtteints'] ?? [], array_map(
            fn ($refus) => 'Requête bloquée par la garde : '.$refus,
            array_slice($sortie['refus'] ?? [], 0, 20)
        ));

        return $corpus;
    }
}

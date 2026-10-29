<?php

namespace App\Conformite\Collecte;

use App\Conformite\Balisage;
use App\Conformite\Corpus;
use App\Conformite\Document;
use App\Conformite\Reference;
use RuntimeException;
use Throwable;

/**
 * Parcourt un site depuis son adresse : la page d'accueil, puis les pages
 * liées du même domaine, et pour chacune les feuilles de style et les
 * scripts qu'elle charge. Le JavaScript n'est pas exécuté.
 */
class CollecteAdresse
{
    public function __construct(private Recuperateur $recuperateur) {}

    public function collecter(string $adresse): Corpus
    {
        $pagesMax = (int) config('charte.conformite.pages_max', 20);
        $ressourcesMax = (int) config('charte.conformite.ressources_max', 40);
        $echeance = microtime(true) + (int) config('charte.conformite.duree_max', 90);

        $corpus = new Corpus('adresse', $adresse);
        $hote = Adresses::hote($adresse);
        $aVisiter = [$adresse];
        $vues = [$adresse => true];
        $ressourcesVues = [];
        $premiere = true;

        while ($aVisiter && count($corpus->documents('html')) < $pagesMax) {
            $page = array_shift($aVisiter);

            if (microtime(true) > $echeance) {
                $corpus->nonAtteints[] = "{$page} : durée d'analyse épuisée";

                continue;
            }

            try {
                $reponse = $this->recuperateur->recuperer($page);
            } catch (Throwable $e) {
                if ($premiere) {
                    throw new RuntimeException($e->getMessage(), 0, $e);
                }
                $corpus->nonAtteints[] = "{$page} : ".$e->getMessage();

                continue;
            }

            if ($reponse->statut >= 400) {
                if ($premiere) {
                    throw new RuntimeException("{$page} a répondu par une erreur {$reponse->statut}.");
                }
                $corpus->nonAtteints[] = "{$page} : erreur {$reponse->statut}";

                continue;
            }
            if (! $reponse->estHtml()) {
                $corpus->ignorer('adresses qui ne sont pas des pages HTML');

                continue;
            }
            /* Une redirection vers un autre domaine sort du périmètre. */
            if (Adresses::hote($reponse->adresse) !== $hote) {
                $corpus->nonAtteints[] = "{$page} : redirige hors du domaine analysé";

                continue;
            }

            $document = new Document($reponse->adresse, 'html', $reponse->corps, strlen($reponse->corps));
            $corpus->ajouter($document);
            $poids = $document->octets;

            /* Feuilles de style et scripts : ceux de la charte sont reconnus
               et mis à part, ceux du projet sont analysés. */
            foreach (Balisage::elements($document, '//link[@href] | //script[@src]') as $element) {
                $estFeuille = $element->tagName === 'link';
                if ($estFeuille && ! preg_match('/stylesheet|preload/i', $element->getAttribute('rel'))) {
                    continue;
                }
                $cible = Adresses::absolue($reponse->adresse, $element->getAttribute($estFeuille ? 'href' : 'src'));
                if ($cible === null) {
                    continue;
                }
                $corpus->ressources[] = ['adresse' => $cible, 'depuis' => $reponse->adresse];
                $this->lireVersion($corpus, $cible);

                if (isset($ressourcesVues[$cible]) || count($ressourcesVues) >= $ressourcesMax) {
                    continue;
                }
                $ressourcesVues[$cible] = true;

                try {
                    $fichier = $this->recuperateur->recuperer($cible);
                } catch (Throwable $e) {
                    $corpus->nonAtteints[] = "{$cible} : ".$e->getMessage();

                    continue;
                }
                if ($fichier->statut >= 400) {
                    $corpus->nonAtteints[] = "{$cible} : erreur {$fichier->statut}";

                    continue;
                }

                $type = $estFeuille || str_contains($fichier->type, 'css') ? 'css' : 'js';
                $version = Reference::versionDe($fichier->corps);
                $charte = $version !== null || in_array(Adresses::hote($cible), Reference::hotesCharte(), true);
                /* Une copie servie par le site dit sa version par son contenu. */
                if ($version !== null && $corpus->versionDeclaree === null) {
                    $corpus->versionDeclaree = $version;
                    $corpus->versionLue = $cible;
                }
                $corpus->ajouter(new Document($cible, $type, $fichier->corps, strlen($fichier->corps), $charte));
                if ($premiere) {
                    $poids += strlen($fichier->corps);
                }
            }

            if ($premiere) {
                $corpus->poidsPremierePage = $poids;
                $premiere = false;
            }

            foreach (Balisage::elements($document, '//a[@href]') as $lien) {
                $suivante = Adresses::absolue($reponse->adresse, $lien->getAttribute('href'));
                if ($suivante === null || Adresses::hote($suivante) !== $hote || isset($vues[$suivante])) {
                    continue;
                }
                if (preg_match('/\.(pdf|jpe?g|png|gif|svg|webp|ico|zip|tgz|gz|docx?|xlsx?|pptx?|odt|ods|csv|mp4|mp3|md|txt|json|xml|woff2?|ttf|css|js)(\?|$)/i', parse_url($suivante, PHP_URL_PATH) ?? '')) {
                    continue;
                }
                $vues[$suivante] = true;
                $aVisiter[] = $suivante;
            }
        }

        foreach ($aVisiter as $restante) {
            $corpus->nonAtteints[] = "{$restante} : au-delà des {$pagesMax} pages analysées";
        }

        return $corpus;
    }

    /** Une adresse figée de la charte dit sa version : /3.0.0/css/faso.css. */
    private function lireVersion(Corpus $corpus, string $adresse): void
    {
        if ($corpus->versionDeclaree === null
            && preg_match('#/(\d+\.\d+\.\d+)/css/(faso|tokens)\.css#', $adresse, $m)) {
            $corpus->versionDeclaree = $m[1];
            $corpus->versionLue = $adresse;
        }
    }
}

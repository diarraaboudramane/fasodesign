<?php

namespace App\Conformite\Regles;

use App\Conformite\Balisage;
use App\Conformite\Corpus;
use App\Conformite\Regle;

/** Un seul titre de premier niveau par page, et aucun niveau sauté. */
final class HierarchieTitres extends Regle
{
    public function id(): string { return 'accessibilite.titres'; }

    public function titre(): string { return 'Hiérarchie des titres'; }

    public function famille(): string { return 'accessibilite'; }

    public function gravite(): string { return 'majeure'; }

    public function lien(): string { return 'accessibilite'; }

    public function correction(): string { return 'Garder un seul <h1> par page et descendre les niveaux un par un, sans en sauter.'; }

    public function verifier(Corpus $corpus): array
    {
        $pages = array_filter($corpus->propres('html', 'gabarit'), fn ($d) => Balisage::estPageComplete($d));
        if (! $pages) {
            return $this->nonVerifiable('Aucune page complète à examiner : dans un gabarit partiel, la hiérarchie dépend de la page qui l\'inclut.');
        }

        $ecarts = [];
        foreach ($pages as $page) {
            $titres = Balisage::elements($page, '//h1 | //h2 | //h3 | //h4 | //h5 | //h6');
            $premiers = array_filter($titres, fn ($t) => $t->tagName === 'h1');
            if (count($premiers) !== 1) {
                $ecarts[] = $this->ecart($page->chemin, count($premiers) === 0 ? 'Aucun <h1>' : count($premiers).' titres <h1>');
            }

            $precedent = 0;
            foreach ($titres as $titre) {
                $niveau = (int) substr($titre->tagName, 1);
                if ($precedent > 0 && $niveau > $precedent + 1) {
                    $ecarts[] = $this->ecart(Balisage::lieu($page, $titre), "<h{$precedent}> suivi de <{$titre->tagName}> : ".Balisage::texte($titre));
                }
                $precedent = $niveau;
            }
        }

        return $this->resultat($ecarts);
    }
}

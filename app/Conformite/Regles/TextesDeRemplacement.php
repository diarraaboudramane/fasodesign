<?php

namespace App\Conformite\Regles;

use App\Conformite\Balisage;
use App\Conformite\Corpus;
use App\Conformite\Regle;

/**
 * Toute image porte un attribut alt. Vide, il signale une image décorative ;
 * absent, le lecteur d'écran lit le nom du fichier.
 */
final class TextesDeRemplacement extends Regle
{
    public function id(): string { return 'accessibilite.alt'; }

    public function titre(): string { return 'Texte de remplacement des images'; }

    public function famille(): string { return 'accessibilite'; }

    public function gravite(): string { return 'bloquante'; }

    public function lien(): string { return 'accessibilite'; }

    public function correction(): string
    {
        return 'Ajouter un attribut alt qui dit ce que l\'image apporte, ou alt="" si elle est décorative.';
    }

    public function verifier(Corpus $corpus): array
    {
        $ecarts = [];

        foreach ($corpus->propres('html', 'gabarit') as $document) {
            foreach (Balisage::elements($document, '//img[not(@alt)]') as $image) {
                $ecarts[] = $this->ecart(Balisage::lieu($document, $image), Balisage::extrait($image));
            }
        }

        foreach ($corpus->propres('jsx') as $document) {
            preg_match_all('/<img\b(?![^>]*\balt\s*=)[^>]*>/i', $document->contenu, $m, PREG_OFFSET_CAPTURE);
            foreach ($m[0] as [$balise, $position]) {
                $ecarts[] = $this->ecart($document->lieu($position), $balise);
            }
        }

        return $this->resultat($ecarts);
    }
}

<?php

namespace App\Conformite\Regles;

use App\Conformite\Balisage;
use App\Conformite\Corpus;
use App\Conformite\Regle;
use DOMElement;

/** Chaque champ de formulaire a un nom accessible : une étiquette, ou à défaut aria-label. */
final class EtiquettesDesChamps extends Regle
{
    public function id(): string { return 'accessibilite.etiquettes'; }

    public function titre(): string { return 'Étiquette de chaque champ de formulaire'; }

    public function famille(): string { return 'accessibilite'; }

    public function gravite(): string { return 'bloquante'; }

    public function lien(): string { return 'composants#formulaires'; }

    public function correction(): string
    {
        return 'Associer un <label for="…"> au champ ; aria-label seulement quand aucun libellé visible n\'est possible.';
    }

    public function verifier(Corpus $corpus): array
    {
        $ecarts = [];

        foreach ($corpus->propres('html', 'gabarit') as $document) {
            $pour = [];
            foreach (Balisage::elements($document, '//label[@for]') as $label) {
                $pour[$label->getAttribute('for')] = true;
            }

            $champs = Balisage::elements($document, '//input[not(@type="hidden") and not(@type="submit") and not(@type="button") and not(@type="reset") and not(@type="image")] | //select | //textarea');
            foreach ($champs as $champ) {
                if (! $this->nomme($champ, $pour)) {
                    $ecarts[] = $this->ecart(Balisage::lieu($document, $champ), Balisage::extrait($champ));
                }
            }
        }

        return $this->resultat($ecarts);
    }

    /** @param array<string, true> $pour */
    private function nomme(DOMElement $champ, array $pour): bool
    {
        if (trim($champ->getAttribute('aria-label')) !== '' || trim($champ->getAttribute('aria-labelledby')) !== ''
            || trim($champ->getAttribute('title')) !== '') {
            return true;
        }
        $id = $champ->getAttribute('id');
        if ($id !== '' && (isset($pour[$id]) || str_contains($id, '{'))) {
            return true;
        }
        for ($parent = $champ->parentNode; $parent instanceof DOMElement; $parent = $parent->parentNode) {
            if ($parent->tagName === 'label') {
                return true;
            }
        }

        return false;
    }
}

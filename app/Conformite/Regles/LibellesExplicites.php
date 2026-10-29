<?php

namespace App\Conformite\Regles;

use App\Conformite\Balisage;
use App\Conformite\Corpus;
use App\Conformite\Reference;
use App\Conformite\Regle;
use DOMElement;

/**
 * Un lien ou un bouton nomme ce qu'il fait. « Continuer » et « Arrêter »
 * restent admis dans une boîte de confirmation, comme le prévoit la charte.
 */
final class LibellesExplicites extends Regle
{
    public function id(): string { return 'redaction.libelles'; }

    public function titre(): string { return 'Libellés de liens et de boutons explicites'; }

    public function famille(): string { return 'redaction'; }

    public function gravite(): string { return 'mineure'; }

    public function lien(): string { return 'accessibilite#redaction'; }

    public function correction(): string { return 'Nommer la destination du lien ou l\'action du bouton : « Télécharger le récépissé », « Passer au paiement ».'; }

    public function verifier(Corpus $corpus): array
    {
        $proscrits = array_flip(Reference::libellesProscrits());
        $ecarts = [];

        foreach ($corpus->propres('html', 'gabarit') as $document) {
            foreach (Balisage::elements($document, '//a | //button') as $element) {
                $texte = mb_strtolower(trim(Balisage::texte($element), " \t\n.!…»«"));
                if (! isset($proscrits[$texte]) || $this->dansUneConfirmation($element, $texte)) {
                    continue;
                }
                $ecarts[] = $this->ecart(Balisage::lieu($document, $element), '« '.Balisage::texte($element).' »');
            }
        }

        foreach ($corpus->propres('jsx') as $document) {
            preg_match_all('/<(a|button)\b[^>]*>\s*([^<{]{1,40}?)\s*<\/\1>/i', $document->contenu, $m, PREG_OFFSET_CAPTURE);
            foreach ($m[2] as $i => [$texte, $position]) {
                if (isset($proscrits[mb_strtolower(trim($texte, " .!…"))])) {
                    $ecarts[] = $this->ecart($document->lieu($position), '« '.trim($texte).' »');
                }
            }
        }

        return $this->resultat($ecarts);
    }

    private function dansUneConfirmation(DOMElement $element, string $texte): bool
    {
        if ($texte !== 'continuer') {
            return false;
        }
        for ($parent = $element->parentNode; $parent instanceof DOMElement; $parent = $parent->parentNode) {
            if ($parent->tagName === 'dialog' || str_contains(' '.$parent->getAttribute('class').' ', ' fs-modale ')
                || $parent->getAttribute('role') === 'alertdialog') {
                return true;
            }
        }

        return false;
    }
}

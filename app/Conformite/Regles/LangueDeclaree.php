<?php

namespace App\Conformite\Regles;

use App\Conformite\Balisage;
use App\Conformite\Corpus;
use App\Conformite\Regle;

final class LangueDeclaree extends Regle
{
    public function id(): string { return 'accessibilite.langue'; }

    public function titre(): string { return 'Langue de la page déclarée'; }

    public function famille(): string { return 'accessibilite'; }

    public function gravite(): string { return 'bloquante'; }

    public function lien(): string { return 'accessibilite'; }

    public function correction(): string { return 'Ajouter lang="fr" sur l\'élément <html>.'; }

    public function verifier(Corpus $corpus): array
    {
        $ecarts = [];
        $racines = 0;

        foreach ($corpus->balisages() as $document) {
            if (! preg_match('/<html([\s>])/i', $document->contenu)) {
                continue;
            }
            $racines++;
            $html = Balisage::elements($document, '//html')[0] ?? null;
            $langue = $html?->getAttribute('lang') ?? '';
            /* Un gabarit qui calcule la langue ({{ … }}) la déclare. */
            if (trim($langue) === '' && ! preg_match('/<html[^>]*\blang\s*=/i', $document->contenu)) {
                $ecarts[] = $this->ecart($document->chemin, '<html> sans attribut lang');
            }
        }

        if ($racines === 0) {
            return $this->nonVerifiable('Aucun élément <html> dans ce qui a été soumis.');
        }

        return $this->resultat($ecarts);
    }
}

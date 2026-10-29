<?php

namespace App\Conformite\Regles;

use App\Conformite\Corpus;
use App\Conformite\Reference;
use App\Conformite\Regle;

/** Les couleurs du drapeau ont changé en 3.0 : les anciennes valeurs trahissent une copie. */
final class TeintesAnciennes extends Regle
{
    public function id(): string { return 'couleurs.anciennes'; }

    public function titre(): string { return 'Couleurs nationales aux valeurs officielles'; }

    public function famille(): string { return 'couleurs'; }

    public function gravite(): string { return 'majeure'; }

    public function lien(): string { return 'fondations#couleurs'; }

    public function correction(): string
    {
        return 'Employer les jetons --rouge-500, --vert-500 et --or-300 (#EF2B2D, #009E49, #FCD116) plutôt que des valeurs recopiées.';
    }

    public function verifier(Corpus $corpus): array
    {
        $ecarts = [];
        $motif = '/'.implode('|', array_map(fn ($h) => preg_quote($h, '/'), array_keys(Reference::anciennesTeintes()))).'/i';

        foreach ($corpus->propres('css', 'html', 'gabarit', 'jsx', 'js') as $document) {
            foreach ($this->zonesDeStyle($document) as [$debut, $texte]) {
                preg_match_all($motif, $texte, $m, PREG_OFFSET_CAPTURE);
                foreach ($m[0] as [$teinte, $position]) {
                    $nom = Reference::anciennesTeintes()[strtolower($teinte)];
                    $ecarts[] = $this->ecart($document->lieu($debut + $position), "{$teinte}, ancien {$nom} du drapeau");
                }
            }
        }

        return $this->resultat($ecarts);
    }

    /**
     * Là où une couleur s'applique vraiment. Dans un balisage, une page qui
     * cite l'ancienne valeur dans son texte ne l'emploie pas : seuls comptent
     * les attributs de style et de dessin, et les blocs <style>.
     *
     * @return list<array{0: int, 1: string}> position de départ, texte
     */
    private function zonesDeStyle(\App\Conformite\Document $document): array
    {
        if (in_array($document->type, ['css', 'js'], true)) {
            return [[0, $document->contenu]];
        }

        preg_match_all('/<style\b[^>]*>.*?<\/style>|\b(?:style|fill|stroke|color|stop-color)\s*=\s*(["\'])[^"\']*\1/is', $document->contenu, $m, PREG_OFFSET_CAPTURE);

        return array_map(fn ($z) => [$z[1], $z[0]], $m[0]);
    }
}

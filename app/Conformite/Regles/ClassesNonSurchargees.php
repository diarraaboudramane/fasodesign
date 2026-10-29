<?php

namespace App\Conformite\Regles;

use App\Conformite\Corpus;
use App\Conformite\Regle;

/** Les adaptations passent par une surcharge de jetons, jamais par les sélecteurs de la charte. */
final class ClassesNonSurchargees extends Regle
{
    public function id(): string { return 'classes.surcharge'; }

    public function titre(): string { return 'Composants de la charte non surchargés'; }

    public function famille(): string { return 'classes'; }

    public function gravite(): string { return 'majeure'; }

    public function lien(): string { return 'index'; }

    public function correction(): string
    {
        return 'Retirer la règle, ou la remplacer par une surcharge de jetons dans la feuille du projet.';
    }

    public function verifier(Corpus $corpus): array
    {
        $ecarts = [];

        foreach ($corpus->propres('css') as $document) {
            $sansCommentaires = preg_replace_callback('#/\*.*?\*/#s', fn ($m) => preg_replace('/[^\n]/', ' ', $m[0]), $document->contenu);
            /* Un sélecteur est ce qui précède une accolade ouvrante. */
            preg_match_all('/([^{};]+)\{/', $sansCommentaires, $m, PREG_OFFSET_CAPTURE);
            foreach ($m[1] as [$selecteur, $position]) {
                if (preg_match('/(^|[\s,>+~(])\.fs-[a-z0-9_-]+/i', $selecteur) && ! str_starts_with(trim($selecteur), '@')) {
                    $ecarts[] = $this->ecart($document->lieu($position + strlen($selecteur) - strlen(ltrim($selecteur))), trim($selecteur));
                }
            }
        }

        return $this->resultat($ecarts);
    }
}

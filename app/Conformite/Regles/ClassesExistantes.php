<?php

namespace App\Conformite\Regles;

use App\Conformite\Corpus;
use App\Conformite\Reference;
use App\Conformite\Regle;

/** Une classe fs-… que la charte ne définit pas n'a aucun effet, ou un effet maison. */
final class ClassesExistantes extends Regle
{
    public function id(): string { return 'classes.existantes'; }

    public function titre(): string { return 'Classes fs- définies par la charte'; }

    public function famille(): string { return 'classes'; }

    public function gravite(): string { return 'majeure'; }

    public function lien(): string { return 'composants'; }

    public function correction(): string
    {
        return 'Employer une classe documentée, ou donner aux classes propres au projet un préfixe distinct de fs-.';
    }

    public function verifier(Corpus $corpus): array
    {
        $connues = Reference::classes();
        $vues = [];
        $ecarts = [];

        foreach ($corpus->propres('html', 'gabarit', 'jsx') as $document) {
            foreach ($this->classes($document) as [$classe, $position]) {
                /* « fs-btn--{{ $variante }} » : la fin est calculée, on ne juge pas. */
                if (str_ends_with($classe, '-') || isset($connues[$classe]) || isset($vues[$classe])) {
                    continue;
                }
                $vues[$classe] = true;
                $ecarts[] = $this->ecart($document->lieu($position), ".{$classe} n'existe pas dans la charte ".Reference::version());
            }
        }

        return $this->resultat($ecarts);
    }
}

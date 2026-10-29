<?php

namespace App\Conformite\Regles;

use App\Conformite\Collecte\Adresses;
use App\Conformite\Corpus;
use App\Conformite\Regle;

/** Le domaine visible est l'un des repères qui permettent de reconnaître un service authentique. */
final class DomaineOfficiel extends Regle
{
    public function id(): string { return 'confiance.domaine'; }

    public function titre(): string { return 'Domaine en .gov.bf'; }

    public function famille(): string { return 'confiance'; }

    public function gravite(): string { return 'majeure'; }

    public function lien(): string { return 'composants-metier#confiance'; }

    public function correction(): string { return 'Servir le service depuis un sous-domaine de gov.bf.'; }

    public function modes(): array { return ['adresse']; }

    public function verifier(Corpus $corpus): array
    {
        $hote = Adresses::hote($corpus->cible);

        return preg_match('/(^|\.)gov\.bf$/', $hote) ? $this->respectee()
            : $this->resultat([$this->ecart($corpus->cible, "Le domaine {$hote} n'est pas un domaine de l'administration")]);
    }
}

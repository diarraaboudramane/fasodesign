<?php

namespace App\Conformite\Regles;

use App\Conformite\Corpus;
use App\Conformite\Reference;
use App\Conformite\Regle;

final class VersionAJour extends Regle
{
    public function id(): string { return 'integration.version'; }

    public function titre(): string { return 'Version de la charte à jour'; }

    public function famille(): string { return 'integration'; }

    public function gravite(): string { return 'majeure'; }

    public function lien(): string { return 'gouvernance#journal'; }

    public function correction(): string
    {
        return 'Passer à la version '.Reference::version().' en suivant la note de migration du journal des versions.';
    }

    public function verifier(Corpus $corpus): array
    {
        if ($corpus->versionDeclaree === null) {
            return $this->nonVerifiable($corpus->mode === 'adresse'
                ? 'Les feuilles de la charte ne sont pas chargées depuis une adresse figée (/x.y.z/css/…) : la version ne se lit pas.'
                : 'Le package.json ne déclare pas @govbf/charte-graphique.');
        }

        $courante = Reference::version();
        if (version_compare($corpus->versionDeclaree, $courante, '>=')) {
            return $this->respectee("Version {$corpus->versionDeclaree}, lue dans {$corpus->versionLue}.");
        }

        return $this->resultat([
            $this->ecart((string) $corpus->versionLue, "Version {$corpus->versionDeclaree}, la version courante est {$courante}"),
        ]);
    }
}

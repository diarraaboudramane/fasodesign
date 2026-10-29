<?php

namespace App\Conformite\Regles;

use App\Conformite\Corpus;
use App\Conformite\Reference;
use App\Conformite\Regle;

/** Un projet ne modifie jamais les feuilles de la charte : il surcharge des jetons. */
final class FichiersNonModifies extends Regle
{
    public function id(): string { return 'integration.non-modifiee'; }

    public function titre(): string { return 'Fichiers de la charte non modifiés'; }

    public function famille(): string { return 'integration'; }

    public function gravite(): string { return 'bloquante'; }

    public function lien(): string { return 'index'; }

    public function correction(): string
    {
        return 'Reprendre le fichier publié tel quel, et placer les adaptations dans une feuille propre au projet.';
    }

    public function verifier(Corpus $corpus): array
    {
        $ecarts = [];
        $copies = 0;

        foreach ($corpus->documents('css') as $document) {
            $nom = strtolower(basename(parse_url($document->chemin, PHP_URL_PATH) ?: $document->chemin));
            if (! in_array($nom, ['faso.css', 'tokens.css', 'icones.css'], true)) {
                continue;
            }
            $copies++;
            if (! isset(Reference::empreintes()[Reference::empreinte($document->contenu)])) {
                $ecarts[] = $this->ecart($document->chemin, "{$nom} ne correspond à aucune version publiée de la charte");
            }
        }

        if ($copies === 0) {
            return $this->nonVerifiable("Aucune copie de faso.css, tokens.css ou icones.css n'a été trouvée à comparer.");
        }

        return $this->resultat($ecarts);
    }
}

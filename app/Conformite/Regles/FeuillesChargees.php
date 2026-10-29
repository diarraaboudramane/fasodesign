<?php

namespace App\Conformite\Regles;

use App\Conformite\Balisage;
use App\Conformite\Corpus;
use App\Conformite\Regle;

/** Les trois feuilles de la charte, et tokens.css avant faso.css. */
final class FeuillesChargees extends Regle
{
    public function id(): string { return 'integration.feuilles'; }

    public function titre(): string { return "Feuilles de la charte chargées dans l'ordre"; }

    public function famille(): string { return 'integration'; }

    public function gravite(): string { return 'bloquante'; }

    public function lien(): string { return 'integration#installation'; }

    public function correction(): string { return 'Charger tokens.css, puis faso.css, puis icones.css, sans les modifier.'; }

    public function verifier(Corpus $corpus): array
    {
        $ecarts = [];
        $trouvees = ['tokens.css' => false, 'faso.css' => false, 'icones.css' => false];

        foreach ($corpus->documents() as $document) {
            foreach (array_keys($trouvees) as $nom) {
                if (preg_match('#[/"\'@]'.preg_quote($nom, '#').'\b#', $document->contenu)
                    || str_ends_with(strtolower(parse_url($document->chemin, PHP_URL_PATH) ?: $document->chemin), '/'.$nom)) {
                    $trouvees[$nom] = true;
                }
            }

            if ($document->type === 'html') {
                $ordre = array_map(fn ($l) => $l->getAttribute('href'), Balisage::elements($document, '//link[@href]'));
                $jetons = $this->position($ordre, 'tokens.css');
                $faso = $this->position($ordre, 'faso.css');
                if ($jetons !== null && $faso !== null && $faso < $jetons) {
                    $ecarts[] = $this->ecart($document->chemin, 'faso.css est chargée avant tokens.css');
                }
            }
        }

        foreach ($trouvees as $nom => $present) {
            if (! $present) {
                $ecarts[] = $this->ecart($corpus->cible, "{$nom} n'est chargée nulle part");
            }
        }

        return $this->resultat($ecarts);
    }

    /** @param list<string> $adresses */
    private function position(array $adresses, string $nom): ?int
    {
        foreach ($adresses as $i => $adresse) {
            if (str_contains($adresse, $nom)) {
                return $i;
            }
        }

        return null;
    }
}

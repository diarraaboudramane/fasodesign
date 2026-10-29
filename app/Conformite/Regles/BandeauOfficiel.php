<?php

namespace App\Conformite\Regles;

use App\Conformite\Balisage;
use App\Conformite\Corpus;
use App\Conformite\Regle;

final class BandeauOfficiel extends Regle
{
    public function id(): string { return 'identite.bandeau'; }

    public function titre(): string { return "Bandeau d'identification officielle"; }

    public function famille(): string { return 'identite'; }

    public function gravite(): string { return 'bloquante'; }

    public function lien(): string { return 'composants-metier#confiance'; }

    public function correction(): string { return 'Reprendre le bandeau .fs-officiel en tête de chaque page, sans en changer le texte.'; }

    public function verifier(Corpus $corpus): array
    {
        $pages = array_filter($corpus->balisages(), fn ($d) => Balisage::estPageComplete($d));
        if (! $pages) {
            return $this->nonVerifiable('Aucune page complète (avec <html> ou <body>) à examiner.');
        }

        $sans = [];
        foreach ($pages as $page) {
            if (! Balisage::elements($page, '//*[contains(concat(" ", normalize-space(@class), " "), " fs-officiel ")]')) {
                $sans[] = $this->ecart($page->chemin, 'Aucun bandeau .fs-officiel');
            }
        }

        /* Dans une archive, il suffit qu'une mise en page commune le porte. */
        if ($corpus->mode === 'archive' && count($sans) < count($pages)) {
            return $this->respectee();
        }

        return $this->resultat($sans);
    }
}

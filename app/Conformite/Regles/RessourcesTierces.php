<?php

namespace App\Conformite\Regles;

use App\Conformite\Collecte\Adresses;
use App\Conformite\Corpus;
use App\Conformite\Reference;
use App\Conformite\Regle;

/**
 * Polices, feuilles et scripts sont servis par le domaine du service : un
 * tiers ajoute une connexion, une dépendance hors de contrôle, et voit
 * passer chaque visite.
 */
final class RessourcesTierces extends Regle
{
    public function id(): string { return 'confiance.tiers'; }

    public function titre(): string { return 'Aucune ressource chargée chez un tiers'; }

    public function famille(): string { return 'confiance'; }

    public function gravite(): string { return 'majeure'; }

    public function lien(): string { return 'fondations#typographie'; }

    public function correction(): string { return 'Servir le fichier depuis le domaine du service, ou depuis le serveur de la charte.'; }

    public function verifier(Corpus $corpus): array
    {
        $autorises = Reference::hotesCharte();
        if ($corpus->mode === 'adresse') {
            $autorises[] = Adresses::hote($corpus->cible);
        }

        $adresses = $corpus->ressources;
        foreach ($corpus->propres('html', 'gabarit', 'jsx', 'css') as $document) {
            preg_match_all('/(?:<link\b[^>]*\bhref|<script\b[^>]*\bsrc)\s*=\s*["\'](https?:)?\/\/([^"\'\/]+)[^"\']*["\']|@import\s+(?:url\()?["\']?(?:https?:)?\/\/([^"\'\/)]+)/i', $document->contenu, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
            foreach ($m as $correspondance) {
                $hote = $correspondance[2][0] ?? '';
                $hote = $hote !== '' ? $hote : ($correspondance[3][0] ?? '');
                $adresses[] = ['adresse' => 'https://'.$hote.'/', 'depuis' => $document->lieu($correspondance[0][1])];
            }
        }

        $ecarts = [];
        $vus = [];
        foreach ($adresses as $ressource) {
            $hote = Adresses::hote($ressource['adresse']);
            if ($hote === '' || in_array($hote, $autorises, true) || isset($vus[$hote])) {
                continue;
            }
            $vus[$hote] = true;
            $ecarts[] = $this->ecart($ressource['depuis'], "Ressource chargée depuis {$hote}");
        }

        return $this->resultat($ecarts);
    }
}

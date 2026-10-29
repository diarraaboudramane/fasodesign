<?php

namespace App\Conformite\Regles;

use App\Conformite\Corpus;
use App\Conformite\Regle;

/**
 * Le budget de la charte : 250 Ko visés, 500 Ko au plus pour une page
 * complète. L'outil compte la page d'accueil, ses feuilles et ses scripts,
 * mais pas ses images ni ses polices : le chiffre est un minimum.
 */
final class BudgetDePoids extends Regle
{
    public const OBJECTIF = 250 * 1024;

    public const PLAFOND = 500 * 1024;

    public function id(): string { return 'performance.poids'; }

    public function titre(): string { return 'Poids de la page d\'accueil'; }

    public function famille(): string { return 'performance'; }

    public function gravite(): string { return 'majeure'; }

    public function lien(): string { return 'accessibilite#budget'; }

    public function correction(): string { return 'Alléger les scripts et les feuilles chargés par la page, différer ce qui n\'est pas indispensable à l\'affichage.'; }

    public function modes(): array { return ['adresse']; }

    public function verifier(Corpus $corpus): array
    {
        if ($corpus->poidsPremierePage === null) {
            return $this->nonVerifiable('La page d\'accueil n\'a pas pu être mesurée.');
        }

        $ko = (int) round($corpus->poidsPremierePage / 1024);
        $note = $corpus->rendu
            ? "{$ko} Ko réellement chargés par la page, images et polices comprises."
            : "{$ko} Ko pour le HTML, les feuilles et les scripts, images et polices non comprises.";

        return $corpus->poidsPremierePage > self::PLAFOND
            ? $this->resultat([$this->ecart($corpus->cible, "{$ko} Ko, au-delà du plafond de 500 Ko")], $note)
            : $this->respectee($note.($corpus->poidsPremierePage > self::OBJECTIF ? ' Au-dessus de l\'objectif de 250 Ko.' : ''));
    }
}

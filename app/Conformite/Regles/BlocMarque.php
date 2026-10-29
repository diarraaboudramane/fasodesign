<?php

namespace App\Conformite\Regles;

use App\Conformite\Balisage;
use App\Conformite\Corpus;
use App\Conformite\Regle;

/** Le bloc-marque : un lien vers l'accueil qui porte l'emblème. */
final class BlocMarque extends Regle
{
    private const MARQUE = '//*[contains(concat(" ", normalize-space(@class), " "), " fs-marque ")]';

    public function id(): string { return 'identite.bloc-marque'; }

    public function titre(): string { return 'Bloc-marque présent et cliquable'; }

    public function famille(): string { return 'identite'; }

    public function gravite(): string { return 'bloquante'; }

    public function lien(): string { return 'fondations#marque'; }

    public function correction(): string
    {
        return "Placer en haut à gauche un lien .fs-marque vers l'accueil, avec les armoiries et la dénomination de l'État.";
    }

    public function verifier(Corpus $corpus): array
    {
        $pages = array_filter($corpus->balisages(), fn ($d) => Balisage::estPageComplete($d));
        if (! $pages) {
            return $this->nonVerifiable('Aucune page complète (avec <html> ou <body>) à examiner.');
        }

        $ecarts = [];
        $presente = false;

        foreach ($pages as $page) {
            $marques = Balisage::elements($page, self::MARQUE);
            if (! $marques) {
                if ($corpus->mode === 'adresse') {
                    $ecarts[] = $this->ecart($page->chemin, 'Aucun élément .fs-marque');
                }

                continue;
            }

            $presente = true;
            $marque = $marques[0];
            if ($marque->tagName !== 'a' || trim($marque->getAttribute('href')) === '') {
                $ecarts[] = $this->ecart(Balisage::lieu($page, $marque), "Le bloc-marque n'est pas un lien vers l'accueil");
            }
            if ($marque->getElementsByTagName('img')->length === 0 && $marque->getElementsByTagName('svg')->length === 0) {
                $ecarts[] = $this->ecart(Balisage::lieu($page, $marque), "Le bloc-marque ne contient pas l'emblème");
            }
        }

        if (! $presente && $corpus->mode === 'archive') {
            $ecarts[] = $this->ecart($corpus->cible, 'Aucune page du projet ne contient de bloc-marque .fs-marque');
        }

        return $this->resultat($ecarts);
    }
}

<?php

namespace App\Conformite\Regles;

use App\Conformite\Corpus;
use App\Conformite\Regle;

/**
 * Une couleur s'emploie par un jeton. Déclarer ses propres jetons reste
 * permis (--ma-couleur: #…) : c'est la valeur posée directement sur une
 * propriété qui est un écart.
 */
final class CouleursEnDur extends Regle
{
    private const COULEUR = '/(?<![\w-])(#[0-9a-f]{3,8}\b|rgba?\([^)]*\)|hsla?\([^)]*\))/i';

    public function id(): string { return 'couleurs.en-dur'; }

    public function titre(): string { return 'Couleurs employées par les jetons'; }

    public function famille(): string { return 'couleurs'; }

    public function gravite(): string { return 'majeure'; }

    public function lien(): string { return 'fondations#couleurs'; }

    public function correction(): string { return 'Remplacer la valeur par le rôle sémantique qui convient, par exemple var(--action) ou var(--texte).'; }

    public function verifier(Corpus $corpus): array
    {
        $ecarts = [];

        foreach ($corpus->propres('css') as $document) {
            foreach (preg_split('/\R/', $document->contenu) as $i => $ligne) {
                $code = preg_replace('#/\*.*?\*/|//.*$#', '', $ligne);
                /* Une déclaration de jeton, ou une variable Sass, n'est pas un emploi. */
                if (preg_match('/^\s*(--[\w-]+|\$[\w-]+)\s*:/', $code) || ! preg_match('/:\s*[^;{]*'.substr(self::COULEUR, 1, -2).'/i', $code)) {
                    continue;
                }
                if (preg_match('/(?:^|[;{\s])(?:color|background(?:-color)?|border(?:-[a-z]+)?|fill|stroke|outline(?:-color)?|box-shadow|text-decoration-color)\s*:[^;]*'.substr(self::COULEUR, 1, -2).'/i', $code, $m)) {
                    $ecarts[] = $this->ecart($document->chemin.', ligne '.($i + 1), trim($ligne));
                }
            }
        }

        /* Les styles écrits dans le balisage comptent aussi. */
        foreach ($corpus->propres('html', 'gabarit', 'jsx') as $document) {
            preg_match_all('/style\s*=\s*["\'][^"\']*(?:color|background)[^"\']*'.substr(self::COULEUR, 1, -2).'[^"\']*["\']/i', $document->contenu, $m, PREG_OFFSET_CAPTURE);
            foreach ($m[0] as [$extrait, $position]) {
                $ecarts[] = $this->ecart($document->lieu($position), $extrait);
            }
        }

        return $this->resultat($ecarts);
    }
}

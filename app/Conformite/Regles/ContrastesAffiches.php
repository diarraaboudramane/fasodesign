<?php

namespace App\Conformite\Regles;

use App\Conformite\Corpus;
use App\Conformite\Regle;

/**
 * Les contrastes tels qu'ils s'affichent : 4,5:1 pour le texte courant,
 * 3:1 pour le grand texte. Mesurés dans la page rendue par le navigateur,
 * couleur du texte composée sur son fond réel.
 */
final class ContrastesAffiches extends Regle
{
    public function id(): string { return 'accessibilite.contrastes'; }

    public function titre(): string { return 'Contrastes du texte affiché'; }

    public function famille(): string { return 'accessibilite'; }

    public function gravite(): string { return 'majeure'; }

    public function lien(): string { return 'accessibilite#criteres'; }

    public function correction(): string
    {
        return 'Employer les rôles de texte de la charte (--texte, --texte-attenue) sur ses surfaces, qui atteignent le seuil.';
    }

    public function modes(): array { return ['adresse']; }

    public function verifier(Corpus $corpus): array
    {
        if ($corpus->contrastes === null) {
            return $this->nonVerifiable('Les contrastes affichés ne se mesurent qu\'avec le rendu JavaScript.');
        }

        $c = $corpus->contrastes;
        $note = "{$c['mesures']} élément(s) de texte mesuré(s) sur la page d'accueil"
            .($c['impossibles'] ? ", {$c['impossibles']} sur un fond en image ou en dégradé, non mesurable(s)." : '.');

        return $this->resultat(array_map(fn ($e) => $this->ecart(
            $corpus->cible.' — '.$e['element'],
            '« '.$e['texte'].' » : '.str_replace('.', ',', (string) $e['rapport']).':1 pour '.str_replace('.', ',', (string) $e['seuil']).':1 exigé'
        ), $c['ecarts']), $note);
    }
}

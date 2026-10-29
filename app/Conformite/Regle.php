<?php

namespace App\Conformite;

/**
 * Une règle de la charte, vérifiable par une machine.
 *
 * Chaque règle renvoie à la page de la charte qui l'explique et porte une
 * gravité : bloquante quand l'identité de l'État ou l'accès d'un usager
 * au service est en jeu, majeure ou mineure sinon.
 */
abstract class Regle
{
    /** Écarts gardés par règle : au-delà, le bilan donne le compte. */
    public const ECARTS_GARDES = 30;

    /** Identifiant stable : il sert à comparer deux analyses. */
    abstract public function id(): string;

    abstract public function titre(): string;

    /** integration, identite, couleurs, classes, accessibilite, redaction, confiance, performance */
    abstract public function famille(): string;

    /** bloquante, majeure ou mineure */
    abstract public function gravite(): string;

    /** Page et ancre de la charte : « fondations#couleurs ». */
    abstract public function lien(): string;

    /** Ce qu'il faut faire pour corriger, en une phrase. */
    abstract public function correction(): string;

    /** @return list<string> */
    public function modes(): array
    {
        return ['adresse', 'archive'];
    }

    /**
     * @return array{statut: string, ecarts: list<array{ou: string, extrait: string}>, note?: string}
     */
    abstract public function verifier(Corpus $corpus): array;

    /* ------------------------------------------------ pour les règles */

    protected function respectee(?string $note = null): array
    {
        return array_filter(['statut' => 'respectee', 'ecarts' => [], 'note' => $note], fn ($v) => $v !== null);
    }

    protected function nonVerifiable(string $raison): array
    {
        return ['statut' => 'non_verifiable', 'ecarts' => [], 'note' => $raison];
    }

    /** @param list<array{ou: string, extrait: string}> $ecarts */
    protected function resultat(array $ecarts, ?string $note = null): array
    {
        return $ecarts ? array_filter(['statut' => 'non_respectee', 'ecarts' => $ecarts, 'note' => $note], fn ($v) => $v !== null)
            : $this->respectee($note);
    }

    protected function ecart(string $ou, string $extrait = ''): array
    {
        $extrait = trim(preg_replace('/\s+/', ' ', $extrait));

        return ['ou' => $ou, 'extrait' => mb_strlen($extrait) > 160 ? mb_substr($extrait, 0, 157).'…' : $extrait];
    }

    /**
     * Les classes fs-… employées dans un balisage, avec leur position.
     *
     * @return list<array{0: string, 1: int}>
     */
    protected function classes(Document $document): array
    {
        $trouvees = [];
        preg_match_all('/\b(?:class|className|:class|\[class\]|ngClass)\s*=\s*(["\'{])(.*?)(?:\1|\})/s', $document->contenu, $m, PREG_OFFSET_CAPTURE);
        foreach ($m[2] as [$valeur, $position]) {
            preg_match_all('/(?<![\w-])(fs-[a-z0-9_-]+)/i', $valeur, $noms);
            foreach ($noms[1] as $nom) {
                $trouvees[] = [strtolower($nom), $position];
            }
        }

        return $trouvees;
    }
}

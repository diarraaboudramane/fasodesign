<?php

namespace App\Conformite;

/**
 * Un fichier soumis à l'analyse : une page récupérée par son adresse, ou
 * un fichier lu dans une archive. Le contenu est gardé en mémoire et n'est
 * jamais écrit sur le disque.
 */
final class Document
{
    public function __construct(
        /** L'adresse de la page, ou le chemin dans l'archive. */
        public readonly string $chemin,
        /** html, gabarit, jsx, css, js, json */
        public readonly string $type,
        public readonly string $contenu,
        /** Octets transférés, pour le budget de poids. Égal à la taille en mode archive. */
        public readonly int $octets,
        /** Vrai pour un fichier de la charte elle-même, qu'on ne juge pas. */
        public readonly bool $charte = false,
    ) {}

    public function estBalisage(): bool
    {
        return in_array($this->type, ['html', 'gabarit', 'jsx'], true);
    }

    /** Numéro de ligne d'une position dans le contenu. */
    public function ligne(int $position): int
    {
        return substr_count(substr($this->contenu, 0, $position), "\n") + 1;
    }

    /** Le lieu d'un écart, tel que le bilan l'affiche. */
    public function lieu(?int $position = null): string
    {
        return $position === null ? $this->chemin : $this->chemin.', ligne '.$this->ligne($position);
    }
}

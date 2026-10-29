<?php

namespace App\Conformite\Collecte;

final class Reponse
{
    public function __construct(
        public readonly string $adresse,
        public readonly int $statut,
        public readonly string $type,
        public readonly string $corps,
    ) {}

    public function estHtml(): bool
    {
        return str_contains($this->type, 'html');
    }
}

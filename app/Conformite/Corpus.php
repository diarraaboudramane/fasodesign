<?php

namespace App\Conformite;

/**
 * Ce que l'analyse a pu rassembler : les documents, et ce qu'elle sait de
 * ce qui lui a échappé (pages non atteintes, fichiers ignorés).
 */
final class Corpus
{
    /** @var list<Document> */
    private array $documents = [];

    /** @var list<string> */
    public array $nonAtteints = [];

    /** @var array<string, int> raison => nombre de fichiers ignorés */
    public array $ignores = [];

    /** Version de la charte déclarée par le projet, si on l'a trouvée. */
    public ?string $versionDeclaree = null;

    /** Où la version a été lue (adresse figée, package.json). */
    public ?string $versionLue = null;

    /** Poids transféré par la première page et ses ressources, en mode adresse. */
    public ?int $poidsPremierePage = null;

    /** Vrai quand un navigateur a exécuté le JavaScript des pages. */
    public bool $rendu = false;

    /**
     * Contrastes mesurés dans la page rendue, quand il y a eu rendu.
     *
     * @var array{ecarts: list<array{element: string, texte: string, rapport: float, seuil: float}>, mesures: int, impossibles: int}|null
     */
    public ?array $contrastes = null;

    /**
     * Ressources externes référencées (feuilles, scripts, polices).
     *
     * @var list<array{adresse: string, depuis: string}>
     */
    public array $ressources = [];

    public function __construct(
        /** adresse ou archive */
        public readonly string $mode,
        public readonly string $cible,
    ) {}

    public function ajouter(Document $document): void
    {
        $this->documents[] = $document;
    }

    public function ignorer(string $raison): void
    {
        $this->ignores[$raison] = ($this->ignores[$raison] ?? 0) + 1;
    }

    /** @return list<Document> */
    public function documents(?string ...$types): array
    {
        $types = array_filter($types);

        return array_values(array_filter($this->documents, fn (Document $d) => ! $types || in_array($d->type, $types, true)));
    }

    /** Les documents du projet, sans les fichiers de la charte. @return list<Document> */
    public function propres(string ...$types): array
    {
        return array_values(array_filter($this->documents(...$types), fn (Document $d) => ! $d->charte));
    }

    /** @return list<Document> */
    public function balisages(): array
    {
        return array_values(array_filter($this->documents, fn (Document $d) => $d->estBalisage()));
    }

    public function octets(): int
    {
        return array_sum(array_map(fn (Document $d) => $d->octets, $this->documents));
    }
}

<?php

namespace App\Models;

use App\Conformite\Moteur;
use App\Conformite\Regle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Un écart contesté par l'usager : la matière pour corriger une règle.
 * L'équipe de la charte le traite : règle corrigée, ou écart maintenu avec
 * une réponse qui explique pourquoi.
 */
class Signalement extends Model
{
    public const STATUTS = [
        'nouveau' => 'Nouveau',
        'corrige' => 'Règle corrigée',
        'maintenu' => 'Écart maintenu',
    ];

    protected $fillable = ['user_id', 'regle', 'ou', 'extrait', 'commentaire'];

    protected function casts(): array
    {
        return ['traite_le' => 'datetime'];
    }

    public function analyse(): BelongsTo
    {
        return $this->belongsTo(Analyse::class);
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function traitePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traite_par');
    }

    /**
     * Le constat affiché par l'outil. Les signalements antérieurs à sa
     * conservation le retrouvent dans le bilan lui-même.
     */
    public function constat(): ?string
    {
        if ($this->extrait !== null) {
            return $this->extrait;
        }
        $resultat = collect($this->analyse?->resultats ?? [])->firstWhere('id', $this->regle);
        $ecart = collect($resultat['ecarts'] ?? [])->first(fn ($e) => mb_substr($e['ou'], 0, 255) === $this->ou);

        return $ecart['extrait'] ?? null;
    }

    /** La règle contestée, si elle existe encore dans le moteur. */
    public function regleContestee(): ?Regle
    {
        foreach (Moteur::regles() as $regle) {
            if ($regle->id() === $this->regle) {
                return $regle;
            }
        }

        return null;
    }
}

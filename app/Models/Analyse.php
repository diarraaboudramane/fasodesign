<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Analyse extends Model
{
    protected $table = 'analyses';

    protected $fillable = ['mode', 'cible', 'version_charte'];

    protected function casts(): array
    {
        return [
            'rendu' => 'boolean',
            'resultats' => 'array',
            'perimetre' => 'array',
            'terminee_le' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function signalements(): HasMany
    {
        return $this->hasMany(Signalement::class);
    }

    public function libelleNiveau(): string
    {
        return match ($this->niveau) {
            'conforme' => 'Conforme',
            'reserves' => 'Conforme avec réserves',
            'non_conforme' => 'Non conforme',
            default => match ($this->etat) {
                'echec' => 'Analyse impossible',
                default => 'En cours',
            },
        };
    }

    public function libelleMode(): string
    {
        return match (true) {
            $this->mode === 'archive' => 'Archive du code source',
            $this->rendu => 'Site en ligne, rendu par un navigateur qui exécute son JavaScript',
            default => 'Site en ligne, lu sans exécuter son JavaScript',
        };
    }

    /** L'analyse terminée précédente sur la même cible, pour comparer. */
    public function precedente(): ?self
    {
        return self::where('user_id', $this->user_id)
            ->where('cible', $this->cible)
            ->where('etat', 'terminee')
            ->where('id', '<', $this->id)
            ->latest('id')
            ->first();
    }

    /**
     * Les écarts sous une forme comparable d'une analyse à l'autre.
     *
     * @return array<string, array{regle: string, ou: string}>
     */
    public function clesEcarts(): array
    {
        $cles = [];
        foreach ($this->resultats ?? [] as $resultat) {
            foreach ($resultat['ecarts'] ?? [] as $ecart) {
                $cles[$resultat['id'].'|'.$ecart['ou']] = ['regle' => $resultat['titre'], 'ou' => $ecart['ou']];
            }
        }

        return $cles;
    }
}

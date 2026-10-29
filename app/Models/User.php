<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Un compte de l'outil de contrôle de conformité.
 *
 * Une adresse en .gov.bf est active dès qu'elle est confirmée ; une autre
 * adresse, celle d'un prestataire par exemple, attend qu'un administrateur
 * de la charte la valide.
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    public const ROLES = ['developpeur', 'referent', 'administrateur'];

    protected $fillable = ['name', 'email', 'entite', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(Analyse::class);
    }

    public static function adresseAdministration(string $email): bool
    {
        return (bool) preg_match('/@([a-z0-9-]+\.)*gov\.bf$/i', trim($email));
    }

    public function estActif(): bool
    {
        return $this->statut === 'actif' && $this->hasVerifiedEmail();
    }

    public function estAdministrateur(): bool
    {
        return $this->role === 'administrateur' && $this->estActif();
    }

    /** Peut-il lire ce bilan ? Son auteur, un référent de la même entité, un administrateur. */
    public function peutLire(Analyse $analyse): bool
    {
        return $analyse->user_id === $this->id
            || $this->estAdministrateur()
            || ($this->role === 'referent' && $analyse->user->entite === $this->entite);
    }
}

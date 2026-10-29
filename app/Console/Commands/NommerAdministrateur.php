<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Le premier administrateur de l'outil de conformité ne peut pas être
 * nommé depuis l'outil : il l'est ici, sur le serveur.
 */
class NommerAdministrateur extends Command
{
    protected $signature = 'charte:administrateur {email : adresse d\'un compte déjà créé}';

    protected $description = 'Nomme administrateur de l\'outil de conformité un compte existant, et l\'active.';

    public function handle(): int
    {
        $compte = User::where('email', Str::lower($this->argument('email')))->first();
        if ($compte === null) {
            $this->error('Aucun compte avec cette adresse. Créez-le d\'abord depuis /conformite/inscription.');

            return self::FAILURE;
        }

        $compte->forceFill([
            'role' => 'administrateur',
            'statut' => 'actif',
            'email_verified_at' => $compte->email_verified_at ?? now(),
        ])->save();
        $this->info("{$compte->name} ({$compte->email}) est administrateur de l'outil.");

        return self::SUCCESS;
    }
}

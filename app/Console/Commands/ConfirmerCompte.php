<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Confirme à la main l'adresse d'un compte dont le lien n'a pas pu partir,
 * le temps de régler l'envoi des courriels.
 */
class ConfirmerCompte extends Command
{
    protected $signature = 'charte:confirmer-compte {email}';

    protected $description = 'Confirme l\'adresse d\'un compte de l\'outil de conformité sans passer par le courriel.';

    public function handle(): int
    {
        $compte = User::where('email', Str::lower($this->argument('email')))->first();
        if ($compte === null) {
            $this->error('Aucun compte avec cette adresse.');

            return self::FAILURE;
        }

        if (! $compte->hasVerifiedEmail()) {
            $compte->markEmailAsVerified();
        }
        $this->info("Adresse de {$compte->name} confirmée. Statut du compte : {$compte->statut}.");
        if ($compte->statut !== 'actif') {
            $this->line('Le compte attend encore la validation d\'un administrateur (page Comptes).');
        }

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Envoie un vrai courriel pour vérifier le réglage MAIL_* du serveur. La
 * réponse du serveur de courrier est affichée telle quelle : c'est elle qui
 * dit si c'est l'adresse, le port, le chiffrement ou l'identifiant qui pèche.
 */
class EssayerCourriel extends Command
{
    protected $signature = 'charte:courriel-essai {adresse : où envoyer le message d\'essai}';

    protected $description = 'Envoie un courriel d\'essai avec le réglage actuel et affiche la réponse du serveur de courrier.';

    public function handle(): int
    {
        $transport = config('mail.default');
        $adresse = $this->argument('adresse');

        if (in_array($transport, ['log', 'array'], true)) {
            $this->error("MAIL_MAILER={$transport} : le message serait écrit dans storage/logs et ne partirait pas.");
            $this->line('Renseignez MAIL_MAILER=smtp, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD et MAIL_SCHEME dans .env,');
            $this->line('puis relancez « php artisan optimize » et cette commande.');

            return self::FAILURE;
        }

        $this->line("Envoi par {$transport} (".config("mail.mailers.{$transport}.host").':'.config("mail.mailers.{$transport}.port").") à {$adresse}…");

        try {
            Mail::raw(
                "Ce message confirme que le serveur de la charte graphique sait envoyer des courriels.\n\n"
                ."Les comptes de l'outil de contrôle de conformité peuvent recevoir leur lien de confirmation.",
                fn ($message) => $message->to($adresse)->subject('Essai d\'envoi — charte graphique')
            );
        } catch (Throwable $e) {
            $this->error('Le serveur de courrier a refusé l\'envoi :');
            $this->line('  '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('Message accepté par le serveur de courrier. Vérifiez la boîte de réception, et les indésirables.');

        return self::SUCCESS;
    }
}

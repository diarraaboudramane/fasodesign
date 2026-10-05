<?php

namespace App\Console\Commands;

use App\Rules\Recaptcha;
use Illuminate\Console\Command;

/**
 * Relit la configuration d'un serveur avant de l'ouvrir aux visiteurs.
 *
 *   php artisan charte:diagnostic
 *
 * Chaque réglage oublié ici se découvre sinon en ligne, et souvent par un
 * symptôme qui ne dit pas sa cause : une page sans styles, une case
 * anti-robot qui refuse tout le monde, des messages de contact perdus.
 */
class Diagnostic extends Command
{
    protected $signature = 'charte:diagnostic';

    protected $description = "Vérifie qu'un serveur est prêt à recevoir des visiteurs";

    private int $erreurs = 0;

    public function handle(): int
    {
        $url = (string) config('app.url');
        $hote = (string) parse_url($url, PHP_URL_HOST);

        $this->line("  Serveur : {$url}");
        $this->newLine();

        $this->controle(config('app.key') !== null && config('app.key') !== '',
            'APP_KEY est posée', 'APP_KEY est vide : php artisan key:generate --force');
        $this->controle(! config('app.debug'),
            'APP_DEBUG est désactivé', 'APP_DEBUG=true : les erreurs exposeraient le code et la configuration');
        $this->controle(str_starts_with($url, 'https://'),
            'APP_URL est en https', 'APP_URL doit commencer par https://');
        $this->controle((bool) config('session.secure'),
            'les cookies ne partent qu\'en https', 'SESSION_SECURE_COOKIE=true manque');

        $officiel = $hote === 'chartegraphique.gov.bf';
        $this->controle(config('charte.indexable') === $officiel,
            $officiel ? 'le site officiel est indexable' : 'le serveur de test refuse l\'indexation',
            $officiel ? 'CHARTE_INDEXABLE=false sur le site officiel : il sortirait des moteurs'
                : 'CHARTE_INDEXABLE=true sur un serveur de test : il concurrencerait le site officiel');

        if (config('charte.verification.active')) {
            $site = (string) config('services.recaptcha.site_key');
            $secret = (string) config('services.recaptcha.secret_key');
            $this->controle(self::cleReelle($site) && self::cleReelle($secret),
                'les clés reCAPTCHA sont renseignées',
                'RECAPTCHA_SITE_KEY ou RECAPTCHA_SECRET_KEY vide ou à renseigner : personne ne pourrait entrer');
            $this->controle($secret !== Recaptcha::CLE_SECRETE_ESSAI,
                'les clés reCAPTCHA ne sont pas les clés d\'essai',
                'clés d\'essai de Google : refusées en production, personne ne pourrait entrer');
        } else {
            $this->warn('  !      la vérification anti-robot est coupée (CHARTE_VERIFICATION=false)');
        }

        $destinataire = (string) config('charte.contact.destinataire');
        $this->controle(filter_var($destinataire, FILTER_VALIDATE_EMAIL) !== false,
            "les messages de contact vont à {$destinataire}",
            'CONTACT_DESTINATAIRE vide ou invalide : le formulaire refuserait tout envoi');
        if (config('mail.default') === 'log') {
            $this->warn('  !      MAIL_MAILER=log : les messages restent dans storage/logs');
        }

        foreach (['framework/cache', 'framework/sessions', 'framework/views', 'logs'] as $dossier) {
            $chemin = storage_path($dossier);
            $this->controle(is_dir($chemin) && is_writable($chemin),
                "storage/{$dossier} est accessible en écriture",
                "storage/{$dossier} absent ou non inscriptible par le compte du serveur web");
        }

        $this->newLine();
        if ($this->erreurs) {
            $this->error("  {$this->erreurs} réglage(s) à corriger avant d'ouvrir le serveur.");

            return self::FAILURE;
        }
        $this->info('  Prêt à recevoir des visiteurs.');

        return self::SUCCESS;
    }

    private static function cleReelle(string $cle): bool
    {
        return $cle !== '' && ! str_contains(mb_strtoupper($cle), 'RENSEIGNER');
    }

    private function controle(bool $ok, string $succes, string $echec): void
    {
        if ($ok) {
            $this->line("  ok     {$succes}");

            return;
        }
        $this->erreurs++;
        $this->line("  <fg=red>ECHEC</>  {$echec}");
    }
}

<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

/**
 * Ce que l'application doit savoir de l'hébergement pour se comporter
 * en ligne comme sur le poste du développeur.
 */
class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        /*
         * Derrière un proxy qui termine le TLS, l'application reçoit du
         * http : elle écrirait des liens en http, que le navigateur
         * bloque dans une page https, et la page s'afficherait sans
         * styles. L'adresse publique déclarée fait foi.
         */
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        /*
         * Sans mandataire de confiance déclaré, tous les visiteurs ont
         * l'adresse du proxy : la limitation des envois les compterait
         * comme un seul, et un vrai moteur de recherche ne serait plus
         * reconnu. L'en-tête X-Forwarded-For n'est cru que s'il vient de
         * ces adresses.
         */
        $mandataires = trim((string) config('charte.mandataires'));
        if ($mandataires !== '') {
            TrustProxies::at($mandataires === '*'
                ? '*'
                : preg_split('/[\s,]+/', $mandataires, -1, PREG_SPLIT_NO_EMPTY));
        }

        /* Les courriels des comptes de l'outil de conformité, en français
           et vers les adresses de l'outil. */
        VerifyEmail::createUrlUsing(fn ($compte) => URL::temporarySignedRoute(
            'conformite.courriel.confirmer',
            now()->addHour(),
            ['id' => $compte->getKey(), 'empreinte' => sha1($compte->getEmailForVerification())]
        ));
        VerifyEmail::toMailUsing(fn ($compte, string $adresse) => (new MailMessage)
            ->subject('Confirmez votre adresse — contrôle de conformité à la charte graphique')
            ->greeting('Bonjour '.$compte->name.',')
            ->line('Confirmez votre adresse pour ouvrir votre compte de l\'outil de contrôle de conformité.')
            ->action('Confirmer mon adresse', $adresse)
            ->line('Le lien est valable une heure. Si vous n\'avez pas créé de compte, ignorez ce message.')
            ->salutation('L\'équipe de la charte graphique'));

        ResetPassword::toMailUsing(fn ($compte, string $jeton) => (new MailMessage)
            ->subject('Nouveau mot de passe — contrôle de conformité à la charte graphique')
            ->greeting('Bonjour '.$compte->name.',')
            ->line('Vous avez demandé à changer le mot de passe de votre compte.')
            ->action('Choisir un nouveau mot de passe', route('password.reset', ['jeton' => $jeton, 'email' => $compte->getEmailForPasswordReset()]))
            ->line('Le lien est valable une heure. Si vous n\'avez rien demandé, ignorez ce message : votre mot de passe ne change pas.')
            ->salutation('L\'équipe de la charte graphique'));
    }
}

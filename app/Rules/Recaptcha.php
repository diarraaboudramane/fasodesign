<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Vérifie auprès de Google la réponse d'un reCAPTCHA v2.
 *
 * La règle échoue fermée : sans clé secrète, sans réponse de Google ou
 * avec les clés d'essai en production, le message est refusé plutôt
 * qu'accepté sans contrôle.
 */
class Recaptcha implements ValidationRule
{
    public const VERIFICATION = 'https://www.google.com/recaptcha/api/siteverify';

    /** Clé secrète d'essai publiée par Google : toute réponse est acceptée. */
    public const CLE_SECRETE_ESSAI = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';

    private const INDISPONIBLE = 'La vérification anti-robot est momentanément indisponible. Réessayez dans quelques minutes.';

    public function __construct(private readonly ?string $ip = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secret = config('services.recaptcha.secret_key');

        /* Une valeur de modèle laissée telle quelle (« À-RENSEIGNER ») vaut
           une clé absente. */
        if (blank($secret) || str_contains(mb_strtoupper($secret), 'RENSEIGNER')) {
            Log::error('reCAPTCHA : RECAPTCHA_SECRET_KEY absente, formulaire refusé.');
            $fail(self::INDISPONIBLE);

            return;
        }

        if ($secret === self::CLE_SECRETE_ESSAI && app()->isProduction()) {
            Log::critical("reCAPTCHA : clés d'essai de Google en production, formulaire refusé.");
            $fail(self::INDISPONIBLE);

            return;
        }

        if (! is_string($value) || $value === '') {
            $fail('Cochez la case « Je ne suis pas un robot ».');

            return;
        }

        try {
            $reponse = Http::asForm()->timeout(5)->post(self::VERIFICATION, array_filter([
                'secret' => $secret,
                'response' => $value,
                'remoteip' => $this->ip,
            ]));
        } catch (ConnectionException $e) {
            Log::warning('reCAPTCHA : Google injoignable.', ['erreur' => $e->getMessage()]);
            $fail(self::INDISPONIBLE);

            return;
        }

        if (! $reponse->successful() || $reponse->json('success') !== true) {
            $fail('La vérification anti-robot a échoué. Cochez à nouveau la case.');
        }
    }
}

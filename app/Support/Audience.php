<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Compte les personnes en ligne et les visiteurs distincts du mois.
 *
 * Rien n'est écrit sur l'appareil et aucune adresse IP n'est gardée. Un
 * visiteur est reconnu par une empreinte : adresse et navigateur, hachés
 * avec la clé de l'application et le mois en cours. Sans la clé, elle ne
 * se remonte pas ; au changement de mois, elle change, et rien ne relie
 * un mois au suivant.
 *
 * Tout est tenu dans le cache de l'application, faute de base de données.
 * Une mesure qui échoue ne doit jamais empêcher une page de s'afficher :
 * les erreurs sont avalées.
 */
final class Audience
{
    private const PRESENCE = 'audience.presence';

    public static function enregistrer(Request $request): void
    {
        if (! config('charte.audience.active')) {
            return;
        }

        $mois = now()->format('Y-m');
        $empreinte = substr(hash_hmac(
            'sha256',
            $mois.'|'.$request->ip().'|'.$request->userAgent(),
            (string) config('app.key')
        ), 0, 32);

        try {
            Cache::lock('audience.verrou', 5)->block(2, function () use ($mois, $empreinte) {
                $presence = self::presenceRecente();
                $presence[$empreinte] = time();
                Cache::put(self::PRESENCE, $presence, now()->addHour());

                /* add() n'écrit que si la clé n'existe pas : un visiteur
                   n'est compté qu'une fois dans le mois. */
                if (Cache::add("audience.vu.{$mois}.{$empreinte}", 1, now()->addDays(40))) {
                    Cache::put("audience.total.{$mois}", self::mois() + 1, now()->addDays(40));
                }
            });
        } catch (Throwable $e) {
            Log::debug('Mesure d\'audience non enregistrée : '.$e->getMessage());
        }
    }

    /** Personnes ayant vu une page depuis config('charte.audience.fenetre') minutes. */
    public static function enLigne(): int
    {
        return count(self::presenceRecente());
    }

    /** Visiteurs distincts depuis le premier jour du mois. */
    public static function mois(): int
    {
        return (int) Cache::get('audience.total.'.now()->format('Y-m'), 0);
    }

    /** « octobre », dans la langue du site. */
    public static function libelleMois(): string
    {
        return now()->locale(config('app.locale'))->translatedFormat('F');
    }

    /** @return array{en_ligne: int, mois: int, libelle_mois: string} */
    public static function chiffres(): array
    {
        return ['en_ligne' => self::enLigne(), 'mois' => self::mois(), 'libelle_mois' => self::libelleMois()];
    }

    /** @return array<string, int> */
    private static function presenceRecente(): array
    {
        $limite = time() - 60 * config('charte.audience.fenetre');

        return array_filter((array) Cache::get(self::PRESENCE, []), fn ($vu) => $vu >= $limite);
    }
}

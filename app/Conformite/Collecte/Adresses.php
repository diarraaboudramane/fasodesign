<?php

namespace App\Conformite\Collecte;

final class Adresses
{
    /** Rend une adresse relative absolue, sans fragment ; null si ce n'est pas du http(s). */
    public static function absolue(string $base, string $relative): ?string
    {
        $relative = trim(html_entity_decode($relative));
        if ($relative === '' || str_starts_with($relative, '#') || preg_match('#^(mailto|tel|javascript|data):#i', $relative)) {
            return null;
        }
        $relative = (string) preg_replace('/#.*$/', '', $relative);

        if (preg_match('#^https?://#i', $relative)) {
            return $relative;
        }
        if (preg_match('#^[a-z][a-z0-9+.-]*:#i', $relative)) {
            return null;
        }

        $b = parse_url($base);
        $origine = $b['scheme'].'://'.$b['host'].(isset($b['port']) ? ':'.$b['port'] : '');

        if (str_starts_with($relative, '//')) {
            return $b['scheme'].':'.$relative;
        }
        if (str_starts_with($relative, '/')) {
            return $origine.self::normaliser($relative);
        }

        $dossier = (string) preg_replace('#/[^/]*$#', '/', $b['path'] ?? '/');

        return $origine.self::normaliser($dossier.$relative);
    }

    public static function hote(string $adresse): string
    {
        return strtolower((string) parse_url($adresse, PHP_URL_HOST));
    }

    private static function normaliser(string $chemin): string
    {
        [$chemin, $requete] = array_pad(explode('?', $chemin, 2), 2, null);
        $parties = [];
        foreach (explode('/', $chemin) as $p) {
            if ($p === '..') {
                array_pop($parties);
            } elseif ($p !== '.') {
                $parties[] = $p;
            }
        }
        $resultat = implode('/', $parties);

        return ($resultat === '' ? '/' : $resultat).($requete !== null ? '?'.$requete : '');
    }
}

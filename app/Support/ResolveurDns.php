<?php

namespace App\Support;

/**
 * Les deux résolutions dont la vérification des moteurs a besoin. Isolées
 * ici pour que les essais puissent les remplacer sans réseau.
 */
class ResolveurDns
{
    /** Nom d'hôte d'une adresse IP, ou null s'il n'y en a pas. */
    public function inverse(string $ip): ?string
    {
        $hote = @gethostbyaddr($ip);

        return ($hote === false || $hote === $ip) ? null : strtolower($hote);
    }

    /** @return list<string> adresses IPv4 et IPv6 d'un nom d'hôte */
    public function direct(string $hote): array
    {
        $enregistrements = @dns_get_record($hote, DNS_A | DNS_AAAA) ?: [];

        return array_values(array_filter(array_map(
            fn ($e) => $e['ip'] ?? $e['ipv6'] ?? null,
            $enregistrements
        )));
    }
}

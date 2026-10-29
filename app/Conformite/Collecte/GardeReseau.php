<?php

namespace App\Conformite\Collecte;

use App\Support\ResolveurDns;
use RuntimeException;

/**
 * Décide si l'outil a le droit d'aller chercher une adresse.
 *
 * L'adresse est fournie par un tiers : sans ce contrôle, l'outil pourrait
 * servir à interroger les machines du réseau interne du serveur. Toutes
 * les adresses IP du nom sont vérifiées, et l'appel se fait ensuite sur
 * l'une d'elles, épinglée : un nom qui change de réponse entre le
 * contrôle et l'appel ne trompe pas la garde.
 */
class GardeReseau
{
    public function __construct(private ResolveurDns $dns) {}

    /**
     * @return array{hote: string, port: int, ip: string} de quoi épingler l'appel
     */
    public function verifier(string $adresse): array
    {
        $parties = parse_url($adresse);
        $schema = strtolower($parties['scheme'] ?? '');
        $hote = strtolower(rtrim($parties['host'] ?? '', '.'));

        if (! in_array($schema, ['http', 'https'], true) || $hote === '') {
            throw new RuntimeException('Seules les adresses http et https sont acceptées.');
        }
        if (isset($parties['user']) || isset($parties['pass'])) {
            throw new RuntimeException("Une adresse ne doit pas contenir d'identifiants.");
        }

        $port = (int) ($parties['port'] ?? ($schema === 'https' ? 443 : 80));
        if (! in_array($port, [80, 443], true) && ! $this->localAutorise()) {
            throw new RuntimeException('Seuls les ports 80 et 443 sont acceptés.');
        }

        $litteral = trim($hote, '[]');
        $ips = filter_var($litteral, FILTER_VALIDATE_IP) ? [$litteral] : $this->dns->direct($hote);
        if (! $ips) {
            throw new RuntimeException("Le nom {$hote} ne correspond à aucune adresse.");
        }

        foreach ($ips as $ip) {
            if (! $this->publique($ip) && ! $this->localAutorise()) {
                throw new RuntimeException("L'adresse {$hote} mène à un réseau privé : elle n'est pas analysée.");
            }
        }

        return ['hote' => $hote, 'port' => $port, 'ip' => $ips[0]];
    }

    public function publique(string $ip): bool
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return false;
        }

        /* Plages que les drapeaux de PHP ne couvrent pas toutes. */
        foreach (['0.0.0.0/8', '100.64.0.0/10', '169.254.0.0/16', '192.0.0.0/24', '198.18.0.0/15', '224.0.0.0/4', '240.0.0.0/4'] as $plage) {
            if ($this->dans($ip, $plage)) {
                return false;
            }
        }

        $ip6 = strtolower($ip);

        return ! (str_starts_with($ip6, 'fc') || str_starts_with($ip6, 'fd') || str_starts_with($ip6, 'fe80')
            || $ip6 === '::1' || str_starts_with($ip6, '::ffff:'));
    }

    /** Réservé au poste de développement : jamais en production. */
    private function localAutorise(): bool
    {
        return (bool) config('charte.conformite.reseau_local') && ! app()->isProduction();
    }

    private function dans(string $ip, string $plage): bool
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }
        [$reseau, $bits] = explode('/', $plage);
        $masque = -1 << (32 - (int) $bits);

        return (ip2long($ip) & $masque) === (ip2long($reseau) & $masque);
    }
}

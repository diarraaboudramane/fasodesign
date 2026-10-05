<?php

namespace App\Support;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\IpUtils;

/**
 * Reconnaît un robot d'indexation authentique.
 *
 * Le nom annoncé ne prouve rien : n'importe qui peut se dire Googlebot.
 * L'adresse IP doit appartenir au moteur, vérifiée de l'une des deux
 * façons que les moteurs publient :
 *
 *   domaines  l'adresse se résout en un nom du domaine du moteur, et ce
 *             nom se résout à son tour vers la même adresse ;
 *   adresses  l'adresse figure dans la liste officielle du moteur.
 *
 * Le verdict est gardé un jour par adresse, pour ne pas interroger le DNS
 * ou le moteur à chaque page.
 */
class MoteurDeRecherche
{
    public function __construct(private readonly ResolveurDns $dns) {}

    public function authentique(Request $request): bool
    {
        $agent = (string) $request->userAgent();
        $ip = (string) $request->ip();

        foreach (config('charte.verification.moteurs') as $nom => $preuve) {
            if (stripos($agent, $nom) !== false) {
                return Cache::remember("moteur.{$nom}.{$ip}", now()->addDay(), fn () => isset($preuve['adresses'])
                    ? IpUtils::checkIp($ip, $this->liste($nom, $preuve['adresses']))
                    : $this->parDomaine($ip, $preuve['domaines']));
            }
        }

        return false;
    }

    /** @param  list<string>  $domaines */
    private function parDomaine(string $ip, array $domaines): bool
    {
        $hote = $this->dns->inverse($ip);
        if ($hote === null) {
            return false;
        }

        $duDomaine = false;
        foreach ($domaines as $domaine) {
            if (str_ends_with($hote, $domaine)) {
                $duDomaine = true;
                break;
            }
        }

        return $duDomaine && in_array($ip, $this->dns->direct($hote), true);
    }

    /**
     * Les préfixes publiés par le moteur, au format { prefixes: [{ ipv4Prefix | ipv6Prefix }] }.
     * Si la liste est injoignable, aucun agent n'est admis : il reçoit la
     * case, comme un visiteur, et la liste est redemandée dix minutes plus tard.
     *
     * @return list<string>
     */
    private function liste(string $nom, string $adresse): array
    {
        $prefixes = Cache::get("moteur.liste.{$nom}");
        if (is_array($prefixes)) {
            return $prefixes;
        }

        try {
            $reponse = Http::timeout(5)->acceptJson()->get($adresse);
            $prefixes = $reponse->successful()
                ? array_values(array_filter(array_map(
                    fn ($p) => $p['ipv4Prefix'] ?? $p['ipv6Prefix'] ?? null,
                    (array) $reponse->json('prefixes')
                )))
                : [];
        } catch (ConnectionException $e) {
            $prefixes = [];
        }

        if ($prefixes === []) {
            Log::warning("Liste d'adresses de {$nom} indisponible : {$adresse}");
        }

        Cache::put("moteur.liste.{$nom}", $prefixes, $prefixes === [] ? now()->addMinutes(10) : now()->addDay());

        return $prefixes;
    }
}

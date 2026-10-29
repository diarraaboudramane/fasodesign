<?php

namespace App\Conformite\Collecte;

use RuntimeException;

/**
 * Va chercher une adresse sur le réseau, sous les limites de l'outil :
 * trois redirections, chacune contrôlée par la garde, 2 Mo et dix
 * secondes par réponse. Isolé ici pour que les essais le remplacent.
 */
class Recuperateur
{
    public const TAILLE_MAX = 2 * 1024 * 1024;

    public const REDIRECTIONS_MAX = 3;

    public const AGENT = 'CharteGraphiqueBF-Controle/3.0';

    public function __construct(private GardeReseau $garde) {}

    public function recuperer(string $adresse): Reponse
    {
        for ($saut = 0; $saut <= self::REDIRECTIONS_MAX; $saut++) {
            $cible = $this->garde->verifier($adresse);
            [$statut, $type, $corps, $suivante] = $this->appeler($adresse, $cible);

            if ($suivante === null) {
                return new Reponse($adresse, $statut, $type, $corps);
            }
            $adresse = Adresses::absolue($adresse, $suivante)
                ?? throw new RuntimeException("Redirection illisible : {$suivante}");
        }

        throw new RuntimeException("Trop de redirections depuis {$adresse}.");
    }

    /**
     * @param  array{hote: string, port: int, ip: string}  $cible
     * @return array{int, string, string, ?string}
     */
    protected function appeler(string $adresse, array $cible): array
    {
        $corps = '';
        $poignee = curl_init($adresse);
        curl_setopt_array($poignee, [
            CURLOPT_RESOLVE => ["{$cible['hote']}:{$cible['port']}:{$cible['ip']}"],
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_USERAGENT => self::AGENT,
            CURLOPT_ENCODING => '',
            CURLOPT_HTTPHEADER => ['Accept: text/html,text/css,application/javascript,*/*;q=0.5'],
            CURLOPT_WRITEFUNCTION => function ($p, $morceau) use (&$corps) {
                $corps .= $morceau;

                /* Rendre une longueur différente interrompt le transfert. */
                return strlen($corps) > self::TAILLE_MAX ? 0 : strlen($morceau);
            },
        ]);

        $ok = curl_exec($poignee);
        $statut = (int) curl_getinfo($poignee, CURLINFO_RESPONSE_CODE);
        $type = strtolower((string) curl_getinfo($poignee, CURLINFO_CONTENT_TYPE));
        $suivante = curl_getinfo($poignee, CURLINFO_REDIRECT_URL) ?: null;
        $erreur = curl_error($poignee);

        if ($ok === false && strlen($corps) > self::TAILLE_MAX) {
            throw new RuntimeException("{$adresse} dépasse 2 Mo.");
        }
        if ($ok === false) {
            throw new RuntimeException("{$adresse} n'a pas répondu ({$erreur}).");
        }

        return [$statut, $type, $corps, $statut >= 300 && $statut < 400 ? $suivante : null];
    }
}

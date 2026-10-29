<?php

namespace Tests;

/**
 * Fabrique des archives ZIP pour les essais, sans l'extension zip de PHP :
 * des archives saines, et des archives piégées que l'outil doit refuser.
 */
final class FabriqueZip
{
    /**
     * @param  array<string, string>  $fichiers  chemin => contenu
     * @param  array<string, array{lien?: bool, taille?: int, methode?: int}>  $options  par chemin
     */
    public static function creer(array $fichiers, array $options = []): string
    {
        $locaux = '';
        $central = '';

        foreach ($fichiers as $nom => $contenu) {
            $o = $options[$nom] ?? [];
            $methode = $o['methode'] ?? 8;
            $donnees = $methode === 8 ? gzdeflate($contenu) : $contenu;
            $taille = $o['taille'] ?? strlen($contenu);
            $crc = crc32($contenu);
            $decalage = strlen($locaux);

            $locaux .= pack('VvvvvvVVVvv', 0x04034B50, 20, 0, $methode, 0, 0, $crc, strlen($donnees), $taille, strlen($nom), 0)
                .$nom.$donnees;

            /* Fait par Unix (3) ; un lien symbolique porte le type 0120000. */
            $externe = (! empty($o['lien']) ? 0120777 : 0100644) << 16;
            $central .= pack('VvvvvvvVVVvvvvvVV', 0x02014B50, (3 << 8) | 20, 20, 0, $methode, 0, 0, $crc,
                strlen($donnees), $taille, strlen($nom), 0, 0, 0, 0, $externe, $decalage).$nom;
        }

        $fin = pack('VvvvvVVv', 0x06054B50, 0, 0, count($fichiers), count($fichiers), strlen($central), strlen($locaux), 0);

        return $locaux.$central.$fin;
    }
}

<?php

namespace App\Conformite;

use RuntimeException;

/**
 * Lit une archive ZIP en mémoire, sans rien écrire sur le disque.
 *
 * L'extension zip de PHP n'est pas requise : le format est lu ici, ce
 * qui permet aussi de refuser une archive piégée avant d'en décompresser
 * un seul octet. Sont refusés :
 *   - les chemins qui sortent de l'archive (../, chemin absolu, lecteur) ;
 *   - les liens symboliques ;
 *   - les archives chiffrées ou au format ZIP64 ;
 *   - un nombre d'entrées, une taille décompressée totale ou un taux de
 *     compression qui trahissent une bombe de décompression.
 *
 * Seules les méthodes « stockée » et « deflate » sont lues ; ce sont
 * celles qu'emploient tous les outils courants.
 */
final class LecteurZip
{
    public const ENTREES_MAX = 5000;

    public const TOTAL_MAX = 200 * 1024 * 1024;

    /** Au-delà, un fichier texte de projet n'a pas de raison d'exister. */
    public const FICHIER_MAX = 2 * 1024 * 1024;

    /** Un code source se compresse rarement plus de 20 fois ; 100 fois, c'est une bombe. */
    public const TAUX_MAX = 100;

    private string $octets;

    /** @var list<array{nom: string, methode: int, compresse: int, taille: int, local: int}> */
    private array $entrees = [];

    public function __construct(string $octets)
    {
        $this->octets = $octets;
        $this->lireRepertoire();
    }

    /** @return list<array{nom: string, taille: int}> les fichiers, dossiers exclus */
    public function fichiers(): array
    {
        return array_values(array_map(
            fn ($e) => ['nom' => $e['nom'], 'taille' => $e['taille']],
            array_filter($this->entrees, fn ($e) => ! str_ends_with($e['nom'], '/'))
        ));
    }

    /** Le contenu décompressé d'un fichier de l'archive. */
    public function lire(string $nom): string
    {
        foreach ($this->entrees as $e) {
            if ($e['nom'] !== $nom) {
                continue;
            }
            if ($e['taille'] > self::FICHIER_MAX) {
                throw new RuntimeException("{$nom} dépasse la taille lue par l'analyse.");
            }

            $entete = substr($this->octets, $e['local'], 30);
            if (strlen($entete) < 30 || substr($entete, 0, 4) !== "PK\x03\x04") {
                throw new RuntimeException("En-tête local illisible pour {$nom}.");
            }
            $l = unpack('vlongueurNom/vlongueurExtra', substr($entete, 26, 4));
            $debut = $e['local'] + 30 + $l['longueurNom'] + $l['longueurExtra'];
            $brut = substr($this->octets, $debut, $e['compresse']);

            $contenu = match ($e['methode']) {
                0 => $brut,
                /* La longueur maximale passée à gzinflate borne la sortie :
                   une taille annoncée mensongère ne fait pas exploser la mémoire. */
                8 => @gzinflate($brut, max(1, $e['taille'])),
                default => throw new RuntimeException("{$nom} : méthode de compression non prise en charge."),
            };

            if ($contenu === false || strlen($contenu) !== $e['taille']) {
                throw new RuntimeException("{$nom} : contenu altéré ou taille annoncée inexacte.");
            }

            return $contenu;
        }

        throw new RuntimeException("{$nom} n'est pas dans l'archive.");
    }

    private function lireRepertoire(): void
    {
        $longueur = strlen($this->octets);
        /* La fin du répertoire central est dans les 65 557 derniers octets. */
        $queue = substr($this->octets, max(0, $longueur - 65557));
        $position = strrpos($queue, "PK\x05\x06");
        if ($position === false) {
            throw new RuntimeException("Ce fichier n'est pas une archive ZIP lisible.");
        }

        $fin = unpack('vdisque/vdisqueDebut/vici/ventrees/Vtaille/Vdecalage', substr($queue, $position + 4, 16));
        if ($fin['entrees'] === 0xFFFF || $fin['decalage'] === 0xFFFFFFFF) {
            throw new RuntimeException('Les archives ZIP64 ne sont pas acceptées.');
        }
        if ($fin['disque'] !== 0 || $fin['entrees'] !== $fin['ici']) {
            throw new RuntimeException('Les archives en plusieurs volumes ne sont pas acceptées.');
        }
        if ($fin['entrees'] > self::ENTREES_MAX) {
            throw new RuntimeException('L\'archive contient plus de '.self::ENTREES_MAX.' fichiers.');
        }

        $curseur = $fin['decalage'];
        $total = 0;

        for ($i = 0; $i < $fin['entrees']; $i++) {
            if (substr($this->octets, $curseur, 4) !== "PK\x01\x02") {
                throw new RuntimeException('Répertoire central altéré.');
            }
            $c = unpack(
                'vfaitPar/vbesoin/vdrapeaux/vmethode/vheure/vdate/Vcrc/Vcompresse/Vtaille/vlongueurNom/vlongueurExtra/vlongueurCommentaire/vdisque/vinterne/Vexterne/Vlocal',
                substr($this->octets, $curseur + 4, 42)
            );
            $nom = substr($this->octets, $curseur + 46, $c['longueurNom']);
            $curseur += 46 + $c['longueurNom'] + $c['longueurExtra'] + $c['longueurCommentaire'];

            if ($c['drapeaux'] & 0x1) {
                throw new RuntimeException('Les archives chiffrées ne sont pas acceptées.');
            }
            if ($c['taille'] === 0xFFFFFFFF || $c['compresse'] === 0xFFFFFFFF || $c['local'] === 0xFFFFFFFF) {
                throw new RuntimeException('Les archives ZIP64 ne sont pas acceptées.');
            }

            $nom = str_replace('\\', '/', $nom);
            if ($nom === '' || str_starts_with($nom, '/') || preg_match('#^[a-z]:#i', $nom)
                || preg_match('#(^|/)\.\.(/|$)#', $nom) || str_contains($nom, "\0")) {
                throw new RuntimeException("Chemin refusé dans l'archive : {$nom}");
            }

            /* Système Unix (octet haut de « fait par » = 3) : les bits de type
               de fichier sont dans l'attribut externe. 0120000 : lien symbolique. */
            if (($c['faitPar'] >> 8) === 3 && ((($c['externe'] >> 16) & 0170000) === 0120000)) {
                throw new RuntimeException("Lien symbolique refusé dans l'archive : {$nom}");
            }

            if ($c['compresse'] > 0 && $c['taille'] / $c['compresse'] > self::TAUX_MAX && $c['taille'] > 1024 * 1024) {
                throw new RuntimeException("Taux de compression anormal pour {$nom} : archive refusée.");
            }

            $total += $c['taille'];
            if ($total > self::TOTAL_MAX) {
                throw new RuntimeException('Le contenu décompressé de l\'archive dépasse '.(self::TOTAL_MAX >> 20).' Mo.');
            }

            $this->entrees[] = [
                'nom' => $nom,
                'methode' => $c['methode'],
                'compresse' => $c['compresse'],
                'taille' => $c['taille'],
                'local' => $c['local'],
            ];
        }
    }
}

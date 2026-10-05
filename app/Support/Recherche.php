<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\Cache;

/**
 * Recherche dans toute la documentation.
 *
 * L'index est tiré des pages telles que l'application les rend, découpées
 * en sections : un résultat mène à la section, pas seulement à la page.
 * Il est recalculé dès qu'une vue change, et mis en cache sinon.
 *
 * La comparaison ignore la casse et les accents : « accessibilite »
 * trouve « Accessibilité ». Tous les mots demandés doivent figurer dans
 * la section.
 */
final class Recherche
{
    public const MAX_RESULTATS = 30;

    private const MAX_MOTS = 8;

    private const LONGUEUR_EXTRAIT = 240;

    /**
     * Les accents retirés un caractère pour un caractère : le texte
     * normalisé garde la longueur de l'original, et une position trouvée
     * dans l'un vaut dans l'autre.
     */
    private const SANS_ACCENTS = [
        'à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a', 'ã' => 'a',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'î' => 'i', 'ï' => 'i', 'í' => 'i', 'ì' => 'i',
        'ô' => 'o', 'ö' => 'o', 'ó' => 'o', 'ò' => 'o', 'õ' => 'o',
        'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ú' => 'u',
        'ç' => 'c', 'ÿ' => 'y', 'ñ' => 'n',
        'ɛ' => 'e', 'ɔ' => 'o', 'ŋ' => 'n', 'ɩ' => 'i', 'ʋ' => 'u',
        "\u{00A0}" => ' ', "\u{202F}" => ' ', '’' => "'",
    ];

    public static function normaliser(string $texte): string
    {
        return strtr(mb_strtolower($texte), self::SANS_ACCENTS);
    }

    /** @return list<string> */
    public static function mots(string $requete): array
    {
        $mots = preg_split('/[^\p{L}\p{N}_:.@#-]+/u', self::normaliser($requete), -1, PREG_SPLIT_NO_EMPTY);
        $mots = array_filter($mots, fn ($m) => mb_strlen(trim($m, '.:-')) >= 2);

        return array_slice(array_values(array_unique($mots)), 0, self::MAX_MOTS);
    }

    /**
     * @return list<array{page: string, libelle: string, url: string, titre: string, extrait: string}>
     */
    public static function chercher(string $requete): array
    {
        $mots = self::mots($requete);
        if (! $mots) {
            return [];
        }

        $resultats = [];
        foreach (self::index() as $section) {
            $titre = self::normaliser($section['titre']);
            $texte = self::normaliser($section['texte']);
            $score = 0;

            foreach ($mots as $mot) {
                $dansTitre = substr_count($titre, $mot);
                $dansTexte = substr_count($texte, $mot);
                if ($dansTitre + $dansTexte === 0) {
                    continue 2;
                }
                $score += 10 * $dansTitre + min($dansTexte, 20);
            }

            $resultats[] = ['score' => $score] + $section;
        }

        usort($resultats, fn ($a, $b) => $b['score'] <=> $a['score']);

        return array_map(fn ($r) => [
            'page' => $r['page'],
            'libelle' => $r['libelle'],
            'url' => route($r['page']).($r['ancre'] ? '#'.$r['ancre'] : ''),
            'titre' => self::surligner($r['titre'], $mots),
            'extrait' => self::extrait($r['texte'], $mots),
        ], array_slice($resultats, 0, self::MAX_RESULTATS));
    }

    /**
     * @return list<array{page: string, libelle: string, ancre: ?string, titre: string, texte: string}>
     */
    public static function index(): array
    {
        $empreinte = md5(implode('|', array_map(
            fn ($f) => $f.filemtime($f),
            array_merge(glob(resource_path('views/pages/*.blade.php')), glob(resource_path('views/layouts/*.blade.php')))
        )).Documentation::version());

        return Cache::rememberForever("recherche.index.{$empreinte}", self::construire(...));
    }

    /** @return list<array{page: string, libelle: string, ancre: ?string, titre: string, texte: string}> */
    private static function construire(): array
    {
        $index = [];

        foreach (Documentation::pages() as $nom => $page) {
            $dom = new DOMDocument;
            libxml_use_internal_errors(true);
            $dom->loadHTML('<?xml encoding="utf-8">'.Documentation::vue($nom)->render(), LIBXML_NONET);
            libxml_clear_errors();
            $xpath = new DOMXPath($dom);

            /* Ce qui n'est pas du texte lisible ne doit pas répondre. */
            foreach (iterator_to_array($xpath->query('//script|//style|//svg|//template')) as $inutile) {
                $inutile->parentNode->removeChild($inutile);
            }

            foreach ($xpath->query('//main//section') as $section) {
                /** @var DOMElement $section */
                $titre = $xpath->query('.//h1|.//h2', $section)->item(0);
                if (! $titre) {
                    continue;
                }

                $index[] = [
                    'page' => $nom,
                    'libelle' => $page['libelle'],
                    'ancre' => $section->getAttribute('id') ?: null,
                    'titre' => self::aplatir($titre->textContent),
                    'texte' => self::aplatir($section->textContent),
                ];
            }
        }

        return $index;
    }

    private static function aplatir(string $texte): string
    {
        return trim(preg_replace('/\s+/u', ' ', $texte));
    }

    /** Un passage autour de la première occurrence, termes surlignés, déjà échappé. */
    private static function extrait(string $texte, array $mots): string
    {
        $normalise = self::normaliser($texte);
        $premiere = null;
        foreach ($mots as $mot) {
            $pos = mb_strpos($normalise, $mot);
            if ($pos !== false && ($premiere === null || $pos < $premiere)) {
                $premiere = $pos;
            }
        }

        $longueur = mb_strlen($texte);
        $debut = max(0, ($premiere ?? 0) - intdiv(self::LONGUEUR_EXTRAIT, 3));
        if ($debut > 0) {
            $espace = mb_strpos($texte, ' ', $debut);
            $debut = $espace === false ? $debut : $espace + 1;
        }
        $fin = min($longueur, $debut + self::LONGUEUR_EXTRAIT);
        if ($fin < $longueur) {
            $espace = mb_strrpos(mb_substr($texte, 0, $fin), ' ');
            $fin = $espace !== false && $espace > $debut ? $espace : $fin;
        }

        return ($debut > 0 ? '… ' : '')
            .self::surligner(mb_substr($texte, $debut, $fin - $debut), $mots)
            .($fin < $longueur ? ' …' : '');
    }

    /** Échappe le texte et entoure de <mark> chaque occurrence des mots. */
    private static function surligner(string $texte, array $mots): string
    {
        $normalise = self::normaliser($texte);
        $longueur = mb_strlen($texte);
        $marque = array_fill(0, $longueur, false);

        foreach ($mots as $mot) {
            $taille = mb_strlen($mot);
            $pos = 0;
            while (($pos = mb_strpos($normalise, $mot, $pos)) !== false) {
                for ($i = $pos; $i < $pos + $taille; $i++) {
                    $marque[$i] = true;
                }
                $pos += $taille;
            }
        }

        $sortie = '';
        $ouvert = false;
        for ($i = 0; $i < $longueur; $i++) {
            if ($marque[$i] !== $ouvert) {
                $sortie .= $marque[$i] ? '<mark>' : '</mark>';
                $ouvert = $marque[$i];
            }
            $sortie .= e(mb_substr($texte, $i, 1));
        }

        return $sortie.($ouvert ? '</mark>' : '');
    }
}

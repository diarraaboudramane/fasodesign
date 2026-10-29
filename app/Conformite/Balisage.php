<?php

namespace App\Conformite;

use DOMDocument;
use DOMElement;
use DOMXPath;

/**
 * Lecture tolérante du balisage : une page servie, mais aussi un gabarit
 * Blade, Vue ou Angular, qui n'est pas du HTML valide. Le JSX n'est pas
 * passé au DOM : ses attributs sont lus par motif.
 */
final class Balisage
{
    /** @var array<string, DOMXPath|null> */
    private static array $cache = [];

    public static function xpath(Document $document): ?DOMXPath
    {
        if (! in_array($document->type, ['html', 'gabarit'], true)) {
            return null;
        }

        $cle = spl_object_hash($document);
        if (! array_key_exists($cle, self::$cache)) {
            $dom = new DOMDocument;
            $precedent = libxml_use_internal_errors(true);
            /* Sans déclaration, libxml lit le texte en Latin-1. */
            $dom->loadHTML('<?xml encoding="utf-8"?>'.$document->contenu, LIBXML_NONET | LIBXML_COMPACT);
            libxml_clear_errors();
            libxml_use_internal_errors($precedent);
            self::$cache[$cle] = new DOMXPath($dom);
        }

        return self::$cache[$cle];
    }

    /** @return list<DOMElement> */
    public static function elements(Document $document, string $requete): array
    {
        $xpath = self::xpath($document);
        if ($xpath === null) {
            return [];
        }
        $resultat = [];
        foreach ($xpath->query($requete) ?: [] as $noeud) {
            if ($noeud instanceof DOMElement) {
                $resultat[] = $noeud;
            }
        }

        return $resultat;
    }

    /** Le lieu d'un élément : document et ligne. La déclaration ajoutée décale d'aucune ligne. */
    public static function lieu(Document $document, DOMElement $element): string
    {
        return $document->chemin.', ligne '.$element->getLineNo();
    }

    public static function extrait(DOMElement $element): string
    {
        $html = $element->ownerDocument->saveHTML($element) ?: '';
        $html = preg_replace('/\s+/', ' ', $html);

        return mb_strlen($html) > 140 ? mb_substr($html, 0, 137).'…' : $html;
    }

    /** Le texte lisible d'un élément, espaces normalisés. */
    public static function texte(DOMElement $element): string
    {
        return trim(preg_replace('/\s+/u', ' ', $element->textContent));
    }

    /** Une page complète, par opposition à un fragment de gabarit. */
    public static function estPageComplete(Document $document): bool
    {
        return $document->type === 'html' && preg_match('/<html[\s>]/i', $document->contenu) === 1
            || $document->type === 'gabarit' && preg_match('/<body[\s>]/i', $document->contenu) === 1;
    }

    public static function oublier(): void
    {
        self::$cache = [];
    }
}

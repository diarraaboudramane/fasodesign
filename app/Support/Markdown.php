<?php

namespace App\Support;

use DOMElement;
use DOMNode;
use DOMText;

/**
 * Conversion en Markdown d'une page de la documentation telle que
 * l'application la rend.
 *
 * Les exemples (.doc-demo) et les gabarits (.doc-maquette) ne sont pas
 * décrits : leur balisage est recopié dans un bloc de code HTML, comme le
 * fait le bouton « Code » du site. Ce qui n'est pas du contenu lisible
 * (scripts, dessins, navigation, formulaires du site) est écarté.
 */
final class Markdown
{
    private const IGNORES = ['script', 'style', 'svg', 'template', 'noscript', 'nav', 'form', 'button', 'input', 'select', 'textarea'];

    private const BLOCS = [
        'address', 'article', 'aside', 'blockquote', 'details', 'div', 'dl', 'dd', 'dt', 'fieldset',
        'figcaption', 'figure', 'footer', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'hr', 'li',
        'main', 'ol', 'p', 'pre', 'section', 'summary', 'table', 'ul',
    ];

    /** Balises de mise en forme : collées à leur voisine, elles le restent. */
    private const FORMES = ['a', 'abbr', 'b', 'br', 'cite', 'code', 'dfn', 'em', 'i', 'img', 'kbd', 'mark', 'q', 's', 'small', 'strong', 'sub', 'sup', 'time', 'u', 'var'];

    /**
     * @param  string  $base  adresse de l'application qui a rendu la page
     * @param  string  $serveur  adresse publique qui la remplace dans les liens
     * @param  string  $page  adresse publique de la page, pour les ancres
     * @param  int  $decalage  niveaux ajoutés aux titres : h2 devient ### pour 1
     */
    public function __construct(
        private readonly string $base,
        private readonly string $serveur,
        private readonly string $page,
        private readonly int $decalage = 1,
    ) {}

    public function convertir(DOMNode $racine): string
    {
        $texte = $this->blocs($racine);
        $texte = preg_replace("/[ \t]+\n/", "\n", preg_replace("/\n{3,}/", "\n\n", $texte));

        return trim($texte)."\n";
    }

    /* ------------------------------------------------------- les blocs */

    private function blocs(DOMNode $parent): string
    {
        $morceaux = [];
        $enCours = [];

        $vider = function () use (&$morceaux, &$enCours) {
            $ligne = $this->joindre($enCours);
            if ($ligne !== '') {
                $morceaux[] = $ligne;
            }
            $enCours = [];
        };

        foreach ($parent->childNodes as $noeud) {
            if ($noeud instanceof DOMElement && in_array(strtolower($noeud->tagName), self::BLOCS, true)) {
                $vider();
                $bloc = rtrim($this->bloc($noeud));
                if (trim($bloc) !== '') {
                    $morceaux[] = $bloc;
                }
            } else {
                $enCours[] = $noeud;
            }
        }
        $vider();

        return implode("\n\n", $morceaux);
    }

    private function bloc(DOMElement $e): string
    {
        if ($this->masque($e)) {
            return '';
        }
        if ($this->aClasse($e, 'doc-demo')) {
            return $this->exemple($e);
        }
        if ($this->aClasse($e, 'doc-maquette')) {
            return $this->maquette($e);
        }

        $balise = strtolower($e->tagName);

        return match (true) {
            $balise === 'h1' => '',
            (bool) preg_match('/^h([2-6])$/', $balise, $m) => str_repeat('#', min(6, (int) $m[1] + $this->decalage)).' '.$this->enLigne($e),
            $balise === 'p' => $this->paragraphe($e),
            $balise === 'ul' => $this->liste($e, false),
            $balise === 'ol' => $this->liste($e, true),
            $balise === 'table' => $this->tableau($e),
            $balise === 'pre' => $this->code($e->textContent),
            $balise === 'hr' => '---',
            $balise === 'summary', $balise === 'dt' => '**'.$this->enLigne($e).'**',
            $balise === 'figcaption' => '*'.$this->enLigne($e).'*',
            $balise === 'blockquote', $this->aClasse($e, 'fs-encart'), $this->aClasse($e, 'doc-note') => $this->citation($e),
            default => $this->blocs($e),
        };
    }

    private function paragraphe(DOMElement $p): string
    {
        foreach ($p->childNodes as $enfant) {
            if ($enfant instanceof DOMElement && in_array(strtolower($enfant->tagName), self::BLOCS, true)) {
                return $this->blocs($p);
            }
        }

        $texte = $this->enLigne($p);
        if ($texte !== '' && ($this->aClasse($p, 'doc-usage-titre') || $this->aClasse($p, 'fs-encart-titre'))) {
            return '**'.$texte.'**';
        }

        return $texte;
    }

    private function citation(DOMElement $e): string
    {
        $texte = $this->blocs($e);

        return $texte === '' ? '' : implode("\n", array_map(
            fn ($ligne) => rtrim('> '.$ligne),
            explode("\n", $texte)
        ));
    }

    private function liste(DOMElement $liste, bool $numerotee): string
    {
        $lignes = [];
        $rang = 0;

        foreach ($liste->childNodes as $item) {
            if (! $item instanceof DOMElement || strtolower($item->tagName) !== 'li' || $this->masque($item)) {
                continue;
            }
            $marque = $numerotee ? (++$rang).'. ' : '- ';
            $contenu = $this->blocs($item);
            if ($contenu === '') {
                continue;
            }
            $retrait = str_repeat(' ', strlen($marque));
            $lignes[] = $marque.str_replace("\n", "\n".$retrait, $contenu);
        }

        return implode("\n", $lignes);
    }

    private function tableau(DOMElement $table): string
    {
        $rangees = [];
        $legende = '';

        foreach ($table->getElementsByTagName('tr') as $tr) {
            $cellules = [];
            foreach ($tr->childNodes as $cellule) {
                if ($cellule instanceof DOMElement && in_array(strtolower($cellule->tagName), ['td', 'th'], true)) {
                    $texte = str_replace(["\n", '|'], [' ', '\|'], $this->enLigne($cellule));
                    $cellules[] = trim(preg_replace('/ {2,}/', ' ', $texte));
                }
            }
            if ($cellules) {
                $rangees[] = $cellules;
            }
        }
        foreach ($table->getElementsByTagName('caption') as $caption) {
            $legende = '*'.$this->enLigne($caption).'*'."\n\n";
        }
        if (! $rangees) {
            return '';
        }

        $colonnes = max(array_map('count', $rangees));
        $ligne = fn (array $c) => '| '.implode(' | ', array_pad($c, $colonnes, '')).' |';

        $sortie = [$ligne(array_shift($rangees)), '|'.str_repeat(' --- |', $colonnes)];
        foreach ($rangees as $rangee) {
            $sortie[] = $ligne($rangee);
        }

        return $legende.implode("\n", $sortie);
    }

    private function exemple(DOMElement $demo): string
    {
        $libelle = $demo->getAttribute('data-libelle') ?: 'Exemple';
        foreach ($demo->getElementsByTagName('*') as $enfant) {
            if ($this->aClasse($enfant, 'doc-demo-scene')) {
                return '*Exemple — '.$libelle."*\n\n".$this->code($this->interieur($enfant), 'html');
            }
        }

        return '';
    }

    private function maquette(DOMElement $maquette): string
    {
        $adresse = '';
        $contenu = null;
        foreach ($maquette->getElementsByTagName('*') as $enfant) {
            if ($this->aClasse($enfant, 'doc-maquette-url')) {
                $adresse = trim($enfant->textContent);
            } elseif ($this->aClasse($enfant, 'doc-maquette-contenu')) {
                $contenu = $enfant;
            }
        }

        return $contenu === null ? '' : '*Gabarit'.($adresse ? ' — '.$adresse : '')."*\n\n"
            .$this->code($this->interieur($contenu), 'html');
    }

    /* ------------------------------------------------------ le code */

    /** Le balisage contenu dans un élément, retrait commun retiré. */
    private function interieur(DOMElement $e): string
    {
        $html = '';
        foreach ($e->childNodes as $enfant) {
            $html .= $e->ownerDocument->saveHTML($enfant);
        }

        return $this->adresses($html);
    }

    private function code(string $texte, ?string $langue = null): string
    {
        $lignes = explode("\n", str_replace("\r", '', $texte));
        while ($lignes && trim($lignes[0]) === '') {
            array_shift($lignes);
        }
        while ($lignes && trim(end($lignes)) === '') {
            array_pop($lignes);
        }
        if (! $lignes) {
            return '';
        }

        $retrait = min(array_map(
            fn ($l) => strlen($l) - strlen(ltrim($l, ' ')),
            array_filter($lignes, fn ($l) => trim($l) !== '')
        ));
        $texte = implode("\n", array_map(fn ($l) => rtrim(substr($l, $retrait)), $lignes));

        preg_match_all('/`+/', $texte, $m);
        $clotures = str_repeat('`', max(3, ...array_map(fn ($s) => strlen($s) + 1, $m[0] ?: [''])));

        return $clotures.($langue ?? self::langue($texte))."\n".$texte."\n".$clotures;
    }

    private static function langue(string $code): string
    {
        $debut = ltrim($code);

        return match (true) {
            str_starts_with($debut, '<?xml'), str_starts_with($debut, '<resources') => 'xml',
            str_contains($code, '@vite'), str_contains($code, '{{'), str_contains($code, '@csrf') => 'blade',
            str_starts_with($debut, '<') => 'html',
            (bool) preg_match('/^(npm|npx|php|composer|curl|cd|mkdir|git|tar|cp|htpasswd|verdaccio|node) /m', $debut),
            str_starts_with($debut, '# ') => 'bash',
            (bool) preg_match('/\b(fun|val) \w/', $code) => 'kotlin',
            (bool) preg_match('/\b(private|public) (void|static)\b/', $code) => 'java',
            str_starts_with($debut, '{'), str_starts_with($debut, '"') => 'json',
            (bool) preg_match('/^[^{}\n]*\{[^}]*:[^}]*;/m', $code) && ! str_contains($code, '=>') => 'css',
            (bool) preg_match('/\b(import|export|const|let|function|=>)\b|\bFaso\./', $code) => 'js',
            default => '',
        };
    }

    /* ----------------------------------------------------- en ligne */

    /** @param  list<DOMNode>  $noeuds */
    private function joindre(array $noeuds): string
    {
        $texte = '';
        $precedent = null;

        foreach ($noeuds as $noeud) {
            /* Deux éléments collés, sans espace dans la source, sont deux
               valeurs distinctes : « 50 » et « #fff1f2 » dans le nuancier. */
            if ($precedent instanceof DOMElement && $noeud instanceof DOMElement
                && ! in_array(strtolower($precedent->tagName), self::FORMES, true)
                && ! in_array(strtolower($noeud->tagName), self::FORMES, true)) {
                $texte .= ' ';
            }
            $texte .= $this->enLigne($noeud);
            $precedent = $noeud;
        }

        return trim(preg_replace('/(?<=\S) {2,}(?=\S)/u', ' ', $texte));
    }

    private function enLigne(DOMNode $noeud): string
    {
        if ($noeud instanceof DOMText) {
            return $this->echapper(preg_replace("/[ \t\r\n]+/", ' ', $noeud->textContent));
        }
        if (! $noeud instanceof DOMElement || $this->masque($noeud)) {
            return '';
        }

        $balise = strtolower($noeud->tagName);
        if (in_array($balise, self::IGNORES, true)) {
            return '';
        }

        $contenu = fn () => $this->joindre(iterator_to_array($noeud->childNodes));

        switch ($balise) {
            case 'br':
                return "  \n";
            case 'code':
            case 'kbd':
                $code = preg_replace('/\s+/', ' ', $noeud->textContent);
                $tic = str_contains($code, '`') ? '``' : '`';

                return trim($code) === '' ? '' : $tic.($tic === '``' ? ' ' : '').$code.($tic === '``' ? ' ' : '').$tic;
            case 'strong':
            case 'b':
                $t = $contenu();

                return $t === '' ? '' : '**'.$t.'**';
            case 'em':
            case 'i':
            case 'cite':
                $t = $contenu();

                return $t === '' ? '' : '*'.$t.'*';
            case 'img':
                $alt = trim($noeud->getAttribute('alt'));

                return $alt === '' ? '' : '!['.$this->echapper($alt).']('.$this->lien($noeud->getAttribute('src')).')';
            case 'a':
                $t = $contenu();
                $href = $noeud->getAttribute('href');
                if ($t === '' || $href === '' || $href === '#' || str_starts_with($href, 'javascript:')) {
                    return $t;
                }

                return '['.$t.']('.$this->lien($href).')';
            default:
                return $contenu();
        }
    }

    private function echapper(string $texte): string
    {
        return addcslashes($texte, '\\`*_[]<>');
    }

    /* ----------------------------------------------------- adresses */

    private function lien(string $href): string
    {
        if (str_starts_with($href, '#')) {
            return $this->page.$href;
        }

        return $this->adresses($href);
    }

    /** Les adresses de l'application deviennent celles du serveur public. */
    private function adresses(string $texte): string
    {
        /* Le paramètre qui force le navigateur à recharger un fichier
           modifié (ressource()) n'a pas de sens hors du site. */
        $texte = preg_replace('/(\.[a-z0-9]+)\?v=\d+/i', '$1', $texte);

        return rtrim($this->base, '/') === rtrim($this->serveur, '/')
            ? $texte
            : str_replace(rtrim($this->base, '/'), rtrim($this->serveur, '/'), $texte);
    }

    /* -------------------------------------------------------- outils */

    private function masque(DOMElement $e): bool
    {
        return $e->getAttribute('aria-hidden') === 'true' || $e->hasAttribute('hidden');
    }

    private function aClasse(DOMNode $e, string $classe): bool
    {
        return $e instanceof DOMElement
            && in_array($classe, preg_split('/\s+/', $e->getAttribute('class')), true);
    }
}

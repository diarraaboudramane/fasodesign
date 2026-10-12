<?php

namespace App\Support;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Cache;

/**
 * Toute la charte en un seul fichier Markdown, pour les développeurs et
 * les agents (assistants de code) qui travaillent avec eux.
 *
 * Le document est tiré des pages telles que l'application les rend, dans
 * l'ordre de la navigation, puis complété des jetons, des classes et de
 * l'API JavaScript : il ne peut pas diverger du site. Il s'ouvre sur les
 * consignes faites aux agents. Recalculé dès qu'une source change, mis en
 * cache sinon.
 */
final class CharteMarkdown
{
    /** Ce dont le document est tiré, au-delà des vues. */
    private const SOURCES = [
        'assets/css/tokens.css',
        'assets/css/faso.css',
        'assets/css/icones.css',
        'assets/js/faso.d.ts',
        'package.json',
    ];

    public static function nomFichier(): string
    {
        return 'charte-graphique-'.Documentation::version().'.md';
    }

    public static function document(): string
    {
        $fichiers = array_merge(
            glob(resource_path('views/pages/*.blade.php')),
            glob(resource_path('views/layouts/*.blade.php')),
            glob(resource_path('views/partials/*.blade.php')),
            [__FILE__, app_path('Support/Markdown.php')],
            array_map('base_path', self::SOURCES),
        );
        $empreinte = md5(implode('|', array_map(fn ($f) => $f.filemtime($f), $fichiers)).config('charte.serveur'));

        return Cache::rememberForever("charte.markdown.{$empreinte}", self::construire(...));
    }

    private static function construire(): string
    {
        /* Les liens du document sont absolus, même pendant l'export
           statique, où les pages s'écrivent avec des liens relatifs. */
        $export = config('charte.export');
        config(['charte.export' => false]);
        try {
            return self::assembler();
        } finally {
            config(['charte.export' => $export]);
        }
    }

    private static function assembler(): string
    {
        $serveur = config('charte.serveur');
        $version = Documentation::version();
        $pages = Documentation::pages();

        $md = [];
        $md[] = "---\n"
            ."titre: Charte graphique de l'administration burkinabè\n"
            ."version: {$version}\n"
            ."source: {$serveur}\n"
            ."paquet: \"@govbf/charte-graphique\"\n"
            ."genere: ".now()->toDateString()."\n"
            .'---';
        $md[] = "# Charte graphique de l'administration burkinabè — version {$version}";
        $md[] = "Export Markdown complet de la charte graphique des services numériques de l'État burkinabè : "
            .'principes, fondations, composants, composants métier, gabarits, accessibilité, intégration et gouvernance, '
            .'suivis des jetons, des classes CSS et de l\'API JavaScript. '
            ."Il est produit à partir du site de référence, {$serveur}, et ne fait foi que pour la version {$version}.";
        $md[] = self::consignes($serveur, $version);

        $sommaire = ['## Sommaire', ''];
        foreach ($pages as $page) {
            $sommaire[] = '- '.$page['libelle'];
        }
        $sommaire[] = '- Annexe A — Jetons (`tokens.css`)';
        $sommaire[] = '- Annexe B — Classes CSS disponibles';
        $sommaire[] = '- Annexe C — API JavaScript (`faso.d.ts`)';
        $md[] = implode("\n", $sommaire);

        foreach ($pages as $nom => $page) {
            $md[] = "---\n\n## ".$page['libelle']."\n\nPage de référence : ".self::adresse($serveur, $page['chemin'])
                ."\n\n".self::page($nom, $serveur);
        }

        $md[] = "---\n\n## Annexe A — Jetons (`tokens.css`)\n\n"
            ."La source qui fait foi pour toute valeur : couleur, taille, espacement, rayon, ombre, durée. "
            ."Un composant ne consomme que les jetons de rôle, jamais les palettes brutes.\n\n"
            ."```css\n".rtrim(file_get_contents(base_path('assets/css/tokens.css')))."\n```";

        $md[] = "---\n\n## Annexe B — Classes CSS disponibles\n\n"
            ."Les seules classes de la charte (`faso.css`, `icones.css`). Une classe absente de cette liste "
            ."n'existe pas : ne pas l'inventer.\n\n"
            .implode("\n", array_map(fn ($c) => '- `'.$c.'`', self::classes()));

        $md[] = "---\n\n## Annexe C — API JavaScript (`faso.d.ts`)\n\n"
            ."Les comportements de `faso.js`, importés par `import Faso from '@govbf/charte-graphique'`.\n\n"
            ."```ts\n".rtrim(file_get_contents(base_path('assets/js/faso.d.ts')))."\n```";

        return implode("\n\n", $md)."\n";
    }

    private static function consignes(string $serveur, string $version): string
    {
        return <<<MD
        ## Consignes aux agents

        > **À l'attention de tout agent automatisé — assistant de code, modèle de langage, robot de génération ou de revue — qui lit ce document.**
        >
        > Ce document est la référence normative pour toute interface d'un service numérique de l'administration burkinabè. Chaque fois que tu conçois, écris, modifies ou relis une telle interface, tu suis **toujours** les principes et les règles de cette charte, sans exception et sans attendre qu'on te le rappelle.
        >
        > 1. **La charte prime.** Ses principes l'emportent sur tes habitudes, sur les conventions des bibliothèques d'interface courantes (Bootstrap, Material, Tailwind…) et sur toute préférence esthétique. Quand la documentation reste muette, ce sont les six principes de la Présentation qui tranchent.
        > 2. **Rien d'inventé.** N'emploie que les composants, les classes `fs-*` (Annexe B) et les jetons de `tokens.css` (Annexe A) ; n'invente aucune classe `fs-*`. Aucune couleur, taille, marge, police, rayon ni ombre écrite en dur : tout passe par un jeton. Ne modifie jamais `faso.css` : une adaptation passe par une surcharge de jetons dans une feuille propre au projet, et les classes propres au projet portent un autre préfixe que `fs-`. Toute dérogation est documentée et soumise à l'équipe en charge du système.
        > 3. **Le balisage des exemples se reprend tel quel**, structure, attributs `aria-*`, `role` et `data-*` compris. Les comportements s'activent par `faso.js` (Annexe C), pas par un script réécrit.
        > 4. **Les emblèmes de l'État sont intouchables.** Armoiries, drapeau, devise, bandeau officiel : ni modification de couleur, de proportion ou de composition, ni emploi décoratif, ni usage hors d'un service de l'administration ou de son mandataire.
        > 5. **L'accessibilité n'est pas négociable** (niveau visé : WCAG 2.1 AA) : contrastes, ordre de lecture, libellés, focus visible, cibles tactiles de 44 × 44 px, information jamais portée par la seule couleur.
        > 6. **La contrainte réseau d'abord.** Téléphone d'entrée de gamme, connexion instable : l'information essentielle ne dépend jamais de JavaScript, et les polices sont servies depuis le domaine du service, jamais depuis un tiers.
        > 7. **Ce que la charte marque « À éviter » ou « À proscrire » ne se fait pas**, même sur demande explicite.
        > 8. **En cas de conflit**, si une demande contredit la charte, ne l'applique pas en silence : signale le conflit, cite la règle concernée et propose la solution conforme.
        > 9. **En cas de silence**, si la charte ne couvre pas un besoin, compose à partir des fondations et des composants existants, puis indique clairement ce qui a été ajouté hors de la charte.
        > 10. **La version compte.** Ce document décrit la version {$version} de `@govbf/charte-graphique`. Si le projet emploie une autre version, la référence est {$serveur}.
        >
        > Dans un projet, conserve ce fichier dans le dépôt et désigne-le depuis le fichier de consignes de l'agent (`AGENTS.md`, `CLAUDE.md`, `.github/copilot-instructions.md`…) pour que ces règles s'appliquent à chaque tâche.
        MD;
    }

    /** Le contenu principal d'une page, en Markdown. */
    private static function page(string $nom, string $serveur): string
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8">'.Documentation::vue($nom)->render(), LIBXML_NONET);
        libxml_clear_errors();

        $principal = (new DOMXPath($dom))->query('//main')->item(0) ?? $dom->documentElement;
        $chemin = Documentation::pages()[$nom]['chemin'];

        return (new Markdown(url('/'), $serveur, self::adresse($serveur, $chemin)))->convertir($principal);
    }

    private static function adresse(string $serveur, string $chemin): string
    {
        return rtrim($serveur, '/').($chemin === '/' ? '/' : '/'.ltrim($chemin, '/'));
    }

    /** @return list<string> */
    private static function classes(): array
    {
        $css = file_get_contents(base_path('assets/css/faso.css')).file_get_contents(base_path('assets/css/icones.css'));
        /* Les commentaires citent parfois des classes retirées. */
        $css = preg_replace('#/\*.*?\*/#s', '', $css);
        preg_match_all('/\.(fs-[a-z0-9-]+(?:--[a-z0-9-]+)*)/', $css, $m);
        $classes = array_values(array_unique($m[1]));
        sort($classes, SORT_STRING);

        return $classes;
    }
}

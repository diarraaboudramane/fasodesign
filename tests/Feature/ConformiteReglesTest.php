<?php

namespace Tests\Feature;

use App\Conformite\Collecte\CollecteAdresse;
use App\Conformite\Collecte\CollecteArchive;
use App\Conformite\Collecte\GardeReseau;
use App\Conformite\Collecte\Recuperateur;
use App\Conformite\Collecte\Reponse;
use App\Conformite\Moteur;
use App\Conformite\Reference;
use App\Support\Documentation;
use RuntimeException;
use Tests\FabriqueZip;
use Tests\TestCase;

/**
 * Les règles, éprouvées sur deux projets d'exemple : l'un conforme, l'autre
 * qui commet un écart de chaque famille. C'est le critère de réception de
 * la note de projet.
 */
class ConformiteReglesTest extends TestCase
{
    public static function pageConforme(string $titre = 'Demander un acte de naissance'): string
    {
        $v = Documentation::version();
        $serveur = config('charte.serveur');

        return <<<HTML
        <!doctype html>
        <html lang="fr">
        <head>
          <link rel="stylesheet" href="{$serveur}/{$v}/css/tokens.css">
          <link rel="stylesheet" href="{$serveur}/{$v}/css/faso.css">
          <link rel="stylesheet" href="{$serveur}/{$v}/css/icones.css">
          <link rel="stylesheet" href="/css/projet.css">
        </head>
        <body class="fs-espace-public">
          <div class="fs-officiel"><div class="fs-conteneur">Site officiel de l'administration burkinabè</div></div>
          <header class="fs-entete">
            <a class="fs-marque" href="/"><img src="/img/armoiries.svg" alt="Armoiries du Burkina Faso"></a>
          </header>
          <main>
            <h1>{$titre}</h1>
            <h2>Pièces à fournir</h2>
            <form>
              <label class="fs-label" for="nom">Nom</label>
              <input class="fs-saisie" id="nom" name="nom">
            </form>
            <a class="fs-btn fs-btn--principal" href="/demarche">Commencer la démarche en ligne</a>
            <dialog class="fs-modale"><button class="fs-btn fs-btn--principal">Continuer</button></dialog>
          </main>
        </body>
        </html>
        HTML;
    }

    private function statuts(array $bilan): array
    {
        return array_column($bilan['resultats'], 'statut', 'id');
    }

    private function analyserArchive(array $fichiers): array
    {
        return (new Moteur)->analyser((new CollecteArchive)->collecter(FabriqueZip::creer($fichiers), 'projet.zip'));
    }

    public function test_un_projet_conforme_obtient_conforme(): void
    {
        $bilan = $this->analyserArchive([
            'projet/package.json' => json_encode(['dependencies' => ['@govbf/charte-graphique' => '^'.Documentation::version()]]),
            'projet/index.html' => self::pageConforme(),
            'projet/css/projet.css' => ".projet-carte { color: var(--texte); }\n:root { --projet-accent: #123456; }",
            'projet/node_modules/autre/index.js' => 'ignoré',
        ]);

        $this->assertSame('conforme', $bilan['niveau'], json_encode(array_filter($bilan['resultats'], fn ($r) => $r['statut'] === 'non_respectee'), JSON_UNESCAPED_UNICODE));
        $statuts = $this->statuts($bilan);
        $this->assertSame('respectee', $statuts['integration.version']);
        $this->assertSame('respectee', $statuts['redaction.libelles'], '« Continuer » est admis dans une boîte de confirmation');
        $this->assertSame('non_verifiable', $statuts['confiance.domaine'], 'le domaine ne se lit pas dans une archive');
        $this->assertSame(1, array_sum($bilan['perimetre']['ignores']));
    }

    public function test_chaque_famille_d_ecart_est_signalee_avec_sa_gravite(): void
    {
        $tokensModifie = str_replace('--action:', '--action: #ff00ff; --ancienne-action:', file_get_contents(base_path('assets/css/tokens.css')));

        $bilan = $this->analyserArchive([
            'projet/package.json' => json_encode(['dependencies' => ['@govbf/charte-graphique' => '2.0.0']]),
            'projet/index.html' => <<<'HTML'
                <!doctype html>
                <html>
                <head>
                  <link rel="stylesheet" href="css/faso.css">
                  <link rel="stylesheet" href="css/tokens.css">
                  <script src="https://cdn.jsdelivr.net/npm/outil.js"></script>
                </head>
                <body>
                  <div class="fs-marque"><img src="logo.png"></div>
                  <h1>Accueil</h1><h1>Encore</h1><h2>Section</h2><h4>Trop bas</h4>
                  <input class="fs-saisie" name="telephone">
                  <a class="fs-bouton" href="/x">Cliquez ici</a>
                </body>
                </html>
                HTML,
            'projet/css/tokens.css' => $tokensModifie,
            'projet/css/projet.css' => ".fs-btn { background: #e30613; }\n.titre { color: #333; }",
        ]);

        $this->assertSame('non_conforme', $bilan['niveau']);
        $parId = array_column($bilan['resultats'], null, 'id');

        foreach ([
            'integration.feuilles' => 'bloquante',      // icones.css absente, ordre inversé
            'integration.version' => 'majeure',         // 2.0.0
            'integration.non-modifiee' => 'bloquante',  // tokens.css retouché
            'identite.bloc-marque' => 'bloquante',      // pas un lien
            'identite.bandeau' => 'bloquante',
            'couleurs.en-dur' => 'majeure',
            'couleurs.anciennes' => 'majeure',
            'classes.existantes' => 'majeure',          // .fs-bouton
            'classes.surcharge' => 'majeure',           // .fs-btn { … }
            'accessibilite.langue' => 'bloquante',
            'accessibilite.alt' => 'bloquante',
            'accessibilite.titres' => 'majeure',
            'accessibilite.etiquettes' => 'bloquante',
            'redaction.libelles' => 'mineure',
            'confiance.tiers' => 'majeure',             // cdn.jsdelivr.net
        ] as $id => $gravite) {
            $this->assertSame('non_respectee', $parId[$id]['statut'], $id);
            $this->assertSame($gravite, $parId[$id]['gravite'], $id);
            $this->assertNotEmpty($parId[$id]['ecarts'][0]['ou'], $id);
            $this->assertNotEmpty($parId[$id]['lien'], $id);
        }

        $this->assertStringContainsString('projet/css/projet.css, ligne 2', json_encode($parId['couleurs.en-dur']['ecarts'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->assertStringContainsString('.fs-bouton', $parId['classes.existantes']['ecarts'][0]['extrait']);
    }

    public function test_une_teinte_citee_dans_le_texte_n_est_pas_un_emploi(): void
    {
        $bilan = $this->analyserArchive([
            'journal.html' => '<p>Avant la 3.0, le rouge valait <code>#e30613</code>.</p>',
            'carte.html' => '<div style="background: #e30613">Carte</div>',
        ]);
        $teintes = array_column($bilan['resultats'], null, 'id')['couleurs.anciennes'];

        $this->assertSame(1, $teintes['occurrences']);
        $this->assertStringStartsWith('carte.html', $teintes['ecarts'][0]['ou']);
    }

    public function test_une_copie_servie_par_le_projet_dit_sa_version(): void
    {
        $bilan = $this->analyserArchive([
            'public/charte/faso.css' => file_get_contents(base_path('archives/2.0.0/assets/css/faso.css')),
        ]);
        $version = array_column($bilan['resultats'], null, 'id')['integration.version'];

        $this->assertSame('non_respectee', $version['statut']);
        $this->assertStringContainsString('2.0.0', $version['ecarts'][0]['extrait']);
    }

    public function test_chaque_regle_renvoie_a_une_section_existante_de_la_charte(): void
    {
        foreach (Moteur::regles() as $regle) {
            [$page, $ancre] = array_pad(explode('#', $regle->lien(), 2), 2, null);
            $this->assertArrayHasKey($page, config('charte.pages'), $regle->id());
            if ($ancre) {
                $html = file_get_contents(resource_path("views/pages/{$page}.blade.php"));
                $this->assertStringContainsString('id="'.$ancre.'"', $html, $regle->id().' → '.$regle->lien());
            }
        }
    }

    public function test_un_site_se_parcourt_par_son_adresse(): void
    {
        $pages = [
            'https://service.gov.bf/' => ['text/html', str_replace('href="/demarche"', 'href="/demarche"', self::pageConforme())],
            'https://service.gov.bf/demarche' => ['text/html', self::pageConforme('Démarche')],
            'https://service.gov.bf/css/projet.css' => ['text/css', '.projet { color: var(--texte); }'],
        ];
        $v = Documentation::version();
        foreach (['tokens', 'faso', 'icones'] as $nom) {
            $pages[config('charte.serveur')."/{$v}/css/{$nom}.css"] = ['text/css', file_get_contents(base_path("assets/css/{$nom}.css"))];
        }

        $faux = new class($pages, app(GardeReseau::class)) extends Recuperateur
        {
            public function __construct(private array $pages, GardeReseau $garde) { parent::__construct($garde); }

            public function recuperer(string $adresse): Reponse
            {
                if (! isset($this->pages[$adresse])) {
                    throw new RuntimeException('introuvable');
                }

                return new Reponse($adresse, 200, $this->pages[$adresse][0], $this->pages[$adresse][1]);
            }
        };

        $corpus = (new CollecteAdresse($faux))->collecter('https://service.gov.bf/');
        $bilan = (new Moteur)->analyser($corpus);
        $statuts = $this->statuts($bilan);

        $this->assertSame(2, $bilan['perimetre']['pages']);
        $this->assertSame($v, $bilan['perimetre']['version_declaree']);
        $this->assertSame('respectee', $statuts['confiance.domaine']);
        $this->assertSame('respectee', $statuts['performance.poids']);
        $this->assertSame('respectee', $statuts['integration.non-modifiee'], 'les feuilles servies par la charte sont reconnues');
        $this->assertSame('conforme', $bilan['niveau'], json_encode(array_filter($bilan['resultats'], fn ($r) => $r['statut'] === 'non_respectee'), JSON_UNESCAPED_UNICODE));
        /* L'image du bloc-marque et le lien du formulaire ne sont pas des pages. */
        $this->assertNotContains('https://service.gov.bf/img/armoiries.svg', array_map(fn ($d) => $d->chemin, $corpus->documents('html')));
    }

    public function test_la_reference_reconnait_les_fichiers_publies(): void
    {
        $empreintes = Reference::empreintes();

        $this->assertContains('faso.css '.Documentation::version(), $empreintes);
        $this->assertContains('tokens.css 2.0.0', $empreintes, 'la version gelée est reconnue');
        $this->assertArrayHasKey('fs-btn', Reference::classes());
        $this->assertArrayHasKey('fs-tissage--pied-de-poule', Reference::classes());
    }
}

<?php

namespace Tests\Feature;

use App\Conformite\Collecte\CollecteRendu;
use App\Conformite\Collecte\GardeReseau;
use App\Conformite\Corpus;
use App\Conformite\Moteur;
use App\Jobs\AnalyserProjet;
use App\Models\Analyse;
use App\Models\User;
use App\Support\Documentation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Le rendu JavaScript côté PHP : de la sortie du navigateur au bilan. Le
 * navigateur lui-même, et sa garde réseau, sont éprouvés par tests/rendu.js.
 */
class ConformiteRenduTest extends TestCase
{
    use RefreshDatabase;

    /** Ce que rend outils/rendu/rendu.js pour un site React conforme, sauf un texte trop pâle. */
    public static function sortie(): array
    {
        $v = Documentation::version();
        $serveur = config('charte.serveur');

        return [
            'pages' => [['adresse' => 'https://application.gov.bf/', 'html' => ConformiteReglesTest::pageConforme()]],
            'ressources' => array_map(fn ($nom) => [
                'adresse' => "{$serveur}/{$v}/css/{$nom}.css",
                'depuis' => 'https://application.gov.bf/',
                'type' => 'css',
                'corps' => file_get_contents(base_path("assets/css/{$nom}.css")),
            ], ['tokens', 'faso', 'icones']),
            'poidsPremierePage' => 210 * 1024,
            'contrastes' => [
                'ecarts' => [['element' => 'main > p.aide', 'texte' => 'Texte trop pâle', 'rapport' => 1.64, 'seuil' => 4.5]],
                'mesures' => 42,
                'impossibles' => 1,
            ],
            'nonAtteints' => [],
            'refus' => ['10.0.0.8:80 : 10.0.0.8 mène au réseau interne'],
        ];
    }

    private function faux(): CollecteRendu
    {
        return new class(app(GardeReseau::class)) extends CollecteRendu
        {
            public function collecter(string $adresse): Corpus
            {
                return $this->depuisSortie(ConformiteRenduTest::sortie(), $adresse);
            }
        };
    }

    public function test_la_page_rendue_est_analysee_et_ses_contrastes_mesures(): void
    {
        $corpus = $this->faux()->collecter('https://application.gov.bf/');
        $bilan = (new Moteur)->analyser($corpus);
        $parId = array_column($bilan['resultats'], null, 'id');

        $this->assertTrue($bilan['perimetre']['rendu']);
        $this->assertFalse($bilan['perimetre']['sans_javascript']);
        $this->assertSame(Documentation::version(), $bilan['perimetre']['version_declaree']);

        $this->assertSame('non_respectee', $parId['accessibilite.contrastes']['statut']);
        $this->assertStringContainsString('1,64:1 pour 4,5:1', $parId['accessibilite.contrastes']['ecarts'][0]['extrait']);
        $this->assertStringContainsString('42 élément(s)', $parId['accessibilite.contrastes']['note']);
        $this->assertStringContainsString('images et polices comprises', $parId['performance.poids']['note']);
        $this->assertSame('reserves', $bilan['niveau'], 'un contraste insuffisant est un écart majeur');

        /* Les contrastes étant mesurés, ils ne sont plus dans « non vérifié ». */
        $this->assertNotContains('Les contrastes de couleur réellement affichés.', $bilan['perimetre']['hors_de_portee']);
        $this->assertStringContainsString('réseau interne', implode(' ', $bilan['perimetre']['non_atteints']));
    }

    public function test_sans_rendu_les_contrastes_restent_non_verifiables(): void
    {
        $bilan = (new Moteur)->analyser(new Corpus('adresse', 'https://application.gov.bf/'));

        $this->assertSame('non_verifiable', array_column($bilan['resultats'], null, 'id')['accessibilite.contrastes']['statut']);
        $this->assertContains('Les contrastes de couleur réellement affichés.', $bilan['perimetre']['hors_de_portee']);
    }

    public function test_une_analyse_avec_rendu_passe_par_le_navigateur(): void
    {
        Mail::fake();
        $this->app->instance(CollecteRendu::class, $this->faux());
        $compte = new User(['name' => 'Awa', 'email' => 'awa@sante.gov.bf', 'entite' => 'Santé', 'password' => 'un mot de passe solide']);
        $compte->forceFill(['statut' => 'actif', 'email_verified_at' => now()])->save();

        $analyse = new Analyse(['mode' => 'adresse', 'cible' => 'https://application.gov.bf/', 'version_charte' => Documentation::version()]);
        $analyse->rendu = true;
        $analyse->user()->associate($compte);
        $analyse->save();

        dispatch_sync(new AnalyserProjet($analyse->id));
        $analyse->refresh();

        $this->assertSame('terminee', $analyse->etat);
        $this->assertSame('Site en ligne, rendu par un navigateur qui exécute son JavaScript', $analyse->libelleMode());
        $this->actingAs($compte)->get(route('conformite.bilan', $analyse))->assertOk()->assertSee('Contrastes du texte affiché');
    }

    public function test_le_rendu_demande_sans_navigateur_installe_est_refuse_clairement(): void
    {
        config(['charte.conformite.rendu.chrome' => null]);
        $compte = new User(['name' => 'Awa', 'email' => 'awa@sante.gov.bf', 'entite' => 'Santé', 'password' => 'un mot de passe solide']);
        $compte->forceFill(['statut' => 'actif', 'email_verified_at' => now()])->save();

        $this->actingAs($compte)->get('/conformite/analyses')->assertOk()->assertSee('pas installé sur ce serveur', false);
        $this->actingAs($compte)
            ->post('/conformite/analyses', ['mode' => 'adresse', 'adresse' => 'https://application.gov.bf/', 'rendu' => '1'])
            ->assertSessionHasErrors('rendu');

        $this->assertSame(0, Analyse::count());
    }
}

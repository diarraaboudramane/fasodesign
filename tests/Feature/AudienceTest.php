<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifierHumain;
use App\Support\Audience;
use App\Support\Documentation;
use App\Support\ResolveurDns;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AudienceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function visiter(string $ip, string $agent = 'Mozilla/5.0 Essai', string $adresse = '/fondations')
    {
        return $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->withHeader('User-Agent', $agent)
            ->get($adresse);
    }

    public function test_chaque_visiteur_distinct_est_compte_une_fois_dans_le_mois(): void
    {
        $this->visiter('203.0.113.1');
        $this->visiter('203.0.113.1', adresse: '/composants');
        $this->visiter('203.0.113.2');

        $this->assertSame(2, Audience::mois());
        $this->assertSame(2, Audience::enLigne());
    }

    public function test_un_visiteur_inactif_n_est_plus_en_ligne_mais_reste_compte_dans_le_mois(): void
    {
        $this->visiter('203.0.113.1');

        /* La présence est datée : six minutes plus tard, hors de la fenêtre. */
        $presence = Cache::get('audience.presence');
        Cache::put('audience.presence', array_map(fn () => time() - 6 * 60, $presence));

        $this->assertSame(0, Audience::enLigne());
        $this->assertSame(1, Audience::mois());
    }

    public function test_un_visiteur_non_verifie_n_est_pas_compte(): void
    {
        $this->defaultCookies = [];

        $this->visiter('203.0.113.1')->assertRedirect();

        $this->assertSame(0, Audience::mois());
    }

    public function test_un_moteur_de_recherche_n_est_pas_compte(): void
    {
        $this->defaultCookies = [];
        $this->app->instance(ResolveurDns::class, new class extends ResolveurDns
        {
            public function inverse(string $ip): ?string { return 'crawl-66-249-66-1.googlebot.com'; }

            public function direct(string $hote): array { return ['66.249.66.1']; }
        });

        $this->visiter('66.249.66.1', 'Googlebot/2.1')->assertOk();

        $this->assertSame(0, Audience::mois());
    }

    public function test_aucune_adresse_ip_n_est_gardee(): void
    {
        $this->visiter('203.0.113.77');

        $this->assertStringNotContainsString('203.0.113.77', serialize(Cache::get('audience.presence')));
        $this->assertNotContains('203.0.113.77', array_keys(Cache::get('audience.presence')));
    }

    public function test_l_accueil_affiche_les_chiffres(): void
    {
        $this->visiter('203.0.113.1');
        $this->visiter('203.0.113.2');

        $this->visiter('203.0.113.3', adresse: '/')
            ->assertSee('3&nbsp;personnes en ligne', false)
            ->assertSee('3&nbsp;visiteurs en '.Audience::libelleMois(), false)
            ->assertSee('data-audience="/audience"', false)
            ->assertSee('assets/js/audience.js', false);
    }

    public function test_un_seul_visiteur_s_ecrit_au_singulier(): void
    {
        $this->visiter('203.0.113.1', adresse: '/')
            ->assertSee('1&nbsp;personne en ligne', false)
            ->assertSee('1&nbsp;visiteur en', false);
    }

    public function test_la_mise_a_jour_en_direct_ne_prolonge_pas_la_case(): void
    {
        $reponse = $this->visiter('203.0.113.1', adresse: '/audience');

        $reponse->assertOk()->assertJsonStructure(['en_ligne', 'mois', 'libelle_mois']);
        $this->assertSame([], array_filter($reponse->headers->getCookies(), fn ($c) => $c->getName() === VerifierHumain::COOKIE));
        $this->assertSame(1, $reponse->json('en_ligne'));
    }

    public function test_la_mise_a_jour_ne_compte_pas_un_visiteur_non_verifie(): void
    {
        $this->defaultCookies = [];

        $this->visiter('203.0.113.1', adresse: '/audience')->assertOk()->assertJson(['en_ligne' => 0, 'mois' => 0]);
    }

    public function test_le_site_exporte_n_affiche_pas_d_audience(): void
    {
        config(['charte.export' => true]);

        $html = Documentation::vue('index')->render();

        $this->assertStringNotContainsString('data-audience', $html);
        $this->assertStringNotContainsString('audience.js', $html);
    }

    public function test_la_mesure_peut_etre_coupee(): void
    {
        config(['charte.audience.active' => false]);

        $this->visiter('203.0.113.1', adresse: '/')->assertDontSee('data-audience', false);
        $this->assertSame(0, Audience::mois());
    }

    public function test_le_script_n_est_pas_diffuse_aux_services(): void
    {
        $this->get('/'.Documentation::version().'/js/audience.js')->assertNotFound();
    }
}

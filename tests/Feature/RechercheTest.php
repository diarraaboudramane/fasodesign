<?php

namespace Tests\Feature;

use App\Support\Documentation;
use App\Support\Recherche;
use Tests\TestCase;

class RechercheTest extends TestCase
{
    public function test_l_accueil_propose_la_recherche(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<form class="fs-champ" role="search" method="get" action="'.route('recherche').'">', false)
            ->assertSee('id="recherche-accueil" name="q"', false);
    }

    public function test_le_site_exporte_ne_propose_pas_de_recherche(): void
    {
        config(['charte.export' => true]);

        $this->assertStringNotContainsString('role="search"', Documentation::vue('index')->render());
    }

    public function test_chaque_page_de_la_documentation_est_indexee(): void
    {
        $pages = array_unique(array_column(Recherche::index(), 'page'));
        sort($pages);
        $attendues = array_keys(config('charte.pages'));
        sort($attendues);

        $this->assertSame($attendues, $pages);
    }

    public function test_un_resultat_mene_a_la_section(): void
    {
        $reponse = $this->get('/recherche?q=typographie');

        $reponse->assertOk();
        $reponse->assertSee('href="'.route('fondations').'#typographie"', false);
    }

    public function test_la_recherche_ignore_accents_et_casse(): void
    {
        $sans = array_column(Recherche::chercher('ACCESSIBILITE'), 'url');
        $avec = array_column(Recherche::chercher('accessibilité'), 'url');

        $this->assertNotEmpty($sans);
        $this->assertSame($avec, $sans);
    }

    public function test_tous_les_mots_doivent_figurer_dans_la_section(): void
    {
        $un = Recherche::chercher('Laravel');
        $deux = Recherche::chercher('Laravel Livewire');

        $this->assertNotEmpty($deux);
        $this->assertLessThanOrEqual(count($un), count($deux));
        $this->assertSame([], Recherche::chercher('Laravel zzqxwv'));
    }

    public function test_les_termes_techniques_se_trouvent_tels_quels(): void
    {
        $urls = array_column(Recherche::chercher('wire:ignore'), 'url');

        $this->assertContains(true, array_map(fn ($u) => str_starts_with($u, route('integration')), $urls));
    }

    public function test_les_termes_trouves_sont_surlignes(): void
    {
        $this->get('/recherche?q=contraste')->assertSee('<mark>contraste</mark>', false);
    }

    public function test_la_requete_est_echappee(): void
    {
        $this->get('/recherche?q='.urlencode('<script>alert(1)</script>'))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_aucun_resultat_propose_des_pistes(): void
    {
        $this->get('/recherche?q=zzqxwv')
            ->assertOk()
            ->assertSee('Aucun résultat pour « zzqxwv ».')
            ->assertSee('Quelques pistes');
    }

    public function test_sans_requete_la_page_affiche_le_champ_seul(): void
    {
        $this->get('/recherche')
            ->assertOk()
            ->assertSee('id="recherche"', false)
            ->assertDontSee('role="status"', false);
    }

    public function test_les_resultats_ne_sont_pas_indexes_par_les_moteurs(): void
    {
        $this->get('/recherche?q=bouton')->assertSee('<meta name="robots" content="noindex, follow">', false);
    }

    public function test_la_recherche_ne_pose_pas_de_cookie(): void
    {
        $this->assertSame([], $this->get('/recherche?q=bouton')->headers->getCookies());
    }
}

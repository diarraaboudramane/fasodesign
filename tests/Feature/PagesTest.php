<?php

namespace Tests\Feature;

use App\Http\Middleware\EnTetesDeSecurite;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PagesTest extends TestCase
{
    /** @return array<string, array{string, string}> */
    public static function pages(): array
    {
        $config = require __DIR__.'/../../config/charte.php';
        $cas = [];
        foreach ($config['pages'] as $nom => $page) {
            $cas[$nom] = [$nom, $page['chemin']];
        }

        return $cas;
    }

    #[DataProvider('pages')]
    public function test_chaque_page_repond_et_se_signale_dans_le_menu(string $nom, string $chemin): void
    {
        $reponse = $this->get($chemin);

        $reponse->assertOk();
        $reponse->assertViewIs("pages.{$nom}");
        $reponse->assertSee('<a class="fs-evitement" href="#contenu">', false);

        /* Parmi les liens vers les pages, un seul porte aria-current="page" :
           le sien. Les exemples de composants en portent d'autres, vers « # ». */
        $html = $reponse->getContent();
        $adresses = array_map(fn ($n) => route($n), array_keys(config('charte.pages')));
        preg_match_all('/<a [^>]*href="([^"]*)"[^>]*aria-current="page"/', $html, $courantes);
        $courantes = array_values(array_unique(array_intersect($courantes[1], $adresses)));
        $this->assertSame([route($nom)], $courantes);
    }

    public function test_les_vues_et_la_configuration_se_correspondent(): void
    {
        $vues = array_map(
            fn ($f) => basename($f, '.blade.php'),
            glob(resource_path('views/pages/*.blade.php'))
        );
        sort($vues);
        $pages = array_keys(config('charte.pages'));
        sort($pages);

        $this->assertSame($pages, $vues);
    }

    #[DataProvider('pages')]
    public function test_aucune_page_ne_pose_de_cookie(string $nom, string $chemin): void
    {
        $reponse = $this->get($chemin);

        $this->assertSame([], $reponse->headers->getCookies());
        $this->assertNull($reponse->headers->get('Set-Cookie'));
    }

    public function test_les_en_tetes_de_securite_sont_poses(): void
    {
        $reponse = $this->get('/');

        $reponse->assertHeader('Content-Security-Policy', EnTetesDeSecurite::CSP);
        $reponse->assertHeader('X-Content-Type-Options', 'nosniff');
        $reponse->assertHeader('X-Frame-Options', 'DENY');
        $reponse->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $reponse->assertHeader('TDM-Reservation', '1');
        $this->assertStringContainsString('must-revalidate', $reponse->headers->get('Cache-Control'));

        /* Le site doit rester trouvable par les moteurs. */
        $this->assertStringNotContainsString('noindex', $reponse->headers->get('X-Robots-Tag'));
    }

    public function test_une_adresse_inconnue_recoit_la_page_404_de_la_charte(): void
    {
        $reponse = $this->get('/cette-page-n-existe-pas');

        $reponse->assertNotFound();
        $reponse->assertSee('Erreur 404');
        $reponse->assertSee(route('composants'), false);
        $reponse->assertHeader('Content-Security-Policy', EnTetesDeSecurite::CSP);
        $this->assertSame([], $reponse->headers->getCookies());
    }

    public function test_les_anciennes_adresses_en_html_menent_a_la_page(): void
    {
        $this->get('/fondations.html')->assertStatus(301)->assertRedirect(route('fondations'));
        $this->get('/index.html')->assertStatus(301)->assertRedirect(route('index'));
        $this->get('/inconnue.html')->assertNotFound();
    }

    public function test_les_extraits_blade_de_la_documentation_restent_du_texte(): void
    {
        /* La page Intégration montre du code Blade : il doit s'afficher,
           non s'exécuter. */
        $html = $this->get('/integration')->getContent();

        $this->assertStringContainsString('{{-- resources/views/layouts/app.blade.php --}}', $html);
        $this->assertStringContainsString("{{ asset('charte/css/faso.css') }}", $html);
        $this->assertStringContainsString('@csrf', $html);
    }
}

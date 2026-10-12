<?php

namespace Tests\Feature;

use App\Support\Documentation;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SiteTest extends TestCase
{
    public function test_robots_txt_est_celui_qu_engendre_l_outillage(): void
    {
        $reponse = $this->get('/robots.txt');

        $reponse->assertOk();
        $this->assertStringStartsWith('text/plain', $reponse->headers->get('Content-Type'));

        /* Le même fichier, à la ligne Sitemap près, qui désigne le domaine
           qui répond et non le domaine officiel écrit dans le fichier. */
        $attendu = preg_replace('/^Sitemap: .*$/m', 'Sitemap: http://localhost/sitemap.xml',
            file_get_contents(base_path('hebergement/robots.txt')));
        $this->assertSame($attendu, $reponse->getContent());
    }

    public function test_un_serveur_de_test_refuse_toute_indexation(): void
    {
        config(['charte.indexable' => false]);

        $robots = $this->get('/robots.txt')->getContent();
        $this->assertStringEndsWith("User-agent: *\nDisallow: /\n", $robots);
        $this->assertStringNotContainsString('Sitemap:', $robots);
        $this->assertSame(1, substr_count($robots, 'User-agent: *'));
        /* Les refus nommés restent, et l'exception des assistants avec eux. */
        $this->assertStringContainsString("User-agent: GPTBot", $robots);
        $this->assertStringContainsString("User-agent: DuckAssistBot\nAllow: /charte.md\nDisallow: /", $robots);
        /* ChatGPT et Claude gardent leur groupe : « * » ne les atteint pas. */
        $this->assertMatchesRegularExpression("/User-agent: ChatGPT-User\n(?:#.*\n)?User-agent: Claude-User\n(?!Disallow: \/\n)/", $robots);
        $this->get('/')->assertHeader('X-Robots-Tag', 'noindex, nofollow, noai, noimageai');
        $this->get('/assets/css/faso.css')->assertHeader('X-Robots-Tag', 'noindex, nofollow, noai, noimageai');
    }

    public function test_le_site_officiel_reste_indexable_par_defaut(): void
    {
        $this->assertTrue(config('charte.indexable'));
        $this->get('/')->assertHeader('X-Robots-Tag', 'noai, noimageai');
    }

    public function test_le_plan_du_site_annonce_chaque_page_et_rien_d_autre(): void
    {
        $reponse = $this->get('/sitemap.xml');

        $reponse->assertOk();
        preg_match_all('#<loc>([^<]+)</loc>#', $reponse->getContent(), $adresses);
        $attendues = array_map(fn ($nom) => route($nom), array_keys(config('charte.pages')));

        $this->assertSame($attendues, $adresses[1]);
    }

    public function test_version_txt_annonce_la_version_du_paquet(): void
    {
        $this->get('/VERSION.txt')
            ->assertOk()
            ->assertSee('Version '.Documentation::version())
            ->assertSee('/'.Documentation::version().'/css/faso.css');
    }

    public function test_l_export_ecrit_des_pages_aux_liens_relatifs(): void
    {
        $sortie = sys_get_temp_dir().'/charte-graphique-export-'.getmypid();
        File::deleteDirectory($sortie);

        $this->artisan('charte:exporter', ['sortie' => $sortie])->assertSuccessful();

        $attendus = array_map(fn ($nom) => "{$nom}.html", array_keys(config('charte.pages')));
        $attendus[] = '404.html';
        $ecrits = array_map('basename', glob("{$sortie}/*.html"));
        sort($attendus);
        sort($ecrits);
        $this->assertSame($attendus, $ecrits);

        foreach (glob("{$sortie}/*.html") as $fichier) {
            $html = file_get_contents($fichier);
            $this->assertStringNotContainsString(config('app.url'), $html, basename($fichier));
            $this->assertStringContainsString('href="assets/css/faso.css"', $html);
        }

        $this->assertStringContainsString(
            '<li><a href="fondations.html" aria-current="page">Fondations</a></li>',
            file_get_contents("{$sortie}/fondations.html")
        );

        /* Le Markdown part avec le site, ses liens restent absolus. */
        $this->assertStringContainsString('href="charte.md"', file_get_contents("{$sortie}/fondations.html"));
        $markdown = file_get_contents("{$sortie}/charte.md");
        $this->assertStringContainsString('## Consignes aux agents', $markdown);
        $this->assertStringContainsString('('.config('charte.serveur').'/fondations)', $markdown);
        $this->assertStringNotContainsString('](fondations.html', $markdown);

        File::deleteDirectory($sortie);
    }
}

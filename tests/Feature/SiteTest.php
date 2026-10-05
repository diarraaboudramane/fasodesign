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
        $this->assertSame(file_get_contents(base_path('hebergement/robots.txt')), $reponse->getContent());
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
        $sortie = sys_get_temp_dir().'/fasodesign-export-'.getmypid();
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

        File::deleteDirectory($sortie);
    }
}

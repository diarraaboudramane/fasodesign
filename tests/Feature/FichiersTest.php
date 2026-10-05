<?php

namespace Tests\Feature;

use App\Support\Documentation;
use Tests\TestCase;

class FichiersTest extends TestCase
{
    public function test_les_feuilles_de_styles_sont_servies_comme_telles(): void
    {
        /* Servie en text/plain, une feuille est refusée par le navigateur
           à cause de nosniff, et la page s'affiche sans aucun style. */
        $reponse = $this->get('/assets/css/faso.css');

        $reponse->assertOk();
        $this->assertStringStartsWith('text/css', $reponse->headers->get('Content-Type'));
        $this->assertSame(file_get_contents(base_path('assets/css/faso.css')), $reponse->streamedContent());
    }

    public function test_les_scripts_sont_servis_comme_tels(): void
    {
        $reponse = $this->get('/assets/js/faso.js');

        $reponse->assertOk();
        $this->assertMatchesRegularExpression('#^(application|text)/javascript#', $reponse->headers->get('Content-Type'));
    }

    public function test_polices_et_emblemes_sont_consommables_depuis_un_autre_domaine(): void
    {
        foreach (['/assets/polices/inter.woff2', '/assets/img/armoiries.svg'] as $adresse) {
            $reponse = $this->get($adresse);

            $reponse->assertOk();
            $reponse->assertHeader('Access-Control-Allow-Origin', '*');
            $this->assertStringContainsString('immutable', $reponse->headers->get('Cache-Control'));
        }
    }

    public function test_archives_et_ressources_android_sont_servies(): void
    {
        $this->get('/archives/v1-avril-2026/charte_graphique_burkina.html')->assertOk();
        $this->get('/android/values/couleurs.xml')->assertOk();
    }

    public function test_on_ne_sort_pas_du_dossier_annonce(): void
    {
        $this->get('/assets/..%2F.env.example')->assertNotFound();
        $this->get('/assets/..%2Fcomposer.json')->assertNotFound();
        $this->get('/assets/css/..%2F..%2Fpackage.json')->assertNotFound();
        $this->get('/vendor/autoload.php')->assertNotFound();
        $this->get('/assets/css/inexistant.css')->assertNotFound();
    }

    public function test_la_diffusion_versionnee_est_figee(): void
    {
        $version = Documentation::version();
        $reponse = $this->get("/{$version}/css/faso.css");

        $reponse->assertOk();
        $reponse->assertHeader('Cache-Control', 'immutable, max-age=31536000, public');
        $reponse->assertHeader('Access-Control-Allow-Origin', '*');
    }

    public function test_la_diffusion_versionnee_ne_sert_que_la_version_courante(): void
    {
        $this->get('/0.0.1/css/faso.css')->assertNotFound();
    }

    public function test_l_outillage_de_la_documentation_n_est_pas_diffuse(): void
    {
        $version = Documentation::version();

        $this->get("/{$version}/css/docs.css")->assertNotFound();
        $this->get("/{$version}/js/docs.js")->assertNotFound();
        $this->get('/assets/css/docs.css')->assertOk();
    }
}

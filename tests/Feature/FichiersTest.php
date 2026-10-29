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
        foreach (['/assets/polices/roboto.woff2', '/assets/img/armoiries.svg'] as $adresse) {
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

    public function test_la_diffusion_versionnee_ne_sert_que_les_versions_publiees(): void
    {
        $this->get('/0.0.1/css/faso.css')->assertNotFound();
    }

    public function test_une_version_anterieure_reste_servie_telle_quelle(): void
    {
        /* Les services restés sur la 2.0.0 ne doivent rien voir changer :
           ses fichiers viennent de sa copie gelée, pas de assets/. */
        $reponse = $this->get('/2.0.0/css/tokens.css');

        $reponse->assertOk();
        $reponse->assertHeader('Cache-Control', 'immutable, max-age=31536000, public');
        $this->assertSame(file_get_contents(base_path('archives/2.0.0/assets/css/tokens.css')), $reponse->streamedContent());
        $this->assertStringContainsString('#e30613', $reponse->streamedContent());
        $this->get('/2.0.0/polices/inter.woff2')->assertOk();
        $this->get('/2.0.0/css/docs.css')->assertNotFound();
    }

    public function test_l_archive_npm_d_une_version_anterieure_est_servie(): void
    {
        foreach (['govbf-charte-graphique' => '', 'govbf-create-charte-graphique' => 'amorce'] as $nom => $dossier) {
            $tar = gzdecode($this->get("/npm/{$nom}-2.0.0.tgz")->assertOk()->streamedContent());
            $taille = octdec(rtrim(substr($tar, 124, 12), "\0"));
            $this->assertSame(
                file_get_contents(base_path(ltrim("archives/2.0.0/{$dossier}/package.json", '/'))),
                substr($tar, 512, $taille)
            );
            $this->assertStringContainsString('"version": "2.0.0"', substr($tar, 512, $taille));
        }
    }

    public function test_l_outillage_de_la_documentation_n_est_pas_diffuse(): void
    {
        $version = Documentation::version();

        $this->get("/{$version}/css/docs.css")->assertNotFound();
        $this->get("/{$version}/js/docs.js")->assertNotFound();
        $this->get('/assets/css/docs.css')->assertOk();
    }

    public function test_les_archives_npm_sont_servies_sans_verification(): void
    {
        /* npm ne coche pas de case : l'archive doit répondre même quand
           la vérification anti-robot est active. */
        config(['charte.verification.active' => true]);
        $version = Documentation::version();

        foreach (['govbf-charte-graphique' => '', 'govbf-create-charte-graphique' => 'amorce'] as $nom => $dossier) {
            $reponse = $this->get("/npm/{$nom}-{$version}.tgz");

            $reponse->assertOk();
            $tar = gzdecode($reponse->streamedContent());
            $this->assertNotFalse($tar, "{$nom} n'est pas une archive gzip");

            /* Le manifeste emporté est celui du dépôt, sous package/. */
            $this->assertSame('package/package.json', rtrim(substr($tar, 0, 100), "\0"));
            $taille = octdec(rtrim(substr($tar, 124, 12), "\0"));
            $this->assertSame(
                file_get_contents(base_path(ltrim("{$dossier}/package.json", '/'))),
                substr($tar, 512, $taille)
            );
        }
    }

    public function test_l_archive_npm_est_stable(): void
    {
        /* La même source donne les mêmes octets : sans quoi l'empreinte
           du package-lock.json des projets ne correspondrait plus. */
        $adresse = '/npm/govbf-charte-graphique-'.Documentation::version().'.tgz';

        $this->assertSame(
            $this->get($adresse)->streamedContent(),
            $this->get($adresse)->streamedContent()
        );
    }

    public function test_seuls_les_paquets_de_la_version_courante_sont_servis(): void
    {
        $this->get('/npm/govbf-charte-graphique-0.0.1.tgz')->assertNotFound();
        $this->get('/npm/autre-paquet-'.Documentation::version().'.tgz')->assertNotFound();
    }
}

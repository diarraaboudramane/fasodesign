<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class HebergementTest extends TestCase
{
    protected function tearDown(): void
    {
        TrustProxies::flushState();
        parent::tearDown();
    }

    private function redemarrer(array $config): void
    {
        config($config);
        (new AppServiceProvider($this->app))->boot();
    }

    public function test_derriere_un_proxy_tls_les_liens_restent_en_https(): void
    {
        $this->redemarrer(['app.url' => 'https://chartegraphique-21.mtdpce-test.gov.bf']);

        /* La requête arrive en http, comme depuis un proxy qui termine le TLS. */
        $html = $this->get('http://chartegraphique-21.mtdpce-test.gov.bf/fondations')->getContent();

        $this->assertStringContainsString('href="https://chartegraphique-21.mtdpce-test.gov.bf/assets/css/faso.css', $html);
        $this->assertStringNotContainsString('href="http://', $html);
    }

    public function test_l_adresse_du_visiteur_est_lue_derriere_le_mandataire_declare(): void
    {
        Route::get('/_adresse', fn (Request $r) => $r->ip());
        $this->redemarrer(['charte.mandataires' => '10.0.0.1']);

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
            ->withHeader('X-Forwarded-For', '203.0.113.5')
            ->get('/_adresse')->assertSee('203.0.113.5');
    }

    public function test_le_diagnostic_accepte_un_serveur_de_test_bien_regle(): void
    {
        config([
            'app.url' => 'https://chartegraphique-21.mtdpce-test.gov.bf',
            'app.debug' => false,
            'session.secure' => true,
            'charte.indexable' => false,
            'services.recaptcha.site_key' => '6LcVraieCleDuSiteDeTest00000000000000000',
            'services.recaptcha.secret_key' => '6LcVraieCleSecreteDeTest000000000000000',
            'charte.contact.destinataire' => 'equipe@exemple.bf',
        ]);

        $this->artisan('charte:diagnostic')->assertSuccessful();
    }

    public function test_le_diagnostic_refuse_un_modele_non_complete(): void
    {
        config([
            'app.url' => 'https://chartegraphique-21.mtdpce-test.gov.bf',
            'app.debug' => false,
            'session.secure' => true,
            'charte.indexable' => false,
            'services.recaptcha.site_key' => 'À-RENSEIGNER',
            'services.recaptcha.secret_key' => 'À-RENSEIGNER',
            'charte.contact.destinataire' => 'À-RENSEIGNER',
        ]);

        $this->artisan('charte:diagnostic')
            ->expectsOutputToContain('personne ne pourrait entrer')
            ->expectsOutputToContain('le formulaire refuserait tout envoi')
            ->assertFailed();
    }

    public function test_le_diagnostic_refuse_un_serveur_de_test_indexable(): void
    {
        config(['app.url' => 'https://chartegraphique-21.mtdpce-test.gov.bf', 'charte.indexable' => true]);

        $this->artisan('charte:diagnostic')
            ->expectsOutputToContain('concurrencerait le site officiel')
            ->assertFailed();
    }

    public function test_un_x_forwarded_for_d_ailleurs_n_est_pas_cru(): void
    {
        Route::get('/_adresse', fn (Request $r) => $r->ip());
        $this->redemarrer(['charte.mandataires' => '10.0.0.1']);

        /* Un visiteur qui écrit lui-même l'en-tête ne change pas d'adresse. */
        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.7'])
            ->withHeader('X-Forwarded-For', '203.0.113.5')
            ->get('/_adresse')->assertSee('198.51.100.7');
    }
}

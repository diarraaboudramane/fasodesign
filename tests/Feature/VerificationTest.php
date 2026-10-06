<?php

namespace Tests\Feature;

use App\Http\Middleware\EnTetesDeSecurite;
use App\Http\Middleware\VerifierHumain;
use App\Rules\Recaptcha;
use App\Support\Documentation;
use App\Support\ResolveurDns;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VerificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->defaultCookies = [];
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    private function dnsRepond(?string $hote, array $adresses): void
    {
        $this->app->instance(ResolveurDns::class, new class($hote, $adresses) extends ResolveurDns
        {
            public function __construct(private ?string $hote, private array $adresses) {}

            public function inverse(string $ip): ?string
            {
                return $this->hote;
            }

            public function direct(string $hote): array
            {
                return $this->adresses;
            }
        });
    }

    public function test_chaque_page_renvoie_d_abord_vers_la_case(): void
    {
        $pages = array_map(fn ($p) => $p['chemin'], config('charte.pages'));

        foreach ([...$pages, '/recherche?q=bouton', '/contact'] as $adresse) {
            $this->get($adresse)->assertRedirect(route('verification', ['retour' => $adresse]));
        }
    }

    public function test_la_page_de_verification_affiche_la_case(): void
    {
        $reponse = $this->get('/verification?retour=/fondations');

        $reponse->assertOk();
        $reponse->assertSee('<div class="g-recaptcha" data-sitekey="'.config('services.recaptcha.site_key').'"></div>', false);
        $reponse->assertSee('<script src="https://www.google.com/recaptcha/api.js?hl=fr" async defer></script>', false);
        $reponse->assertSee('name="retour" value="/fondations"', false);
        $reponse->assertHeader('Content-Security-Policy', EnTetesDeSecurite::CSP_RECAPTCHA);
    }

    public function test_cocher_la_case_ouvre_le_site_et_ramene_a_la_page_demandee(): void
    {
        Http::fake([Recaptcha::VERIFICATION => Http::response(['success' => true])]);

        $reponse = $this->post('/verification', [
            'g-recaptcha-response' => 'jeton',
            'retour' => '/gabarits#erreurs',
        ]);

        $reponse->assertRedirect('/gabarits#erreurs');
        $reponse->assertCookie(VerifierHumain::COOKIE);

        $cookie = collect($reponse->headers->getCookies())->firstWhere(fn ($c) => $c->getName() === VerifierHumain::COOKIE);
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertSame('lax', $cookie->getSameSite());

        /* Renvoyé tel que le serveur l'a écrit, déjà chiffré, comme le
           ferait le navigateur. */
        $this->withUnencryptedCookie(VerifierHumain::COOKIE, $cookie->getValue());
        $this->get('/gabarits')->assertOk();
    }

    public function test_sans_case_cochee_le_site_reste_ferme(): void
    {
        Http::fake();

        $this->post('/verification', ['g-recaptcha-response' => '', 'retour' => '/'])
            ->assertSessionHasErrors('g-recaptcha-response')
            ->assertCookieMissing(VerifierHumain::COOKIE);

        Http::assertNothingSent();
    }

    public function test_un_jeton_refuse_par_google_laisse_le_site_ferme(): void
    {
        Http::fake([Recaptcha::VERIFICATION => Http::response(['success' => false])]);

        $this->post('/verification', ['g-recaptcha-response' => 'faux', 'retour' => '/'])
            ->assertSessionHasErrors('g-recaptcha-response')
            ->assertCookieMissing(VerifierHumain::COOKIE);
    }

    public function test_le_retour_ne_mene_jamais_hors_du_site(): void
    {
        Http::fake([Recaptcha::VERIFICATION => Http::response(['success' => true])]);

        foreach (['//pirate.example', 'https://pirate.example', '/\\pirate.example', 'pirate'] as $retour) {
            $this->post('/verification', ['g-recaptcha-response' => 'jeton', 'retour' => $retour])
                ->assertRedirect('/');
        }
    }

    public function test_la_validation_expire(): void
    {
        $this->withCookie(VerifierHumain::COOKIE, (string) (time() - 60 * config('charte.verification.duree') - 1));
        $this->get('/')->assertRedirect();

        $this->withCookie(VerifierHumain::COOKIE, (string) time());
        $this->get('/')->assertOk();
    }

    public function test_un_cookie_forge_n_ouvre_rien(): void
    {
        /* Les cookies des essais sont chiffrés avec la clé de l'application ;
           un cookie écrit en clair par le visiteur ne se déchiffre pas. */
        $this->withUnencryptedCookie(VerifierHumain::COOKIE, (string) time());

        $this->get('/')->assertRedirect();
    }

    public function test_les_fichiers_de_la_charte_restent_ouverts(): void
    {
        $version = Documentation::version();

        foreach (['/assets/css/faso.css', "/{$version}/css/faso.css", '/robots.txt', '/sitemap.xml', '/VERSION.txt'] as $adresse) {
            $this->get($adresse)->assertOk();
        }
    }

    public function test_un_vrai_googlebot_passe(): void
    {
        $this->dnsRepond('crawl-66-249-66-1.googlebot.com', ['66.249.66.1']);

        $this->withServerVariables(['REMOTE_ADDR' => '66.249.66.1'])
            ->withHeader('User-Agent', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)')
            ->get('/fondations')
            ->assertOk();
    }

    public function test_un_faux_googlebot_recoit_la_case(): void
    {
        $this->dnsRepond('box.pirate.example', ['203.0.113.9']);

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.9'])
            ->withHeader('User-Agent', 'Mozilla/5.0 (compatible; Googlebot/2.1)')
            ->get('/fondations')
            ->assertRedirect();
    }

    public function test_un_nom_de_domaine_qui_ne_pointe_pas_vers_l_adresse_est_refuse(): void
    {
        /* Le pirate contrôle la résolution inverse de son adresse, pas la
           résolution directe des noms de Google. */
        $this->dnsRepond('crawl-66-249-66-1.googlebot.com', ['66.249.66.1']);

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.9'])
            ->withHeader('User-Agent', 'Googlebot/2.1')
            ->get('/fondations')
            ->assertRedirect();
    }

    public function test_un_vrai_duckduckbot_passe_et_un_faux_recoit_la_case(): void
    {
        Http::fake(['duckduckgo.com/*' => Http::response([
            'prefixes' => [['ipv4Prefix' => '20.191.45.212/32'], ['ipv4Prefix' => '40.88.21.0/24']],
        ])]);

        $this->withServerVariables(['REMOTE_ADDR' => '40.88.21.235'])
            ->withHeader('User-Agent', 'DuckDuckBot-Https/1.1; (+https://duckduckgo.com/duckduckbot)')
            ->get('/fondations')->assertOk();

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.9'])
            ->withHeader('User-Agent', 'DuckDuckBot-Https/1.1')
            ->get('/fondations')->assertRedirect();
    }

    public function test_liste_de_duckduckgo_injoignable_personne_n_est_admis_sans_case(): void
    {
        Http::fake(['duckduckgo.com/*' => Http::response('', 503)]);

        $this->withServerVariables(['REMOTE_ADDR' => '40.88.21.235'])
            ->withHeader('User-Agent', 'DuckDuckBot-Https/1.1')
            ->get('/fondations')->assertRedirect();
    }

    public function test_les_moteurs_admis_sont_ceux_que_la_documentation_annonce(): void
    {
        /* La page Intégration et outils/verifier.js promettent ces quatre
           moteurs autorisés : la barrière ne doit en oublier aucun. */
        $admis = array_map('strtolower', array_keys(config('charte.verification.moteurs')));
        sort($admis);

        $this->assertSame(['applebot', 'bingbot', 'duckduckbot', 'googlebot'], $admis);
        $this->assertStringContainsString('Googlebot, Bingbot, DuckDuckBot ou',
            file_get_contents(resource_path('views/pages/integration.blade.php')));
    }

    public function test_une_duree_de_dix_minutes_s_applique_et_s_affiche(): void
    {
        config(['charte.verification.duree' => 10]);

        $this->get('/verification')->assertSee("qu'après 10&nbsp;minutes", false);

        $this->withCookie(VerifierHumain::COOKIE, (string) (time() - 9 * 60));
        $this->get('/')->assertOk();

        $this->withCookie(VerifierHumain::COOKIE, (string) (time() - 11 * 60));
        $this->get('/')->assertRedirect();
    }

    public function test_chaque_page_vue_fait_repartir_le_delai(): void
    {
        config(['charte.verification.duree' => 10]);

        /* Coché il y a 9 minutes : la page s'affiche, et le cookie renvoyé
           est daté de maintenant. */
        $this->withCookie(VerifierHumain::COOKIE, (string) (time() - 9 * 60));
        $reponse = $this->get('/fondations')->assertOk();

        $cookie = collect($reponse->headers->getCookies())->firstWhere(fn ($c) => $c->getName() === VerifierHumain::COOKIE);
        $this->assertNotNull($cookie);
        $this->assertEqualsWithDelta(time() + 600, $cookie->getExpiresTime(), 5);

        /* Le cookie chiffré porte l'heure de cette page, non celle de la
           case cochée : les dix minutes repartent d'ici. */
        $valeur = app('encrypter')->decrypt($cookie->getValue(), false);
        $horodatage = (int) substr($valeur, strrpos($valeur, '|') + 1);
        $this->assertEqualsWithDelta(time(), $horodatage, 5);
    }

    public function test_sans_page_vue_pendant_le_delai_la_case_est_redemandee(): void
    {
        config(['charte.verification.duree' => 10]);

        $this->withCookie(VerifierHumain::COOKIE, (string) (time() - 10 * 60 - 1));
        $reponse = $this->get('/fondations');

        $reponse->assertRedirect();
        $this->assertSame([], array_filter($reponse->headers->getCookies(), fn ($c) => $c->getName() === VerifierHumain::COOKIE));
    }

    public function test_les_durees_s_ecrivent_comme_on_les_dit(): void
    {
        $this->assertSame('5&nbsp;minutes', \App\Support\Duree::lisible(5));
        $this->assertSame('1&nbsp;minute', \App\Support\Duree::lisible(1));
        $this->assertSame('1&nbsp;heure', \App\Support\Duree::lisible(60));
        $this->assertSame('1&nbsp;heure 30&nbsp;minutes', \App\Support\Duree::lisible(90));
        $this->assertSame('24&nbsp;heures', \App\Support\Duree::lisible(1440));
    }

    public function test_la_verification_peut_etre_coupee(): void
    {
        config(['charte.verification.active' => false]);

        $this->get('/')->assertOk();
    }

    public function test_un_visiteur_deja_verifie_n_a_rien_a_cocher(): void
    {
        $this->withCookie(VerifierHumain::COOKIE, (string) time());

        $this->get('/verification?retour=/integration')->assertRedirect('/integration');
    }
}

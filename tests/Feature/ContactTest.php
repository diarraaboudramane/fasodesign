<?php

namespace Tests\Feature;

use App\Http\Middleware\EnTetesDeSecurite;
use App\Mail\MessageContact;
use App\Rules\Recaptcha;
use Illuminate\Http\Client\Request as RequeteHttp;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactTest extends TestCase
{
    private const SAISIE = [
        'nom' => 'Awa Ouédraogo',
        'courriel' => 'a.ouedraogo@exemple.bf',
        'objet' => 'Composant manquant',
        'message' => "Il manque un sélecteur de commune dans l'annuaire.",
        'g-recaptcha-response' => 'jeton-renvoye-par-la-case',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        /* Les essais ne vérifient pas le jeton anti-falsification, qui
           est celui du cadriciel ; ils vérifient ce qui est propre ici. */
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);
        Mail::fake();
    }

    private function googleRepond(bool $succes): void
    {
        Http::fake([Recaptcha::VERIFICATION => Http::response(['success' => $succes])]);
    }

    public function test_la_page_affiche_la_case_et_charge_le_script_de_google(): void
    {
        $reponse = $this->get('/contact');

        $reponse->assertOk();
        $reponse->assertSee('<div class="g-recaptcha" data-sitekey="'.config('services.recaptcha.site_key').'"></div>', false);
        $reponse->assertSee('<script src="https://www.google.com/recaptcha/api.js?hl=fr" async defer></script>', false);
        $reponse->assertSee('name="_token"', false);
        $reponse->assertHeader('Content-Security-Policy', EnTetesDeSecurite::CSP_RECAPTCHA);
    }

    public function test_les_origines_de_google_ne_sont_ouvertes_que_sur_le_contact(): void
    {
        $this->get('/')->assertHeader('Content-Security-Policy', EnTetesDeSecurite::CSP);
        $this->get('/integration')->assertHeader('Content-Security-Policy', EnTetesDeSecurite::CSP);
    }

    public function test_un_message_verifie_par_google_est_transmis(): void
    {
        $this->googleRepond(true);

        $this->post('/contact', self::SAISIE)
            ->assertRedirect(route('contact'))
            ->assertSessionHas('envoye');

        Mail::assertSent(MessageContact::class, function (MessageContact $mail) {
            return $mail->hasTo('equipe@exemple.bf')
                && $mail->hasReplyTo('a.ouedraogo@exemple.bf')
                && ! array_key_exists('g-recaptcha-response', $mail->donnees);
        });

        Http::assertSent(fn (RequeteHttp $r) => $r->url() === Recaptcha::VERIFICATION
            && $r['secret'] === config('services.recaptcha.secret_key')
            && $r['response'] === 'jeton-renvoye-par-la-case');
    }

    public function test_sans_case_cochee_rien_n_est_envoye_ni_demande_a_google(): void
    {
        Http::fake();

        $this->post('/contact', ['g-recaptcha-response' => ''] + self::SAISIE)
            ->assertSessionHasErrors(['g-recaptcha-response' => 'Cochez la case « Je ne suis pas un robot ».']);

        Http::assertNothingSent();
        Mail::assertNothingSent();
    }

    public function test_un_jeton_refuse_par_google_bloque_l_envoi(): void
    {
        $this->googleRepond(false);

        $this->post('/contact', self::SAISIE)->assertSessionHasErrors('g-recaptcha-response');

        Mail::assertNothingSent();
    }

    public function test_google_injoignable_bloque_l_envoi(): void
    {
        Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('délai dépassé'));

        $this->post('/contact', self::SAISIE)->assertSessionHasErrors('g-recaptcha-response');

        Mail::assertNothingSent();
    }

    public function test_les_cles_d_essai_sont_refusees_en_production(): void
    {
        $this->googleRepond(true);
        $this->app['env'] = 'production';

        $this->post('/contact', self::SAISIE)->assertSessionHasErrors('g-recaptcha-response');

        Http::assertNothingSent();
        Mail::assertNothingSent();
    }

    public function test_sans_cle_secrete_le_formulaire_refuse(): void
    {
        $this->googleRepond(true);
        config(['services.recaptcha.secret_key' => null]);

        $this->post('/contact', self::SAISIE)->assertSessionHasErrors('g-recaptcha-response');

        Mail::assertNothingSent();
    }

    public function test_sans_destinataire_le_message_n_est_pas_perdu_en_silence(): void
    {
        $this->googleRepond(true);
        config(['charte.contact.destinataire' => null]);

        $this->post('/contact', self::SAISIE)->assertSessionHasErrors('envoi');

        Mail::assertNothingSent();
    }

    public function test_les_champs_sont_valides_en_francais(): void
    {
        $this->googleRepond(true);

        $this->post('/contact', ['courriel' => 'usager.exemple'] + self::SAISIE)
            ->assertSessionHasErrors(['courriel' => 'Ajoutez le symbole arobase et le nom de domaine, par exemple nom@exemple.bf']);
    }

    public function test_les_erreurs_sont_affichees_et_la_saisie_conservee(): void
    {
        $this->googleRepond(false);

        $this->from('/contact')->followingRedirects()->post('/contact', self::SAISIE)
            ->assertSee('La vérification anti-robot a échoué')
            ->assertSee('value="Composant manquant"', false)
            ->assertSee('href="#verification"', false);
    }

    public function test_les_envois_repetes_sont_freines(): void
    {
        $this->googleRepond(false);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/contact', self::SAISIE);
        }

        $this->post('/contact', self::SAISIE)->assertStatus(429);
    }

    public function test_le_site_exporte_ne_mentionne_pas_le_contact(): void
    {
        config(['charte.export' => true]);

        $html = \App\Support\Documentation::vue('index')->render();

        $this->assertStringNotContainsString('Nous écrire', $html);
    }

    public function test_l_accueil_de_l_application_mene_au_contact(): void
    {
        $this->get('/')->assertSee('<a href="'.route('contact').'">Nous écrire</a>', false);
    }
}

<?php

namespace Tests\Feature;

use App\Jobs\AnalyserProjet;
use App\Mail\BilanPret;
use App\Models\Analyse;
use App\Models\User;
use App\Support\Documentation;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\FabriqueZip;
use Tests\TestCase;

/** Le parcours de l'outil, du compte au bilan. */
class ConformiteParcoursTest extends TestCase
{
    use RefreshDatabase;

    private function compte(array $attributs = []): User
    {
        $compte = new User(['name' => 'Awa Ouédraogo', 'email' => 'awa@sante.gov.bf', 'entite' => 'Ministère de la Santé', 'password' => 'un mot de passe solide']);
        $compte->forceFill($attributs + ['statut' => 'actif', 'email_verified_at' => now()])->save();

        return $compte;
    }

    private function archiveConforme(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('projet.zip', FabriqueZip::creer([
            'projet/package.json' => json_encode(['dependencies' => ['@govbf/charte-graphique' => Documentation::version()]]),
            'projet/index.html' => ConformiteReglesTest::pageConforme(),
        ]));
    }

    public function test_sans_compte_l_outil_renvoie_a_la_connexion_et_le_site_reste_ouvert(): void
    {
        $this->get('/conformite')->assertOk()->assertSee('Contrôle de conformité');
        $this->get('/conformite/analyses')->assertRedirect(route('connexion'));
        /* La documentation n'ouvre toujours pas de session. */
        $this->assertEmpty(array_filter($this->get('/fondations')->headers->getCookies(), fn ($c) => $c->getName() === config('session.cookie')));
    }

    public function test_une_adresse_gov_bf_est_ouverte_des_qu_elle_est_confirmee(): void
    {
        Notification::fake();

        $this->post('/conformite/inscription', [
            'name' => 'Awa Ouédraogo', 'email' => 'Awa@Sante.gov.bf', 'entite' => 'Ministère de la Santé',
            'password' => 'un mot de passe solide', 'password_confirmation' => 'un mot de passe solide',
        ])->assertRedirect(route('conformite.courriel'));

        $compte = User::firstOrFail();
        $this->assertSame('awa@sante.gov.bf', $compte->email);
        $this->assertSame('actif', $compte->statut);
        Notification::assertSentTo($compte, VerifyEmail::class);

        $this->get('/conformite/analyses')->assertRedirect(route('conformite.courriel'));

        $lien = URL::temporarySignedRoute('conformite.courriel.confirmer', now()->addHour(), ['id' => $compte->id, 'empreinte' => sha1($compte->email)]);
        $this->get($lien)->assertRedirect(route('conformite.espace'));
        $this->get('/conformite/analyses')->assertOk()->assertSee('Nouvelle analyse');
    }

    public function test_une_autre_adresse_attend_la_validation_d_un_administrateur(): void
    {
        $prestataire = $this->compte(['email' => 'dev@prestataire.com', 'statut' => 'en_attente']);
        $this->actingAs($prestataire)->get('/conformite/analyses')->assertForbidden()->assertSee('en attente de validation', false);

        $admin = $this->compte(['email' => 'admin@mtdpce.gov.bf', 'role' => 'administrateur']);
        $this->actingAs($admin)->get('/conformite/comptes')->assertOk()->assertSee('dev@prestataire.com');
        $this->actingAs($admin)->post("/conformite/comptes/{$prestataire->id}", ['statut' => 'actif', 'role' => 'developpeur'])->assertRedirect();

        $this->assertSame('actif', $prestataire->fresh()->statut);
        $this->actingAs($prestataire->fresh())->get('/conformite/analyses')->assertOk();
    }

    public function test_seul_un_administrateur_voit_les_comptes(): void
    {
        $this->actingAs($this->compte())->get('/conformite/comptes')->assertNotFound();
    }

    public function test_une_archive_est_analysee_puis_supprimee_et_le_bilan_rendu(): void
    {
        Mail::fake();
        $compte = $this->compte();

        $reponse = $this->actingAs($compte)->post('/conformite/analyses', ['mode' => 'archive', 'archive' => $this->archiveConforme()]);

        $analyse = Analyse::firstOrFail();
        $reponse->assertRedirect(route('conformite.bilan', $analyse));
        $this->assertSame('terminee', $analyse->etat);
        $this->assertSame('conforme', $analyse->niveau);
        $this->assertFileDoesNotExist(AnalyserProjet::cheminArchive($analyse), 'l\'archive ne reste pas sur le serveur');
        Mail::assertSent(BilanPret::class);

        $this->get(route('conformite.bilan', $analyse))->assertOk()
            ->assertSee('Conforme')
            ->assertSee('Ce qui n\'a pas été vérifié', false)
            ->assertSee('Il ne vaut ni audit d\'accessibilité ni homologation', false);

        $markdown = $this->get(route('conformite.markdown', $analyse))->assertOk();
        $this->assertStringContainsString('# Bilan de conformité', $markdown->getContent());
        $this->assertStringContainsString('Ce qui n\'a pas été vérifié', $markdown->getContent());
    }

    public function test_l_envoi_avec_progression_recoit_l_adresse_du_bilan_en_json(): void
    {
        Mail::fake();
        $compte = $this->compte();

        $reponse = $this->actingAs($compte)
            ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
            ->post('/conformite/analyses', ['mode' => 'archive', 'archive' => $this->archiveConforme()]);

        $reponse->assertOk()->assertJson(['adresse' => route('conformite.bilan', Analyse::firstOrFail())]);
    }

    public function test_les_erreurs_de_l_envoi_avec_progression_reviennent_en_json(): void
    {
        $compte = $this->compte();
        $json = ['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'];

        $this->actingAs($compte)->withHeaders($json)
            ->post('/conformite/analyses', ['mode' => 'archive', 'archive' => UploadedFile::fake()->create('projet.pdf', 10)])
            ->assertStatus(422)->assertJsonValidationErrors('archive');

        config(['charte.conformite.analyses_par_jour' => 0]);
        $this->actingAs($compte)->withHeaders($json)
            ->post('/conformite/analyses', ['mode' => 'archive', 'archive' => $this->archiveConforme()])
            ->assertStatus(422)->assertJsonValidationErrors('mode');
    }

    public function test_le_formulaire_separe_les_champs_de_chaque_mode(): void
    {
        $page = $this->actingAs($this->compte())->get('/conformite/analyses')->assertOk()->getContent();

        $this->assertStringContainsString('data-mode-bloc="adresse"', $page);
        $this->assertStringContainsString('data-mode-bloc="archive"', $page);
        $this->assertStringContainsString('data-progression hidden', $page);
        /* Sans script, rien n'est masqué d'avance : les deux blocs restent utilisables. */
        $this->assertDoesNotMatchRegularExpression('/data-mode-bloc="[a-z]+"[^>]*hidden/', $page);
    }

    public function test_une_archive_piegee_donne_un_echec_explique(): void
    {
        Mail::fake();
        $piege = UploadedFile::fake()->createWithContent('piege.zip', FabriqueZip::creer(['../../.env' => 'x']));

        $this->actingAs($this->compte())->post('/conformite/analyses', ['mode' => 'archive', 'archive' => $piege]);

        $analyse = Analyse::firstOrFail();
        $this->assertSame('echec', $analyse->etat);
        $this->assertStringContainsString('Chemin refusé', $analyse->erreur);
        $this->assertFileDoesNotExist(AnalyserProjet::cheminArchive($analyse));
    }

    public function test_une_adresse_privee_est_refusee(): void
    {
        Mail::fake();
        $this->actingAs($this->compte())->post('/conformite/analyses', ['mode' => 'adresse', 'adresse' => 'http://127.0.0.1/']);

        $analyse = Analyse::firstOrFail();
        $this->assertSame('echec', $analyse->etat);
        $this->assertStringContainsString('réseau privé', $analyse->erreur);
    }

    public function test_un_bilan_n_est_visible_que_de_son_auteur_et_de_son_referent(): void
    {
        Mail::fake();
        $auteur = $this->compte();
        $this->actingAs($auteur)->post('/conformite/analyses', ['mode' => 'archive', 'archive' => $this->archiveConforme()]);
        $analyse = Analyse::firstOrFail();

        $autre = $this->compte(['email' => 'autre@finances.gov.bf', 'entite' => 'Ministère des Finances']);
        $this->actingAs($autre)->get(route('conformite.bilan', $analyse))->assertNotFound();
        $this->actingAs($autre)->get(route('conformite.markdown', $analyse))->assertNotFound();

        $referent = $this->compte(['email' => 'chef@sante.gov.bf', 'role' => 'referent']);
        $this->actingAs($referent)->get(route('conformite.bilan', $analyse))->assertOk();
    }

    public function test_le_nombre_d_analyses_par_jour_est_limite(): void
    {
        Mail::fake();
        config(['charte.conformite.analyses_par_jour' => 1]);
        $compte = $this->compte();

        $this->actingAs($compte)->post('/conformite/analyses', ['mode' => 'archive', 'archive' => $this->archiveConforme()]);
        $this->actingAs($compte)->post('/conformite/analyses', ['mode' => 'archive', 'archive' => $this->archiveConforme()])
            ->assertSessionHasErrors('mode');

        $this->assertSame(1, Analyse::count());
    }

    public function test_un_ecart_signale_est_traite_par_l_equipe_et_la_reponse_rendue_a_l_usager(): void
    {
        Mail::fake();
        $compte = $this->compte();
        $archive = UploadedFile::fake()->createWithContent('projet.zip', FabriqueZip::creer([
            'projet/index.html' => ConformiteReglesTest::pageConforme(),
            'projet/css/projet.css' => ".carte { color: #333; }",
        ]));
        $this->actingAs($compte)->post('/conformite/analyses', ['mode' => 'archive', 'archive' => $archive]);
        $analyse = Analyse::firstOrFail();
        $ou = 'projet/css/projet.css, ligne 1';

        /* Un écart qui n'est pas dans le bilan ne se signale pas. */
        $this->actingAs($compte)->post(route('conformite.signaler', $analyse), ['regle' => 'couleurs.en-dur', 'ou' => 'ailleurs, ligne 9'])->assertNotFound();

        $this->actingAs($compte)->post(route('conformite.signaler', $analyse), [
            'regle' => 'couleurs.en-dur', 'ou' => $ou, 'commentaire' => 'Cette couleur vient du logo de notre direction.',
        ])->assertRedirect();
        /* Le même écart ne se signale pas deux fois. */
        $this->actingAs($compte)->post(route('conformite.signaler', $analyse), ['regle' => 'couleurs.en-dur', 'ou' => $ou]);

        $signalement = $analyse->signalements()->sole();
        $this->assertSame('.carte { color: #333; }', $signalement->extrait, 'le constat est repris du bilan');
        $this->assertSame('nouveau', $signalement->statut);

        /* La page des écarts signalés, distincte de celle des comptes, donne le détail. */
        $admin = $this->compte(['email' => 'admin@mtdpce.gov.bf', 'role' => 'administrateur']);
        $this->actingAs($admin)->get('/conformite/comptes')->assertOk()->assertDontSee('Cette couleur vient du logo');
        $this->actingAs($admin)->get('/conformite/signalements')->assertOk()
            ->assertSee('Couleurs employées par les jetons')
            ->assertSee('Majeure')
            ->assertSee('Awa Ouédraogo')
            ->assertSee('Ministère de la Santé')
            ->assertSee($ou)
            ->assertSee('.carte { color: #333; }')
            ->assertSee('Cette couleur vient du logo de notre direction.');

        /* Maintenir l'écart demande une réponse. */
        $this->actingAs($admin)->post(route('conformite.signalements.traiter', $signalement), ['statut' => 'maintenu'])
            ->assertSessionHasErrors('reponse');
        $this->actingAs($admin)->post(route('conformite.signalements.traiter', $signalement), [
            'statut' => 'maintenu', 'reponse' => 'Une couleur de logo se déclare en jeton du projet, par exemple --projet-logo.',
        ])->assertRedirect();

        $this->assertSame('maintenu', $signalement->fresh()->statut);
        $this->actingAs($admin)->get('/conformite/signalements')->assertSee('Aucun signalement dans cette catégorie');
        $this->actingAs($compte)->get(route('conformite.bilan', $analyse))->assertOk()
            ->assertSee('Écart maintenu')
            ->assertSee('--projet-logo');
    }

    public function test_seul_un_administrateur_voit_les_ecarts_signales(): void
    {
        $this->actingAs($this->compte())->get('/conformite/signalements')->assertNotFound();
    }

    public function test_la_purge_supprime_les_bilans_de_plus_d_un_an(): void
    {
        $compte = $this->compte();
        $ancienne = new Analyse(['mode' => 'adresse', 'cible' => 'https://a.gov.bf/', 'version_charte' => '3.0.0']);
        $ancienne->user()->associate($compte);
        $ancienne->save();
        $ancienne->forceFill(['created_at' => now()->subDays(400)])->save();

        $this->artisan('charte:purger-analyses')->assertSuccessful();

        $this->assertSame(0, Analyse::count());
    }
}

<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifierHumain;
use App\Support\CharteMarkdown;
use App\Support\Documentation;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MarkdownTest extends TestCase
{
    private function markdown(): string
    {
        return $this->get('/charte.md')->assertOk()->getContent();
    }

    public function test_la_charte_est_servie_en_markdown(): void
    {
        $reponse = $this->get('/charte.md');

        $reponse->assertOk();
        $this->assertStringStartsWith('text/markdown', $reponse->headers->get('Content-Type'));
        $this->assertStringContainsString(CharteMarkdown::nomFichier(), $reponse->headers->get('Content-Disposition'));
        $this->assertStringStartsWith("---\ntitre: Charte graphique", $reponse->getContent());
    }

    public function test_le_markdown_se_lit_sans_passer_par_la_case(): void
    {
        /* Un agent ou curl ne coche pas de case. */
        config(['charte.verification.active' => true]);
        $this->disableCookieEncryption();

        $this->call('GET', '/charte.md', cookies: [VerifierHumain::COOKIE => ''])->assertOk();
    }

    public function test_les_consignes_aux_agents_ouvrent_le_document(): void
    {
        $md = $this->markdown();
        $consignes = strpos($md, '## Consignes aux agents');
        $premierePage = strpos($md, '## Présentation');

        $this->assertNotFalse($consignes);
        $this->assertLessThan($premierePage, $consignes);
        $this->assertStringContainsString('tu suis **toujours** les principes et les règles de cette charte', $md);
        $this->assertStringContainsString('version '.Documentation::version(), $md);
    }

    public function test_toutes_les_pages_et_les_annexes_y_figurent(): void
    {
        $md = $this->markdown();

        foreach (Documentation::pages() as $page) {
            $this->assertMatchesRegularExpression('/^## '.preg_quote($page['libelle'], '/').'$/m', $md);
        }
        $this->assertStringContainsString('## Annexe A — Jetons', $md);
        $this->assertStringContainsString('--rouge-500', $md);
        $this->assertStringContainsString('## Annexe B — Classes CSS disponibles', $md);
        $this->assertStringContainsString('## Annexe C — API JavaScript', $md);
    }

    public function test_les_exemples_donnent_leur_balisage(): void
    {
        $md = $this->markdown();

        $this->assertStringContainsString(
            "```html\n<button type=\"button\" class=\"fs-btn fs-btn--principal\">Déposer la demande</button>",
            $md
        );
        /* Les gabarits aussi, avec leur adresse. */
        $this->assertStringContainsString('*Gabarit — https://eservices.gov.bf*', $md);
    }

    public function test_les_exemples_n_emploient_que_des_classes_de_l_annexe(): void
    {
        /* Les consignes interdisent d'inventer une classe : chaque classe
           fs-* d'un exemple doit donc figurer dans la liste promise. */
        $md = $this->markdown();
        [$corps, $annexe] = explode('## Annexe B — Classes CSS disponibles', $md, 2);

        preg_match_all('/^- `(fs-[a-z0-9-]+)`$/m', $annexe, $m);
        $disponibles = array_flip($m[1]);

        preg_match_all('/```html\n(.*?)\n```/s', $corps, $blocs);
        preg_match_all('/class="([^"]+)"/', implode("\n", $blocs[1]), $attributs);
        $employees = [];
        foreach ($attributs[1] as $valeur) {
            foreach (preg_split('/\s+/', $valeur) as $classe) {
                if (str_starts_with($classe, 'fs-')) {
                    $employees[$classe] = true;
                }
            }
        }

        $this->assertNotEmpty($employees);
        $this->assertSame([], array_values(array_diff(array_keys($employees), array_keys($disponibles))));
    }

    public function test_les_liens_designent_le_serveur_public(): void
    {
        $md = $this->markdown();
        $serveur = config('charte.serveur');

        $this->assertStringContainsString("]({$serveur}/composants)", $md);
        $this->assertStringNotContainsString('](http://localhost', $md);
        $this->assertStringNotContainsString('?v=', $md);
    }

    public function test_la_commande_ecrit_le_meme_document(): void
    {
        $fichier = sys_get_temp_dir().'/charte-markdown-'.getmypid().'.md';
        File::delete($fichier);

        $this->artisan('charte:markdown', ['sortie' => $fichier])->assertSuccessful();

        $this->assertSame(CharteMarkdown::document(), file_get_contents($fichier));
        File::delete($fichier);
    }

    public function test_la_documentation_propose_le_telechargement(): void
    {
        $this->get('/integration')
            ->assertSee('href="'.route('markdown').'" download="'.CharteMarkdown::nomFichier().'"', false);
        $this->get('/')->assertSee(CharteMarkdown::nomFichier());
    }
}

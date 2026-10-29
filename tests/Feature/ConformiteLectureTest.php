<?php

namespace Tests\Feature;

use App\Conformite\Collecte\GardeReseau;
use App\Conformite\LecteurZip;
use App\Support\ResolveurDns;
use RuntimeException;
use Tests\FabriqueZip;
use Tests\TestCase;

/** Ce que l'outil accepte de lire, et surtout ce qu'il refuse. */
class ConformiteLectureTest extends TestCase
{
    public function test_une_archive_saine_se_lit_en_memoire(): void
    {
        $zip = new LecteurZip(FabriqueZip::creer([
            'projet/index.html' => '<html lang="fr"></html>',
            'projet/css/projet.css' => str_repeat('body { color: var(--texte); } ', 50),
        ]));

        $this->assertSame(['projet/index.html', 'projet/css/projet.css'], array_column($zip->fichiers(), 'nom'));
        $this->assertSame('<html lang="fr"></html>', $zip->lire('projet/index.html'));
        $this->assertStringStartsWith('body {', $zip->lire('projet/css/projet.css'));
    }

    public function test_un_chemin_qui_sort_de_l_archive_est_refuse(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Chemin refusé');
        new LecteurZip(FabriqueZip::creer(['../../.env' => 'APP_KEY=volee']));
    }

    public function test_un_chemin_absolu_est_refuse(): void
    {
        $this->expectExceptionMessage('Chemin refusé');
        new LecteurZip(FabriqueZip::creer(['/etc/passwd' => 'x']));
    }

    public function test_un_lien_symbolique_est_refuse(): void
    {
        $this->expectExceptionMessage('Lien symbolique refusé');
        new LecteurZip(FabriqueZip::creer(['config' => '/etc'], ['config' => ['lien' => true]]));
    }

    public function test_une_bombe_de_decompression_est_refusee_avant_d_etre_ouverte(): void
    {
        /* Quelques octets compressés qui se diraient 500 Mo une fois ouverts. */
        $this->expectExceptionMessage('Taux de compression anormal');
        new LecteurZip(FabriqueZip::creer(['bombe.txt' => str_repeat('0', 2048)], ['bombe.txt' => ['taille' => 500 * 1024 * 1024]]));
    }

    public function test_une_taille_annoncee_mensongere_est_detectee_a_la_lecture(): void
    {
        $zip = new LecteurZip(FabriqueZip::creer(['a.css' => str_repeat('a', 100)], ['a.css' => ['taille' => 50]]));

        $this->expectExceptionMessage('taille annoncée inexacte');
        $zip->lire('a.css');
    }

    public function test_un_fichier_qui_n_est_pas_une_archive_est_refuse(): void
    {
        $this->expectExceptionMessage("n'est pas une archive ZIP");
        new LecteurZip('<html>pas une archive</html>');
    }

    private function garde(array $ips): GardeReseau
    {
        return new GardeReseau(new class($ips) extends ResolveurDns
        {
            public function __construct(private array $ips) {}

            public function direct(string $hote): array { return $this->ips; }
        });
    }

    public function test_une_adresse_publique_est_acceptee_et_epinglee(): void
    {
        $cible = $this->garde(['196.28.245.10'])->verifier('https://eservices.gov.bf/demarches');

        $this->assertSame(['hote' => 'eservices.gov.bf', 'port' => 443, 'ip' => '196.28.245.10'], $cible);
    }

    public function test_le_reseau_interne_du_serveur_est_inaccessible(): void
    {
        foreach ([
            ['http://localhost/', ['127.0.0.1']],
            ['http://intranet.gov.bf/', ['10.0.0.5']],
            ['http://metadonnees/', ['169.254.169.254']],
            ['http://[::1]/', []],
            ['http://192.168.1.1/', []],
            /* Un nom public qui pointe aussi vers une adresse privée. */
            ['https://piege.example/', ['196.28.245.10', '172.16.0.1']],
        ] as [$adresse, $ips]) {
            try {
                $this->garde($ips)->verifier($adresse);
                $this->fail("{$adresse} aurait dû être refusée");
            } catch (RuntimeException $e) {
                $this->assertStringContainsString('réseau privé', $e->getMessage(), $adresse);
            }
        }
    }

    public function test_seuls_http_et_https_sur_leurs_ports_sont_acceptes(): void
    {
        foreach (['ftp://eservices.gov.bf/', 'file:///etc/passwd', 'https://eservices.gov.bf:8443/', 'https://moi:secret@eservices.gov.bf/'] as $adresse) {
            try {
                $this->garde(['196.28.245.10'])->verifier($adresse);
                $this->fail("{$adresse} aurait dû être refusée");
            } catch (RuntimeException) {
                $this->addToAssertionCount(1);
            }
        }
    }
}

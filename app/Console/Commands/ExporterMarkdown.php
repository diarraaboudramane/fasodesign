<?php

namespace App\Console\Commands;

use App\Support\CharteMarkdown;
use Illuminate\Console\Command;

/**
 * Écrit toute la charte dans un fichier Markdown : le même document que
 * /charte.md, pour un dépôt de projet ou une chaîne d'intégration.
 */
class ExporterMarkdown extends Command
{
    protected $signature = 'charte:markdown {sortie? : fichier à écrire, charte-graphique-<version>.md par défaut}';

    protected $description = 'Écrit toute la charte en Markdown, avec les consignes aux agents';

    public function handle(): int
    {
        $sortie = $this->argument('sortie') ?: CharteMarkdown::nomFichier();
        $dossier = dirname($sortie);
        if (! is_dir($dossier) && ! mkdir($dossier, 0777, true)) {
            $this->error("Impossible de créer {$dossier}");

            return self::FAILURE;
        }

        $octets = file_put_contents($sortie, CharteMarkdown::document());
        $this->line('  '.$sortie.' écrit ('.round($octets / 1024).' Ko)');

        return self::SUCCESS;
    }
}

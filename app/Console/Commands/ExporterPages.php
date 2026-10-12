<?php

namespace App\Console\Commands;

use App\Support\CharteMarkdown;
use App\Support\Documentation;
use Illuminate\Console\Command;

/**
 * Écrit chaque page de la documentation en fichier HTML statique.
 *
 * C'est ce que « npm run site » publie, et ce que « npm test » vérifie :
 * les pages exportées sont celles que l'application sert, aux liens près,
 * qui y sont relatifs pour que le site se lise depuis n'importe quel
 * hébergement statique.
 */
class ExporterPages extends Command
{
    protected $signature = 'charte:exporter {sortie : dossier où écrire les pages}';

    protected $description = 'Écrit les pages de la documentation en HTML statique';

    public function handle(): int
    {
        config(['charte.export' => true]);

        $sortie = rtrim($this->argument('sortie'), '/\\');
        if (! is_dir($sortie) && ! mkdir($sortie, 0777, true)) {
            $this->error("Impossible de créer {$sortie}");

            return self::FAILURE;
        }

        foreach (array_keys(Documentation::pages()) as $nom) {
            file_put_contents("{$sortie}/{$nom}.html", Documentation::vue($nom)->render());
        }

        /* Répond aux adresses inconnues : ErrorDocument chez Apache,
           convention de GitHub Pages. */
        file_put_contents("{$sortie}/404.html", view('errors.404')->render());

        /* Toute la charte en Markdown, à la même adresse que sur l'application. */
        file_put_contents("{$sortie}/charte.md", CharteMarkdown::document());

        $this->line('  '.(count(Documentation::pages()) + 1).' pages et charte.md écrits dans '.$sortie);

        return self::SUCCESS;
    }
}

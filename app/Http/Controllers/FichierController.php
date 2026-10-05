<?php

namespace App\Http\Controllers;

use App\Support\Documentation;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Mime\MimeTypes;

/**
 * Sert les fichiers de la charte depuis leur emplacement dans le dépôt.
 *
 * Ils ne sont pas recopiés dans public/ : assets/ est aussi la source
 * du paquet npm et de tout l'outillage, et une copie finirait par
 * diverger de l'original sans que rien ne le signale.
 */
class FichierController extends Controller
{
    private const UN_AN = 'public, max-age=31536000';

    private const FIGE = 'public, max-age=31536000, immutable';

    /** /assets/…, /archives/…, /android/… */
    public function dossier(string $dossier, string $chemin): BinaryFileResponse
    {
        $fichier = $this->resoudre($dossier, $chemin);
        $extension = strtolower(pathinfo($fichier, PATHINFO_EXTENSION));

        $entetes = match ($extension) {
            /* Un service hébergé sur un autre domaine consomme les polices
               et les emblèmes : sans autorisation, le navigateur les
               télécharge puis les rejette. */
            'woff2', 'svg' => ['Cache-Control' => self::FIGE, 'Access-Control-Allow-Origin' => '*'],
            'css', 'js' => ['Cache-Control' => self::UN_AN],
            default => [],
        };

        return $this->servir($fichier, $entetes);
    }

    /** /<version>/css/faso.css : une adresse qui ne change jamais de contenu. */
    public function diffusion(string $version, string $type, string $chemin): BinaryFileResponse
    {
        abort_unless($version === Documentation::version(), 404);
        abort_if(in_array("{$type}/{$chemin}", config('charte.hors_diffusion'), true), 404);

        return $this->servir($this->resoudre("assets/{$type}", $chemin), [
            'Cache-Control' => self::FIGE,
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    /**
     * Le chemin demandé, à condition qu'il désigne un fichier situé
     * dans le dossier annoncé : « ../.env » ne sort pas d'assets/.
     */
    private function resoudre(string $dossier, string $chemin): string
    {
        $base = realpath(base_path($dossier));
        $fichier = $base === false ? false : realpath($base.DIRECTORY_SEPARATOR.$chemin);

        abort_if(
            $fichier === false
            || ! str_starts_with($fichier, $base.DIRECTORY_SEPARATOR)
            || ! is_file($fichier)
            || str_starts_with(basename($fichier), '.'),
            404
        );

        return $fichier;
    }

    /**
     * Le type est tiré de l'extension, jamais du contenu : la détection
     * par le contenu donne « text/plain » pour une feuille de styles, que
     * le navigateur refuse alors d'appliquer à cause de nosniff.
     *
     * @param  array<string, string>  $entetes
     */
    private function servir(string $fichier, array $entetes): BinaryFileResponse
    {
        $extension = strtolower(pathinfo($fichier, PATHINFO_EXTENSION));
        $type = MimeTypes::getDefault()->getMimeTypes($extension)[0] ?? 'application/octet-stream';

        if (str_starts_with($type, 'text/') || in_array($type, [
            'application/javascript', 'application/json', 'application/xml', 'image/svg+xml',
        ], true)) {
            $type .= '; charset=utf-8';
        }

        return response()->file($fichier, ['Content-Type' => $type] + $entetes);
    }
}

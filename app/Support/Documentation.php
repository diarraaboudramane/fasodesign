<?php

namespace App\Support;

use Illuminate\Contracts\View\View;
use InvalidArgumentException;

/**
 * Ce que l'application et l'export statique partagent : la façon de
 * produire une page, et le numéro de version de la charte.
 */
final class Documentation
{
    /** @return array<string, array<string, mixed>> */
    public static function pages(): array
    {
        return config('charte.pages');
    }

    public static function vue(string $nom): View
    {
        $page = self::pages()[$nom]
            ?? throw new InvalidArgumentException("Page inconnue : {$nom}");

        return view("pages.{$nom}", ['courante' => $nom, 'page' => $page]);
    }

    /**
     * La version est celle du paquet npm : la documentation décrit ce
     * paquet, et ne doit pas annoncer un autre numéro que lui.
     */
    public static function version(): string
    {
        static $version;

        return $version ??= json_decode(
            file_get_contents(base_path('package.json')), true, flags: JSON_THROW_ON_ERROR
        )['version'];
    }
}

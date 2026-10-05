<?php

/*
 * Les deux seules fonctions dont les vues ont besoin pour désigner une
 * adresse. Elles écrivent une adresse de l'application quand celle-ci
 * répond, et un chemin relatif quand les pages sont exportées en
 * fichiers statiques.
 */

if (! function_exists('page')) {
    /** Adresse d'une page de la documentation, par son nom dans config/charte.php. */
    function page(string $nom): string
    {
        if (config('charte.export')) {
            return $nom.'.html';
        }

        return route($nom);
    }
}

if (! function_exists('ressource')) {
    /** Adresse d'un fichier servi tel quel : assets/…, archives/…, android/… */
    function ressource(string $chemin): string
    {
        if (config('charte.export')) {
            return $chemin;
        }

        /* Les feuilles et les scripts sont gardés un an par le navigateur :
           la date du fichier dans l'adresse fait recharger celui qui change,
           et lui seul. */
        $fichier = base_path($chemin);

        return asset($chemin).(is_file($fichier) ? '?v='.filemtime($fichier) : '');
    }
}

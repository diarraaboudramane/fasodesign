<?php

/*
 * Les pages de la documentation, dans l'ordre de la navigation.
 *
 * Cette liste est la seule à tenir : routes, menus, plan du site et
 * export statique en sont tirés. Une page ajoutée ici sans vue dans
 * resources/views/pages fait échouer les essais.
 *
 *   titre        contenu de <title>, HTML autorisé (&nbsp;)
 *   description  contenu de <meta name="description">
 *   libelle      intitulé dans les menus
 *   gabarit      « accueil » (en-tête et pied complets) ou
 *                « documentation » (barre latérale et sommaire)
 *   sommaire     titre du groupe « Sur cette page » de la barre latérale
 *   teinte       couleur d'accent des titres de section, si elle diffère
 */

return [

    'pages' => [

        'index' => [
            'chemin' => '/',
            'titre' => "Charte graphique de l'administration burkinabè",
            'description' => "Système de conception des services numériques de l'État burkinabè : fondations, composants d'interface, composants métier et gabarits institutionnels.",
            'libelle' => 'Présentation',
            'gabarit' => 'accueil',
        ],

        'fondations' => [
            'chemin' => '/fondations',
            'titre' => "Fondations&nbsp;— Charte graphique de l'administration burkinabè",
            'description' => "Emblème, couleurs nationales, typographie, grille, espacement, formes et mouvement du système de conception de l'État burkinabè.",
            'libelle' => 'Fondations',
            'gabarit' => 'documentation',
            'sommaire' => 'Sur cette page',
            'teinte' => null,
        ],

        'composants' => [
            'chemin' => '/composants',
            'titre' => "Composants de base&nbsp;— Charte graphique de l'administration burkinabè",
            'description' => "Catalogue des composants d'interface du système de conception de l'État burkinabè : actions, formulaires, navigation, information, données, retour système et superposition.",
            'libelle' => 'Composants de base',
            'gabarit' => 'documentation',
            'sommaire' => 'Familles',
            'teinte' => 'or',
        ],

        'composants-metier' => [
            'chemin' => '/composants-metier',
            'titre' => "Composants métier&nbsp;— Charte graphique de l'administration burkinabè",
            'description' => "Blocs propres au service public burkinabè : identification officielle, fiche de démarche, pièces à fournir, suivi de dossier et signalement de fraude.",
            'libelle' => 'Composants métier',
            'gabarit' => 'documentation',
            'sommaire' => 'Sur cette page',
            'teinte' => 'rouge',
        ],

        'gabarits' => [
            'chemin' => '/gabarits',
            'titre' => "Gabarits institutionnels&nbsp;— Charte graphique de l'administration burkinabè",
            'description' => "Assemblages de référence : portail de services, fiche de démarche, formulaire long, tableau de bord usager, authentification et pages d'erreur.",
            'libelle' => 'Gabarits',
            'gabarit' => 'documentation',
            'sommaire' => 'Sur cette page',
            'teinte' => 'vert',
        ],

        'accessibilite' => [
            'chemin' => '/accessibilite',
            'titre' => "Accessibilité et rédaction&nbsp;— Charte graphique de l'administration burkinabè",
            'description' => "Exigences d'accessibilité, contraintes de réseau et règles de rédaction administrative des services numériques de l'État burkinabè.",
            'libelle' => 'Accessibilité',
            'gabarit' => 'documentation',
            'sommaire' => 'Sur cette page',
            'teinte' => 'or',
        ],

        'integration' => [
            'chemin' => '/integration',
            'titre' => "Intégration&nbsp;— Charte graphique de l'administration burkinabè",
            'description' => "Intégrer la charte comme dépendance : npm, Composer, téléchargement. Cas détaillé d'Angular, rendu côté serveur, politique de sécurité, encapsulation des styles.",
            'libelle' => 'Intégration',
            'gabarit' => 'documentation',
            'sommaire' => 'Sur cette page',
            'teinte' => 'or',
        ],

        'gouvernance' => [
            'chemin' => '/gouvernance',
            'titre' => "Gouvernance&nbsp;— Charte graphique de l'administration burkinabè",
            'description' => "Rôles, versionnement, cycle de vie des composants, procédure de contribution, dérogations et journal des versions du système de conception de l'État burkinabè.",
            'libelle' => 'Gouvernance',
            'gabarit' => 'documentation',
            'sommaire' => 'Sur cette page',
            'teinte' => null,
        ],

    ],

    /*
     * Dossiers du projet servis tels quels sous leur propre nom. Ce
     * sont les mêmes que ceux que « npm run site » recopie dans le site
     * statique : l'application et le site exporté exposent les mêmes
     * adresses.
     */
    'dossiers' => ['assets', 'archives', 'android'],

    /*
     * Sous-dossiers d'assets diffusés à une adresse figée,
     * /<version>/css/faso.css, et ce qui en est retiré : l'outillage de
     * la documentation n'a rien à faire dans une adresse qu'un service
     * mettra en production.
     */
    'diffusion' => ['css', 'js', 'img', 'polices'],
    'hors_diffusion' => ['css/docs.css', 'js/docs.js'],

    /*
     * Vrai pendant « php artisan charte:exporter » : les liens sont
     * alors écrits relatifs, comme dans les pages d'origine, pour que le
     * site exporté se lise depuis n'importe quel hébergement statique.
     */
    'export' => false,

    /*
     * Adresse qui reçoit les messages du formulaire de contact. Sans
     * elle, le formulaire refuse l'envoi au lieu de perdre le message.
     */
    'contact' => [
        'destinataire' => env('CONTACT_DESTINATAIRE'),
    ],

    /*
     * Vérification anti-robot à l'entrée du site : avant la première page,
     * l'usager coche la case reCAPTCHA. Elle vaut pour toutes les pages
     * HTML de l'application, pas pour les fichiers de la charte (assets/,
     * /<version>/…), que les services chargent depuis leurs propres sites.
     *
     *   duree    minutes pendant lesquelles la case reste validée sur
     *            l'appareil (cookie chiffré « charte_humain »)
     *   moteurs  robots d'indexation admis sans case : les quatre que la
     *            page Intégration et la recette déclarent autorisés. Sans
     *            eux, la documentation sortirait des résultats de
     *            recherche. Le nom annoncé ne suffit pas, l'adresse IP
     *            doit être celle du moteur :
     *              domaines  résolution inverse puis directe, la méthode
     *                        que publient Google, Bing et Apple ;
     *              adresses  liste officielle des adresses, pour
     *                        DuckDuckGo qui ne publie que celle-ci.
     *            Un agent qui se dit Googlebot depuis une autre adresse
     *            reçoit la case.
     */
    'verification' => [
        'active' => (bool) env('CHARTE_VERIFICATION', true),
        'duree' => (int) env('CHARTE_VERIFICATION_DUREE', 1440),
        'moteurs' => [
            'Googlebot' => ['domaines' => ['.googlebot.com', '.google.com', '.googleusercontent.com']],
            'bingbot' => ['domaines' => ['.search.msn.com']],
            'Applebot' => ['domaines' => ['.applebot.apple.com']],
            'DuckDuckBot' => ['adresses' => 'https://duckduckgo.com/duckduckbot.json'],
        ],
    ],

];

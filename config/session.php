<?php

/*
 * Seul le formulaire de contact ouvre une session (groupe « formulaire »,
 * bootstrap/app.php) : les pages de la documentation ne posent aucun
 * cookie. La session y sert au jeton anti-falsification et au retour
 * des erreurs de saisie ; elle est stockée en fichiers, faute de base.
 */
return [

    'driver' => env('SESSION_DRIVER', 'file'),

    'lifetime' => (int) env('SESSION_LIFETIME', 30),

    'cookie' => 'charte_session',

    /* Vrai en production, derrière HTTPS : sans lui, le cookie partirait
       aussi en clair. */
    'secure' => env('SESSION_SECURE_COOKIE'),

    'http_only' => true,

    'same_site' => 'lax',

];

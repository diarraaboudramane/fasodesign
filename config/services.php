<?php

return [

    /*
     * Google reCAPTCHA v2, case « Je ne suis pas un robot ».
     * Les clés se créent dans la console reCAPTCHA (Google Cloud), pour
     * le type « Case à cocher » et le domaine du site.
     *
     * Le fichier .env.example porte les clés d'essai publiques de Google :
     * la case est toujours validée. Elles sont refusées en production.
     */
    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    ],

];

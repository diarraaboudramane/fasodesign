<?php

/*
 * Le disque local n'est pas servi : par défaut, le cadriciel ouvre
 * /storage/{path} en lecture et en écriture, et la documentation n'a
 * aucun fichier à recevoir.
 */
return [

    'default' => 'local',

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => false,
            'throw' => false,
            'report' => false,
        ],

    ],

];

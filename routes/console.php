<?php

/*
 * Les commandes de la documentation sont des classes, dans
 * app/Console/Commands : « php artisan list charte ».
 */

use Illuminate\Support\Facades\Schedule;

/* Les bilans de conformité sont gardés un an : « php artisan schedule:run »
   chaque minute, depuis le cron du serveur, applique cette règle. */
Schedule::command('charte:purger-analyses')->daily();

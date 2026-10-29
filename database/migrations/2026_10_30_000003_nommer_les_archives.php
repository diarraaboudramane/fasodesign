<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * L'archive déposée reçoit un nom aléatoire, gardé avec l'analyse : deux
 * dépôts ne peuvent jamais se disputer le même fichier, même quand les
 * numéros d'analyse repartent de 1 (base vidée, essais).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->string('archive')->nullable()->after('cible');
        });
    }

    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->dropColumn('archive');
        });
    }
};

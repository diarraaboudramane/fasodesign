<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Un signalement garde le constat tel qu'il était affiché, et son suivi
 * par l'équipe de la charte : nouveau, règle corrigée, ou maintenu.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('signalements', function (Blueprint $table) {
            $table->text('extrait')->nullable()->after('ou');
            /* nouveau, corrige ou maintenu */
            $table->string('statut')->default('nouveau')->after('commentaire');
            $table->text('reponse')->nullable()->after('statut');
            $table->foreignId('traite_par')->nullable()->after('reponse')->constrained('users')->nullOnDelete();
            $table->timestamp('traite_le')->nullable()->after('traite_par');
        });
    }

    public function down(): void
    {
        Schema::table('signalements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('traite_par');
            $table->dropColumn(['extrait', 'statut', 'reponse', 'traite_le']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            /* adresse ou archive */
            $table->string('mode');
            /* L'adresse analysée, ou le nom de l'archive déposée. */
            $table->string('cible');
            /* attente, en_cours, terminee ou echec */
            $table->string('etat')->default('attente');
            /* conforme, reserves ou non_conforme, une fois terminée */
            $table->string('niveau')->nullable();
            $table->unsignedInteger('bloquants')->default(0);
            $table->unsignedInteger('majeurs')->default(0);
            $table->unsignedInteger('mineurs')->default(0);
            $table->string('version_charte');
            /* Le détail règle par règle, et ce qui n'a pas pu être vérifié. */
            $table->json('resultats')->nullable();
            $table->json('perimetre')->nullable();
            $table->text('erreur')->nullable();
            $table->timestamp('terminee_le')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        /* Un écart que l'usager estime erroné. Sert à corriger les règles. */
        Schema::create('signalements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analyse_id')->constrained('analyses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('regle');
            $table->string('ou');
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('signalements');
        Schema::dropIfExists('analyses');
    }
};

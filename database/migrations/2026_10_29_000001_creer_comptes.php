<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Les comptes de l'outil de contrôle de conformité. Le reste du site se
 * lit sans compte : ces tables ne servent qu'à l'outil.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('entite');
            /* developpeur, referent ou administrateur */
            $table->string('role')->default('developpeur');
            /* en_attente tant qu'un administrateur n'a pas validé une
               adresse hors .gov.bf, actif, ou suspendu */
            $table->string('statut')->default('en_attente');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};

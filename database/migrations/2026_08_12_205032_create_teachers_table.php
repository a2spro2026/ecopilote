<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('nom_complet');
            $table->string('contact');
            $table->string('ville');
            $table->string('statut'); // public, prive
            $table->string('matiere');
            $table->string('niveau')->nullable();
            $table->string('disponibilite'); // immediat, a_negocier
            $table->string('etat')->default('actif'); // actif, en_attente
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};

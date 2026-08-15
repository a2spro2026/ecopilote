<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('code', 16)->nullable()->unique();
            $table->string('nom_complet');
            $table->string('contact');
            $table->string('contact_tuteur');
            $table->string('ville');
            $table->string('niveau_scolaire');
            $table->string('matiere');
            $table->string('type_cours'); // individuel, en_groupe
            $table->string('etat')->default('actif'); // actif, en_attente, suspendu
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

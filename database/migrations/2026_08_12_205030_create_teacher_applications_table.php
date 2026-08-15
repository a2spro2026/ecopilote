<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_applications', function (Blueprint $table) {
            $table->id();
            $table->string('nom_complet');
            $table->string('contact');
            $table->string('ville');
            $table->string('matiere');
            $table->string('niveau');
            $table->string('statut');
            $table->string('disponibilite');
            $table->string('etat')->default('en_attente');
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_applications');
    }
};

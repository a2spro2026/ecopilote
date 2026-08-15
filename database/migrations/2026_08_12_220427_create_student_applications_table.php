<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_applications', function (Blueprint $table) {
            $table->id();
            $table->string('nom_complet');
            $table->string('contact');
            $table->string('contact_tuteur');
            $table->string('ville');
            $table->string('niveau_scolaire');
            $table->string('matiere');
            $table->string('type_cours');
            $table->string('etat')->default('en_attente');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_applications');
    }
};

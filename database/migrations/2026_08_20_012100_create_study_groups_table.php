<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code', 16)->nullable()->unique();
            $table->string('matiere');
            $table->string('niveau', 32);
            $table->foreignId('teacher_id')->constrained('teachers')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('study_group_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_group_id')->constrained('study_groups')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->unique(['study_group_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_group_student');
        Schema::dropIfExists('study_groups');
    }
};

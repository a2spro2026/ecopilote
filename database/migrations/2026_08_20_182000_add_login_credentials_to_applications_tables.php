<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->string('login')->nullable()->after('type_cours');
            $table->text('access_password')->nullable()->after('login');
        });

        Schema::table('teacher_applications', function (Blueprint $table) {
            $table->string('login')->nullable()->after('disponibilite');
            $table->text('access_password')->nullable()->after('login');
        });
    }

    public function down(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->dropColumn(['login', 'access_password']);
        });

        Schema::table('teacher_applications', function (Blueprint $table) {
            $table->dropColumn(['login', 'access_password']);
        });
    }
};

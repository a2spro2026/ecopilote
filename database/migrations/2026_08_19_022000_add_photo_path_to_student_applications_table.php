<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('type_cours');
        });
    }

    public function down(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }
};

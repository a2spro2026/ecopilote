<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('tuteur_nom')->nullable()->after('contact');
            $table->string('mode_paiement', 32)->nullable()->after('paiement');
            $table->string('periode_paiement', 32)->nullable()->after('mode_paiement');
            $table->string('photo_path')->nullable()->after('periode_paiement');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->string('periode_paiement', 32)->nullable()->after('type_paiement');
            $table->string('photo_path')->nullable()->after('periode_paiement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['tuteur_nom', 'mode_paiement', 'periode_paiement', 'photo_path']);
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['periode_paiement', 'photo_path']);
        });
    }
};

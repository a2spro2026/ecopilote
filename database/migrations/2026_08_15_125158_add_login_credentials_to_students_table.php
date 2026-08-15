<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('login')->nullable()->unique()->after('nom_complet');
            $table->text('access_password')->nullable()->after('login');
        });

        DB::table('students')->orderBy('id')->get(['id', 'nom_complet'])->each(function ($student) {
            $local = Str::of($student->nom_complet)
                ->ascii()
                ->lower()
                ->replaceMatches('/[^a-z0-9]+/u', '.')
                ->trim('.')
                ->toString() ?: 'eleve';
            $login = $local.'@ecopilote.ma';
            if (DB::table('students')->where('login', $login)->exists()) {
                $login = $local.'.'.$student->id.'@ecopilote.ma';
            }

            DB::table('students')->where('id', $student->id)->update([
                'login' => $login,
                'access_password' => Crypt::encryptString((string) random_int(10000000, 99999999)),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['login']);
            $table->dropColumn(['login', 'access_password']);
        });
    }
};

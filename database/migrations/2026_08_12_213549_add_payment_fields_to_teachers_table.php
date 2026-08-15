<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('code', 16)->nullable()->unique()->after('id');
            $table->string('paiement')->nullable()->after('etat'); // salaire, commission, pourcentage
            $table->decimal('paiement_valeur', 10, 2)->nullable()->after('paiement');
            $table->string('type_paiement')->nullable()->after('paiement_valeur'); // vir, chq, vers, esp
        });

        $teachers = DB::table('teachers')->orderBy('id')->get(['id']);
        foreach ($teachers as $teacher) {
            DB::table('teachers')->where('id', $teacher->id)->update([
                'code' => 'PF'.str_pad((string) $teacher->id, 4, '0', STR_PAD_LEFT),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['code', 'paiement', 'paiement_valeur', 'type_paiement']);
        });
    }
};

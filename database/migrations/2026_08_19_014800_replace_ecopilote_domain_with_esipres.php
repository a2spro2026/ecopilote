<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->replaceDomain('users', 'email');
        $this->replaceDomain('teachers', 'login');
        $this->replaceDomain('students', 'login');
    }

    public function down(): void
    {
        $this->replaceDomain('users', 'email', '@esipres.com', '@ecopilote.ma');
        $this->replaceDomain('teachers', 'login', '@esipres.com', '@ecopilote.ma');
        $this->replaceDomain('students', 'login', '@esipres.com', '@ecopilote.ma');
    }

    private function replaceDomain(string $table, string $column, string $from = '@ecopilote.ma', string $to = '@esipres.com'): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        DB::table($table)
            ->where($column, 'like', '%'.$from)
            ->orderBy('id')
            ->get(['id', $column])
            ->each(function ($row) use ($table, $column, $from, $to) {
                DB::table($table)->where('id', $row->id)->update([
                    $column => str_replace($from, $to, (string) $row->{$column}),
                ]);
            });
    }
};

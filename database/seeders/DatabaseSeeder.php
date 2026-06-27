<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@ecopilote.ma',
                'role' => User::ROLE_SUPERADMIN,
            ],
            [
                'name' => 'Service Comptabilité',
                'email' => 'comptabilite@ecopilote.ma',
                'role' => User::ROLE_COMPTABILITE,
            ],
            [
                'name' => 'Service Accueil',
                'email' => 'accueil@ecopilote.ma',
                'role' => User::ROLE_ACCUEIL,
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'password' => 'password',
                ]
            );
        }
    }
}

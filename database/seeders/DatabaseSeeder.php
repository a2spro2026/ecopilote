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
                'name' => 'Zerragui',
                'email' => 'zerragui@esipres.com',
                'role' => User::ROLE_SUPERADMIN,
                'password' => '0661755048',
            ],
            [
                'name' => 'Service Comptabilité',
                'email' => 'comptabilite@esipres.com',
                'role' => User::ROLE_COMPTABILITE,
                'password' => 'password',
            ],
            [
                'name' => 'Service Accueil',
                'email' => 'accueil@esipres.com',
                'role' => User::ROLE_ACCUEIL,
                'password' => 'password',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'password' => $data['password'],
                ]
            );
        }

        // Remplace l'ancien compte superadmin s'il existe encore.
        User::where('email', 'admin@esipres.com')->delete();
    }
}

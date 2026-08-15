<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return view('admin.rooms.active', [
            'salles' => $this->demoActiveRooms(),
        ]);
    }

    /**
     * Données de démonstration — à remplacer par le live métier.
     *
     * @return list<array<string, mixed>>
     */
    private function demoActiveRooms(): array
    {
        return [
            [
                'id' => 'salle-01',
                'nom' => 'Salle 01',
                'matiere' => 'Mathématique',
                'niveau' => 'Lycée',
                'professeur' => 'Karim Benali',
                'debut' => '14:00',
                'fin' => '15:30',
                'presents' => ['Sara Amrani', 'Youssef El Fassi', 'Imane Chakir', 'Omar Tazi'],
                'absents' => ['Nour Belkadi'],
                'tone' => 'emerald',
            ],
            [
                'id' => 'salle-02',
                'nom' => 'Salle 02',
                'matiere' => 'Français',
                'niveau' => 'Collège',
                'professeur' => 'Leila Mansouri',
                'debut' => '14:00',
                'fin' => '15:00',
                'presents' => ['Aya Bennani', 'Mehdi Saidi', 'Lina Kadiri'],
                'absents' => ['Rania El Ouardi', 'Hamza Farouk'],
                'tone' => 'blue',
            ],
            [
                'id' => 'salle-03',
                'nom' => 'Salle 03',
                'matiere' => 'Physique',
                'niveau' => 'Lycée',
                'professeur' => 'Hassan Idrissi',
                'debut' => '15:00',
                'fin' => '16:30',
                'presents' => ['Zineb Alaoui', 'Adam Cherkaoui', 'Salma Bouzid', 'Yassine Naciri', 'Inès Hariri'],
                'absents' => [],
                'tone' => 'indigo',
            ],
            [
                'id' => 'salle-04',
                'nom' => 'Salle 04',
                'matiere' => 'Anglais',
                'niveau' => 'Primaire',
                'professeur' => 'Nadia Serhani',
                'debut' => '15:30',
                'fin' => '16:30',
                'presents' => ['Amine Lahbabi', 'Hiba Toumi'],
                'absents' => ['Sofia El Amrani', 'Rayan Berrada'],
                'tone' => 'amber',
            ],
            [
                'id' => 'salle-05',
                'nom' => 'Salle 05',
                'matiere' => 'Coran',
                'niveau' => 'Divers',
                'professeur' => 'Abdelilah Ouazzani',
                'debut' => '16:00',
                'fin' => '17:00',
                'presents' => ['Fatima Zahra', 'Ilyas Mekouar', 'Meryem Chraibi'],
                'absents' => ['Anas Filali'],
                'tone' => 'teal',
            ],
            [
                'id' => 'salle-06',
                'nom' => 'Salle 06',
                'matiere' => 'SVT',
                'niveau' => 'Université',
                'professeur' => 'Samira Berrada',
                'debut' => '16:00',
                'fin' => '17:30',
                'presents' => ['Khalid Benjelloun', 'Ghita Ait', 'Reda Slimani', 'Salma Kettani'],
                'absents' => ['Othmane Raji', 'Imane Saad'],
                'tone' => 'violet',
            ],
            [
                'id' => 'salle-07',
                'nom' => 'Salle 07',
                'matiere' => 'Espagnol',
                'niveau' => 'Lycée',
                'professeur' => 'Carlos Mendoza',
                'debut' => '17:00',
                'fin' => '18:00',
                'presents' => ['Sara Lahlou', 'Younes Amrani', 'Ines Fassi'],
                'absents' => ['Bilal Cherkaoui'],
                'tone' => 'rose',
            ],
            [
                'id' => 'salle-08',
                'nom' => 'Salle 08',
                'matiere' => 'Allemand',
                'niveau' => 'Collège',
                'professeur' => 'Fatima Zahra Alaoui',
                'debut' => '17:00',
                'fin' => '18:30',
                'presents' => ['Amine Berrada', 'Laila Touzani', 'Redouane Saidi', 'Nour El Idrissi'],
                'absents' => ['Hajar Benkirane', 'Omar Chraibi'],
                'tone' => 'blue',
            ],
            [
                'id' => 'salle-09',
                'nom' => 'Salle 09',
                'matiere' => 'Programme Scolaire',
                'niveau' => 'Primaire',
                'professeur' => 'Mounir El Kettani',
                'debut' => '17:30',
                'fin' => '18:30',
                'presents' => ['Aya Mansouri', 'Yassine Filali'],
                'absents' => [],
                'tone' => 'emerald',
            ],
            [
                'id' => 'salle-10',
                'nom' => 'Salle 10',
                'matiere' => 'Mathématique',
                'niveau' => 'Université',
                'professeur' => 'Rachid Benjelloun',
                'debut' => '18:00',
                'fin' => '19:30',
                'presents' => ['Salma Hariri', 'Anas Ouazzani', 'Imane Bennani', 'Mehdi Alaoui', 'Ghita Serhani'],
                'absents' => ['Khalid Tazi'],
                'tone' => 'indigo',
            ],
            [
                'id' => 'salle-11',
                'nom' => 'Salle 11',
                'matiere' => 'Français',
                'niveau' => 'Fonctionnaire',
                'professeur' => 'Souad Chraibi',
                'debut' => '18:00',
                'fin' => '19:00',
                'presents' => ['Hassan Amrani', 'Nadia Belhaj', 'Karim Fassi'],
                'absents' => ['Leila Saad', 'Youssef Kadiri'],
                'tone' => 'amber',
            ],
            [
                'id' => 'salle-12',
                'nom' => 'Salle 12',
                'matiere' => 'Physique',
                'niveau' => 'Lycée',
                'professeur' => 'Imane Bouzid',
                'debut' => '18:30',
                'fin' => '20:00',
                'presents' => ['Omar Naciri', 'Sara El Ouardi', 'Hamza Berrada', 'Rania Toumi'],
                'absents' => ['Adam Lahbabi'],
                'tone' => 'teal',
            ],
        ];
    }
}

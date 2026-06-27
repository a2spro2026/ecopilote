<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        return view('admin.dashboard', [
            'modules' => $user->modules(),
            'data' => $user->isSuperAdmin() ? $this->analytics() : null,
        ]);
    }

    /**
     * Données de synthèse affichées sur le tableau de bord du superadmin.
     * (Valeurs de démonstration — à remplacer par les vraies requêtes.)
     */
    private function analytics(): array
    {
        return [
            'today' => \Illuminate\Support\Carbon::now()->locale('fr')->isoFormat('dddd D MMMM YYYY'),

            'cards' => [
                ['label' => 'Effectif total',   'value' => '1 240', 'unit' => 'étudiants', 'trend' => '+4,2%', 'up' => true,  'color' => 'from-blue-500 to-indigo-500',   'icon' => 'M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342'],
                ['label' => 'Chiffre du mois',  'value' => '320 000', 'unit' => 'MAD', 'trend' => '+8,1%', 'up' => true,  'color' => 'from-emerald-500 to-teal-500', 'icon' => 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                ['label' => 'Total charges',    'value' => '145 000', 'unit' => 'MAD', 'trend' => '+2,4%', 'up' => false, 'color' => 'from-rose-500 to-pink-500',     'icon' => 'M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941'],
                ['label' => 'Total salaires',   'value' => '98 000',  'unit' => 'MAD', 'trend' => '0,0%', 'up' => true,  'color' => 'from-amber-500 to-orange-500', 'icon' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z'],
            ],

            'months'    => ['Sep', 'Oct', 'Nov', 'Déc', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            'revenue'   => [280, 295, 310, 305, 320, 330, 325, 340, 355, 360],
            'charges'   => [120, 130, 128, 140, 145, 138, 150, 148, 155, 160],
            'effectifs' => [1180, 1195, 1205, 1210, 1220, 1225, 1230, 1235, 1238, 1240],

            'niveaux' => [
                ['niveau' => 'Maternelle', 'classes' => 6,  'etudiants' => 180, 'color' => 'bg-pink-500'],
                ['niveau' => 'Primaire',   'classes' => 14, 'etudiants' => 420, 'color' => 'bg-amber-500'],
                ['niveau' => 'Collège',    'classes' => 12, 'etudiants' => 380, 'color' => 'bg-emerald-500'],
                ['niveau' => 'Lycée',      'classes' => 9,  'etudiants' => 260, 'color' => 'bg-blue-600'],
            ],

            'emploi' => [
                'slots' => ['08:00 - 10:00', '10:15 - 12:15', '14:00 - 16:00'],
                'jours' => ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'],
                'grille' => [
                    ['Mathématiques', 'Physique', 'Sport', 'Français', 'Anglais'],
                    ['Français', 'SVT', 'Arts', 'Mathématiques', 'Histoire-Géo'],
                    ['Anglais', 'Informatique', '—', 'Physique', 'Éducation civique'],
                ],
            ],

            'activites' => [
                ['activite' => 'Football',  'jour' => 'Mercredi', 'horaire' => '14:00 - 16:00', 'responsable' => 'M. Karim',  'lieu' => 'Terrain A'],
                ['activite' => 'Théâtre',   'jour' => 'Vendredi', 'horaire' => '15:00 - 17:00', 'responsable' => 'Mme Salma', 'lieu' => 'Salle culturelle'],
                ['activite' => 'Robotique', 'jour' => 'Mardi',    'horaire' => '16:00 - 18:00', 'responsable' => 'M. Younes', 'lieu' => 'Labo info'],
                ['activite' => 'Musique',   'jour' => 'Jeudi',    'horaire' => '15:00 - 16:30', 'responsable' => 'Mme Nadia', 'lieu' => 'Salle musique'],
            ],

            'vacances' => [
                ['nom' => 'Vacances de la Toussaint', 'debut' => '20 Oct 2025', 'fin' => '03 Nov 2025', 'jours' => 14],
                ['nom' => "Vacances d'hiver",          'debut' => '22 Déc 2025', 'fin' => '05 Jan 2026', 'jours' => 14],
                ['nom' => 'Vacances de printemps',     'debut' => '16 Mar 2026', 'fin' => '30 Mar 2026', 'jours' => 14],
                ['nom' => "Vacances d'été",            'debut' => '06 Juil 2026', 'fin' => '31 Aoû 2026', 'jours' => 56],
            ],
        ];
    }

    public function module(Request $request, string $key)
    {
        $module = config("admin.modules.$key");

        abort_if(! $module, 404);

        return view('admin.module', [
            'key' => $key,
            'module' => $module,
        ]);
    }
}

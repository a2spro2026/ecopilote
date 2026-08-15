<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return view('admin.subjects.index', [
            'matieres' => $this->demoSubjects(),
        ]);
    }

    /**
     * Données de démonstration — à remplacer par les agrégats métier.
     *
     * @return list<array{nom: string, effectif: int, revenus: string, evolution: string, up: bool, tone: string}>
     */
    private function demoSubjects(): array
    {
        return [
            [
                'nom' => 'Français',
                'effectif' => 42,
                'revenus' => '12 800.00',
                'evolution' => '+6,2 %',
                'up' => true,
                'tone' => 'blue',
            ],
            [
                'nom' => 'Anglais',
                'effectif' => 38,
                'revenus' => '11 450.00',
                'evolution' => '+4,1 %',
                'up' => true,
                'tone' => 'emerald',
            ],
            [
                'nom' => 'Espagnol',
                'effectif' => 21,
                'revenus' => '6 200.00',
                'evolution' => '+2,8 %',
                'up' => true,
                'tone' => 'amber',
            ],
            [
                'nom' => 'Allemand',
                'effectif' => 14,
                'revenus' => '4 100.00',
                'evolution' => '-1,4 %',
                'up' => false,
                'tone' => 'violet',
            ],
            [
                'nom' => 'Mathématique',
                'effectif' => 56,
                'revenus' => '18 900.00',
                'evolution' => '+9,5 %',
                'up' => true,
                'tone' => 'indigo',
            ],
            [
                'nom' => 'Physique',
                'effectif' => 29,
                'revenus' => '9 750.00',
                'evolution' => '+3,0 %',
                'up' => true,
                'tone' => 'teal',
            ],
            [
                'nom' => 'Science vie et terre',
                'effectif' => 24,
                'revenus' => '7 320.00',
                'evolution' => '+1,7 %',
                'up' => true,
                'tone' => 'green',
            ],
            [
                'nom' => 'Programme Scolaire',
                'effectif' => 67,
                'revenus' => '22 400.00',
                'evolution' => '+5,4 %',
                'up' => true,
                'tone' => 'rose',
            ],
            [
                'nom' => 'Coran',
                'effectif' => 33,
                'revenus' => '8 650.00',
                'evolution' => '+7,2 %',
                'up' => true,
                'tone' => 'emerald',
            ],
        ];
    }
}

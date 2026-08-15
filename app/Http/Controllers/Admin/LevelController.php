<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return view('admin.levels.index', [
            'niveaux' => $this->demoLevels(),
        ]);
    }

    /**
     * Données de démonstration — à remplacer par les agrégats métier.
     *
     * @return list<array{nom: string, effectif: int, revenus: string, evolution: string, up: bool, tone: string}>
     */
    private function demoLevels(): array
    {
        return [
            [
                'nom' => 'Primaire',
                'effectif' => 48,
                'revenus' => '14 200.00',
                'evolution' => '+5,1 %',
                'up' => true,
                'tone' => 'blue',
            ],
            [
                'nom' => 'Collège',
                'effectif' => 61,
                'revenus' => '19 800.00',
                'evolution' => '+6,8 %',
                'up' => true,
                'tone' => 'emerald',
            ],
            [
                'nom' => 'Lycée',
                'effectif' => 54,
                'revenus' => '21 350.00',
                'evolution' => '+4,3 %',
                'up' => true,
                'tone' => 'indigo',
            ],
            [
                'nom' => 'Université',
                'effectif' => 27,
                'revenus' => '10 900.00',
                'evolution' => '+2,2 %',
                'up' => true,
                'tone' => 'violet',
            ],
            [
                'nom' => 'Fonctionnaire',
                'effectif' => 18,
                'revenus' => '7 450.00',
                'evolution' => '-0,8 %',
                'up' => false,
                'tone' => 'amber',
            ],
            [
                'nom' => 'Divers',
                'effectif' => 12,
                'revenus' => '3 780.00',
                'evolution' => '+1,5 %',
                'up' => true,
                'tone' => 'teal',
            ],
        ];
    }
}

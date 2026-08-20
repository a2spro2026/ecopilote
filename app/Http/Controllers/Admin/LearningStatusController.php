<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Support\SubjectAbbreviation;
use Illuminate\Http\Request;

class LearningStatusController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $rows = [];

        $students = Student::query()
            ->where('etat', '!=', Student::ETAT_SUSPENDU)
            ->orderBy('nom_complet')
            ->orderBy('id')
            ->get();

        foreach ($students as $student) {
            $subjects = array_values(array_filter(array_map(
                'trim',
                preg_split('/[,;\/|]+/', (string) $student->matiere) ?: []
            )));

            if ($subjects === []) {
                $subjects = [''];
            }

            foreach ($subjects as $subject) {
                $rows[] = [
                    'code' => $student->displayId(),
                    'nom' => $student->nom_complet,
                    'matiere' => $subject,
                    'matiere_abbr' => SubjectAbbreviation::display($subject),
                    'seances' => 0,
                    'prof' => '—',
                    'classe' => '—',
                    'jours' => '—',
                ];
            }
        }

        return view('admin.students.learning', [
            'rows' => $rows,
            'months' => $this->months(),
            'currentMonth' => now()->format('Y-m'),
            'matieres' => $this->subjects(),
        ]);
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function months(): array
    {
        $labels = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];

        $items = [];
        $cursor = now()->startOfMonth();

        for ($i = 0; $i < 12; $i++) {
            $date = $cursor->copy()->subMonths($i);
            $items[] = [
                'value' => $date->format('Y-m'),
                'label' => $labels[(int) $date->format('n')].' '.$date->format('Y'),
            ];
        }

        return $items;
    }

    /**
     * @return list<string>
     */
    private function subjects(): array
    {
        return [
            'Mathématiques',
            'Physique-Chimie',
            'Français',
            'Anglais',
            'SVT',
            'Histoire-Géographie',
            'Informatique',
            'Arabe',
        ];
    }
}

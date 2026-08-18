<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $niveaux = $this->levelRows();

        return view('admin.levels.index', compact('niveaux'));
    }

    public function print(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $niveaux = $this->levelRows();

        return view('admin.levels.print', compact('niveaux'));
    }

    /**
     * @return list<array{nom: string, key: string, tone: string, etudiants: int, profs: int}>
     */
    private function levelRows(): array
    {
        $rows = [];

        foreach ($this->levelCatalog() as $level) {
            $rows[] = array_merge($level, [
                'etudiants' => $this->countStudents($level['patterns']),
                'profs' => $this->countTeachers($level['patterns']),
            ]);
        }

        return $rows;
    }

    /**
     * @return list<array{nom: string, key: string, tone: string, patterns: list<string>}>
     */
    private function levelCatalog(): array
    {
        return [
            ['nom' => 'Primaire', 'key' => 'primaire', 'tone' => 'emerald', 'patterns' => ['primaire']],
            ['nom' => 'Collège', 'key' => 'college', 'tone' => 'blue', 'patterns' => ['college', 'collège']],
            ['nom' => 'Lycée', 'key' => 'lycee', 'tone' => 'violet', 'patterns' => ['lycee', 'lycée']],
            ['nom' => 'Coran', 'key' => 'coran', 'tone' => 'amber', 'patterns' => ['coran', 'coranique']],
        ];
    }

    /**
     * @param  list<string>  $patterns
     */
    private function countStudents(array $patterns): int
    {
        return Student::query()
            ->where('etat', Student::ETAT_ACTIF)
            ->where(function ($query) use ($patterns) {
                foreach ($patterns as $pattern) {
                    $query->orWhere('niveau_scolaire', 'like', '%'.$pattern.'%');
                }
            })
            ->count();
    }

    /**
     * @param  list<string>  $patterns
     */
    private function countTeachers(array $patterns): int
    {
        return Teacher::query()
            ->where('etat', Teacher::ETAT_ACTIF)
            ->where(function ($query) use ($patterns) {
                foreach ($patterns as $pattern) {
                    $query->orWhere('niveau', 'like', '%'.$pattern.'%');
                }
            })
            ->count();
    }
}

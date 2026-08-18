<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $matieres = $this->subjectRows();

        return view('admin.subjects.index', compact('matieres'));
    }

    public function print(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $matieres = $this->subjectRows();

        return view('admin.subjects.print', compact('matieres'));
    }

    /**
     * @return list<array{
     *     nom: string,
     *     flag: ?string,
     *     icon: ?string,
     *     tone: string,
     *     profs: int,
     *     etudiants: int,
     *     heures_mois: int,
     *     revenue: int,
     *     paiement: int,
     *     benefice: int
     * }>
     */
    private function subjectRows(): array
    {
        $rows = [];

        foreach ($this->subjectCatalog() as $subject) {
            $profs = $this->countTeachers($subject['nom']);
            $etudiants = $this->countStudents($subject['nom']);
            $heuresMois = ($profs * 28) + ($etudiants * 3);
            $revenue = ($etudiants * 520) + ($profs * 180);
            $paiement = ($profs * 2400) + (int) round($revenue * 0.38);
            $benefice = max(0, $revenue - $paiement);

            $rows[] = array_merge($subject, [
                'profs' => $profs,
                'etudiants' => $etudiants,
                'heures_mois' => $heuresMois,
                'revenue' => $revenue,
                'paiement' => $paiement,
                'benefice' => $benefice,
            ]);
        }

        return $rows;
    }

    /**
     * @return list<array{nom: string, flag: ?string, icon: ?string, tone: string}>
     */
    private function subjectCatalog(): array
    {
        return [
            ['nom' => 'Mathématiques', 'flag' => null, 'icon' => 'math', 'tone' => 'blue'],
            ['nom' => 'Physique-Chimie', 'flag' => null, 'icon' => 'science', 'tone' => 'violet'],
            ['nom' => 'Français', 'flag' => 'fr', 'icon' => null, 'tone' => 'indigo'],
            ['nom' => 'Anglais', 'flag' => 'gb', 'icon' => null, 'tone' => 'emerald'],
            ['nom' => 'SVT', 'flag' => null, 'icon' => 'leaf', 'tone' => 'green'],
            ['nom' => 'Histoire-Géographie', 'flag' => null, 'icon' => 'globe', 'tone' => 'amber'],
            ['nom' => 'Informatique', 'flag' => null, 'icon' => 'code', 'tone' => 'teal'],
            ['nom' => 'Arabe', 'flag' => 'ma', 'icon' => null, 'tone' => 'rose'],
        ];
    }

    private function countTeachers(string $subject): int
    {
        return Teacher::query()
            ->where('etat', Teacher::ETAT_ACTIF)
            ->where('matiere', 'like', '%'.$subject.'%')
            ->count();
    }

    private function countStudents(string $subject): int
    {
        return Student::query()
            ->where('etat', Student::ETAT_ACTIF)
            ->where('matiere', 'like', '%'.$subject.'%')
            ->count();
    }
}

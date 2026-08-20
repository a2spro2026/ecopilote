<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudyGroup;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $groups = StudyGroup::query()
            ->with(['teacher', 'students'])
            ->orderBy('id')
            ->get();

        return view('admin.groups.index', [
            'groups' => $groups,
            'nextCode' => 'GR-'.str_pad((string) ((int) StudyGroup::query()->max('id') + 1), 4, '0', STR_PAD_LEFT),
            'matieres' => $this->subjects(),
            'niveaux' => $this->levels(),
            'professeurs' => $this->teachersPayload(),
            'eleves' => $this->studentsPayload(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $data = $request->validate([
            'group_id' => ['nullable', 'integer', 'exists:study_groups,id'],
            'matiere' => ['required', 'string', Rule::in($this->subjects())],
            'niveau' => ['required', 'string', Rule::in(array_keys($this->levels()))],
            'teacher_id' => ['required', 'integer', 'exists:teachers,id'],
            'eleves' => ['required', 'array', 'min:1'],
            'eleves.*' => ['integer', 'exists:students,id'],
        ], [
            'eleves.required' => 'Cochez au moins un élève.',
        ]);

        $teacher = collect($this->teachersPayload())->firstWhere('id', (int) $data['teacher_id']);
        if (! $teacher || ! $this->teacherMatches($teacher, $data['matiere'], $data['niveau'])) {
            throw ValidationException::withMessages([
                'teacher_id' => 'Ce professeur ne correspond pas à la matière et au niveau.',
            ]);
        }

        $allowedIds = collect($this->studentsPayload())
            ->filter(fn (array $student) => $this->studentMatches($student, $data['matiere'], $data['niveau']))
            ->pluck('id')
            ->all();

        foreach ($data['eleves'] as $eleveId) {
            if (! in_array((int) $eleveId, $allowedIds, true)) {
                throw ValidationException::withMessages([
                    'eleves' => 'Chaque élève doit correspondre à sa demande d’inscription (matière et niveau).',
                ]);
            }
        }

        if (! empty($data['group_id'])) {
            $group = StudyGroup::findOrFail($data['group_id']);
            $group->update([
                'matiere' => $data['matiere'],
                'niveau' => $data['niveau'],
                'teacher_id' => $data['teacher_id'],
            ]);
            $message = 'Groupe '.$group->displayId().' mis à jour.';
        } else {
            $group = StudyGroup::create([
                'matiere' => $data['matiere'],
                'niveau' => $data['niveau'],
                'teacher_id' => $data['teacher_id'],
            ]);
            $message = 'Groupe '.$group->displayId().' ajouté.';
        }

        $group->students()->sync($data['eleves']);

        return redirect()
            ->route('admin.page.groupes')
            ->with('status', $message);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function teachersPayload(): array
    {
        return Teacher::query()
            ->where('etat', Teacher::ETAT_ACTIF)
            ->orderBy('nom_complet')
            ->get()
            ->map(fn (Teacher $teacher) => [
                'id' => $teacher->id,
                'nom' => $teacher->nom_complet,
                'matieres' => $this->splitSubjects((string) $teacher->matiere),
                'niveau_key' => $this->levelKeyFromText((string) $teacher->niveau),
            ])
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function studentsPayload(): array
    {
        $labels = $this->levels();

        return Student::query()
            ->where('etat', '!=', Student::ETAT_SUSPENDU)
            ->orderBy('nom_complet')
            ->get()
            ->map(function (Student $student) use ($labels) {
                $key = $this->levelKeyFromText((string) $student->niveau_scolaire);

                return [
                    'id' => $student->id,
                    'code' => $student->displayId(),
                    'nom' => $student->nom_complet,
                    'niveau_key' => $key,
                    'niveau' => ($key && isset($labels[$key])) ? $labels[$key] : $student->niveau_scolaire,
                    'matieres' => $this->splitSubjects((string) $student->matiere),
                    'paiement' => $student->subjectFee(),
                ];
            })
            ->all();
    }

    /**
     * @param  array{matieres: list<string>, niveau_key: ?string}  $teacher
     */
    private function teacherMatches(array $teacher, string $matiere, string $niveau): bool
    {
        if (! $this->hasNormalizedSubject($teacher['matieres'] ?? [], $matiere)) {
            return false;
        }

        $key = $teacher['niveau_key'] ?? null;

        return $key === null || $key === $niveau;
    }

    /**
     * @param  array{matieres: list<string>, niveau_key: ?string}  $student
     */
    private function studentMatches(array $student, string $matiere, string $niveau): bool
    {
        if (! $this->hasNormalizedSubject($student['matieres'] ?? [], $matiere)) {
            return false;
        }

        $key = $student['niveau_key'] ?? null;

        return $key === null || $key === $niveau;
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

    /**
     * @return array<string, string>
     */
    private function levels(): array
    {
        return [
            'primaire' => 'Primaire',
            'college' => 'Collège',
            'lycee' => 'Lycée',
            'coran' => 'Coran',
        ];
    }

    /**
     * @return list<string>
     */
    private function splitSubjects(string $raw): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/[,;\/|]+/', $raw) ?: [])));
    }

    private function normalize(string $value): string
    {
        $value = trim(mb_strtolower($value, 'UTF-8'));

        return strtr($value, [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', 'å' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n', 'ÿ' => 'y', 'œ' => 'oe', 'æ' => 'ae',
        ]);
    }

    private function hasNormalizedSubject(array $subjects, string $wanted): bool
    {
        $needle = $this->normalize($wanted);

        foreach ($subjects as $subject) {
            if ($this->normalize((string) $subject) === $needle) {
                return true;
            }
        }

        return false;
    }

    private function levelKeyFromText(string $value): ?string
    {
        $text = $this->normalize($value);

        if ($text === '' || str_contains($text, 'non renseign')) {
            return null;
        }

        foreach ($this->levels() as $key => $label) {
            if ($text === $key || $text === $this->normalize($label)) {
                return $key;
            }
        }

        if (str_contains($text, 'coran')) {
            return 'coran';
        }
        if (str_contains($text, 'prim') || preg_match('/\b(cp|ce1|ce2|cm1|cm2)\b/', $text)) {
            return 'primaire';
        }
        if (str_contains($text, 'universit') || str_contains($text, 'lyc') || str_contains($text, '2nde') || str_contains($text, '1ere') || str_contains($text, 'terminale') || str_contains($text, 'bac')) {
            return 'lycee';
        }
        if (str_contains($text, 'coll') || preg_match('/\b(6eme|5eme|4eme|3eme|3e)\b/', $text)) {
            return 'college';
        }

        return null;
    }
}

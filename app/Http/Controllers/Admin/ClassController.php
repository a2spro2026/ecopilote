<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $classes = $this->demoClasses();

        return view('admin.classes.index', [
            'classes' => $classes,
            'filterOptions' => $this->filterOptionsFrom($classes),
        ]);
    }

    public function show(Request $request, int $classe)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $item = collect($this->demoClasses())->firstWhere('id', $classe);
        abort_unless($item !== null, 404);

        return view('admin.classes.show', [
            'classe' => $item,
        ]);
    }

    public function create(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return view('admin.classes.create', [
            'classNumber' => 'CL-0001',
            'matieres' => $this->subjects(),
            'niveaux' => $this->levels(),
            'jours' => ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
            'professeurs' => array_values(array_filter(
                $this->validatedTeachers(),
                fn (array $t) => ($t['statut'] ?? '') === 'validé'
            )),
            'eleves' => $this->students(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $data = $request->validate([
            'numero' => ['required', 'string'],
            'matiere' => ['required', 'string', 'in:'.implode(',', $this->subjects())],
            'niveau' => ['required', 'string', 'in:'.implode(',', array_keys($this->levels()))],
            'type' => ['required', 'in:individuelle,groupe'],
            'statut' => ['required', 'in:active,suspendue'],
            'professeur_id' => ['required', 'integer'],
            'eleves' => ['required', 'array', 'min:1'],
            'eleves.*' => ['integer'],
            'jours' => ['required', 'array', 'min:1'],
            'jours.*' => ['string'],
            'heure_debut' => ['required', 'date_format:H:i'],
            'heure_fin' => ['required', 'date_format:H:i'],
            'date_debut' => ['required', 'date'],
            'sans_date_fin' => ['nullable', 'boolean'],
            'date_fin' => ['nullable', 'date'],
        ]);

        $teachers = collect($this->validatedTeachers());
        $teacher = $teachers->firstWhere('id', (int) $data['professeur_id']);

        if (! $teacher || ($teacher['statut'] ?? '') !== 'validé') {
            throw ValidationException::withMessages([
                'professeur_id' => 'Seuls les professeurs validés peuvent être affectés.',
            ]);
        }

        if (! $this->hasNormalizedSubject($teacher['matieres'] ?? [], $data['matiere'])) {
            throw ValidationException::withMessages([
                'matiere' => 'Ce professeur n’enseigne pas la matière sélectionnée.',
            ]);
        }

        if ($data['type'] === 'individuelle' && count($data['eleves']) > 1) {
            throw ValidationException::withMessages([
                'eleves' => 'Une classe individuelle ne peut contenir qu’un seul élève.',
            ]);
        }

        $allowedStudentIds = collect($this->students())
            ->filter(fn (array $student) => $this->studentMatchesFilters($student, $data['matiere'], $data['niveau']))
            ->pluck('id')
            ->all();

        foreach ($data['eleves'] as $eleveId) {
            if (! in_array((int) $eleveId, $allowedStudentIds, true)) {
                throw ValidationException::withMessages([
                    'eleves' => 'Chaque élève doit correspondre à la matière et au niveau sélectionnés.',
                ]);
            }
        }

        if (strtotime($data['heure_fin']) <= strtotime($data['heure_debut'])) {
            throw ValidationException::withMessages([
                'heure_fin' => 'L’heure de fin doit être postérieure à l’heure de début.',
            ]);
        }

        $sansFin = $request->boolean('sans_date_fin');
        if (! $sansFin) {
            if (empty($data['date_fin'])) {
                throw ValidationException::withMessages([
                    'date_fin' => 'Indiquez une date de fin ou cochez « Sans date de fin ».',
                ]);
            }
            if (strtotime($data['date_fin']) < strtotime($data['date_debut'])) {
                throw ValidationException::withMessages([
                    'date_fin' => 'La date de fin ne peut pas être antérieure à la date de début.',
                ]);
            }
        }

        // Interface uniquement : pas d’enregistrement en base métier pour cette étape.
        return redirect()
            ->route('admin.classes.create')
            ->with('success', 'Classe '.$data['numero'].' créée avec succès.');
    }

    /**
     * Données de démonstration — à remplacer par le repository métier.
     *
     * @return list<array<string, mixed>>
     */
    private function demoClasses(): array
    {
        return [];
    }

    /**
     * @param  list<array<string, mixed>>  $classes
     * @return array{matieres: list<string>, niveaux: list<string>, professeurs: list<string>}
     */
    private function filterOptionsFrom(array $classes): array
    {
        return [
            'matieres' => collect($classes)->pluck('matiere')->unique()->sort()->values()->all(),
            'niveaux' => collect($classes)->pluck('niveau')->unique()->sort()->values()->all(),
            'professeurs' => collect($classes)->pluck('professeur.nom')->unique()->sort()->values()->all(),
        ];
    }

    private function validatedTeachers(): array
    {
        return Teacher::query()
            ->where('etat', Teacher::ETAT_ACTIF)
            ->orderBy('id')
            ->get()
            ->map(fn (Teacher $teacher) => [
                'id' => $teacher->id,
                'nom' => $teacher->nom_complet,
                'matieres' => $this->splitSubjects((string) $teacher->matiere),
                'niveaux' => array_values(array_filter([(string) $teacher->niveau])),
                'statut' => 'validé',
            ])
            ->all();
    }

    private function students(): array
    {
        $labels = $this->levels();

        return Student::query()
            ->where('etat', '!=', Student::ETAT_SUSPENDU)
            ->orderBy('id')
            ->get()
            ->map(function (Student $student) use ($labels) {
                $key = $this->levelKeyFromText((string) $student->niveau_scolaire);

                return [
                    'id' => $student->id,
                    'nom' => $student->nom_complet,
                    'niveau' => ($key && isset($labels[$key])) ? $labels[$key] : $student->niveau_scolaire,
                    'niveau_key' => $key,
                    'matieres' => $this->splitSubjects((string) $student->matiere),
                ];
            })
            ->all();
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
        if (str_contains($text, 'lyc') || str_contains($text, '2nde') || str_contains($text, '1ere') || str_contains($text, 'terminale') || str_contains($text, 'bac')) {
            return 'lycee';
        }
        if (str_contains($text, 'coll') || preg_match('/\b(6eme|5eme|4eme|3eme|3e)\b/', $text)) {
            return 'college';
        }

        return null;
    }

    /**
     * @param  array{niveau_key:?string, matieres:list<string>}  $student
     */
    private function studentMatchesFilters(array $student, string $matiere, string $niveau): bool
    {
        if (! $this->hasNormalizedSubject($student['matieres'] ?? [], $matiere)) {
            return false;
        }

        $key = $student['niveau_key'] ?? null;

        return $key === null || $key === $niveau;
    }
}

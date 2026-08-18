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
            'matieres' => ['Mathématiques', 'Physique-Chimie', 'Français', 'Anglais', 'SVT', 'Histoire-Géo'],
            'niveaux' => ['6ème', '5ème', '4ème', '3ème', '2nde', '1ère', 'Terminale'],
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
            'matiere' => ['required', 'string'],
            'niveau' => ['required', 'string'],
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

        if (! in_array($data['matiere'], $teacher['matieres'], true)) {
            throw ValidationException::withMessages([
                'matiere' => 'La matière sélectionnée est incompatible avec ce professeur.',
            ]);
        }

        if (! in_array($data['niveau'], $teacher['niveaux'], true)) {
            throw ValidationException::withMessages([
                'niveau' => 'Le niveau sélectionné est incompatible avec ce professeur.',
            ]);
        }

        if ($data['type'] === 'individuelle' && count($data['eleves']) > 1) {
            throw ValidationException::withMessages([
                'eleves' => 'Une classe individuelle ne peut contenir qu’un seul élève.',
            ]);
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
                'matieres' => array_values(array_filter(array_map('trim', explode(',', (string) $teacher->matiere)))),
                'niveaux' => array_values(array_filter([(string) $teacher->niveau])),
                'statut' => 'validé',
            ])
            ->all();
    }

    private function students(): array
    {
        return Student::query()
            ->where('etat', Student::ETAT_ACTIF)
            ->orderBy('id')
            ->get()
            ->map(fn (Student $student) => [
                'id' => $student->id,
                'nom' => $student->nom_complet,
                'niveau' => $student->niveau_scolaire,
            ])
            ->all();
    }
}

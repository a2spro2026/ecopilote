<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $teachers = collect($this->validatedTeachers())->keyBy('id');
        $students = collect($this->students())->keyBy('id');

        $build = function (
            int $id,
            string $numero,
            string $statut,
            string $matiere,
            string $niveau,
            string $type,
            int $professeurId,
            array $eleveIds,
            array $jours,
            string $heureDebut,
            string $heureFin,
            string $dateDebut,
            ?string $dateFin,
            ?array $presence,
            array $seances
        ) use ($teachers, $students): array {
            $prof = $teachers->get($professeurId);
            $eleves = collect($eleveIds)->map(function (int $eid) use ($students) {
                $s = $students->get($eid);

                return [
                    'id' => $eid,
                    'nom' => $s['nom'] ?? 'Élève',
                    'niveau' => $s['niveau'] ?? '',
                    'statut' => 'actif',
                ];
            })->values()->all();

            return [
                'id' => $id,
                'numero' => $numero,
                'statut' => $statut,
                'matiere' => $matiere,
                'niveau' => $niveau,
                'type' => $type,
                'professeur' => [
                    'id' => $professeurId,
                    'nom' => $prof['nom'] ?? '—',
                    'matieres' => $prof['matieres'] ?? [],
                    'statut' => $prof['statut'] ?? 'validé',
                ],
                'eleves' => $eleves,
                'jours' => $jours,
                'heure_debut' => $heureDebut,
                'heure_fin' => $heureFin,
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin,
                'presence' => $presence,
                'seances' => $seances,
            ];
        };

        return [
            $build(1, 'CL-0001', 'active', 'Mathématiques', '2nde', 'groupe', 1, [1, 6, 3, 7], ['Lundi', 'Mercredi'], '18:00', '19:30', '2026-01-06', null, ['presents' => 3, 'absents' => 1], [
                ['date' => '12 Août 2026', 'horaire' => '18:00 – 19:30', 'statut' => 'terminee', 'presents' => 3, 'absents' => 1],
                ['date' => '10 Août 2026', 'horaire' => '18:00 – 19:30', 'statut' => 'terminee', 'presents' => 4, 'absents' => 0],
                ['date' => '13 Août 2026', 'horaire' => '18:00 – 19:30', 'statut' => 'programmee', 'presents' => 0, 'absents' => 0],
            ]),
            $build(2, 'CL-0002', 'active', 'Anglais', '3ème', 'individuelle', 3, [2], ['Mardi'], '17:00', '18:00', '2026-02-01', '2026-12-15', ['presents' => 1, 'absents' => 0], [
                ['date' => '11 Août 2026', 'horaire' => '17:00 – 18:00', 'statut' => 'terminee', 'presents' => 1, 'absents' => 0],
                ['date' => '12 Août 2026', 'horaire' => '17:00 – 18:00', 'statut' => 'active', 'presents' => 1, 'absents' => 0],
            ]),
            $build(3, 'CL-0003', 'suspendue', 'Physique-Chimie', '1ère', 'groupe', 2, [3, 7], ['Jeudi', 'Samedi'], '16:00', '17:30', '2025-11-01', null, ['presents' => 1, 'absents' => 1], [
                ['date' => '01 Août 2026', 'horaire' => '16:00 – 17:30', 'statut' => 'annulee', 'presents' => 0, 'absents' => 0],
                ['date' => '25 Juillet 2026', 'horaire' => '16:00 – 17:30', 'statut' => 'terminee', 'presents' => 1, 'absents' => 1],
            ]),
            $build(4, 'CL-0004', 'terminee', 'Français', '1ère', 'groupe', 4, [3, 7], ['Vendredi'], '19:00', '20:00', '2025-09-01', '2026-06-30', ['presents' => 2, 'absents' => 0], [
                ['date' => '20 Juin 2026', 'horaire' => '19:00 – 20:00', 'statut' => 'terminee', 'presents' => 2, 'absents' => 0],
                ['date' => '13 Juin 2026', 'horaire' => '19:00 – 20:00', 'statut' => 'terminee', 'presents' => 1, 'absents' => 1],
            ]),
            $build(5, 'CL-0005', 'active', 'SVT', 'Terminale', 'groupe', 2, [5], ['Lundi'], '10:00', '11:30', '2026-03-01', null, null, []),
            $build(6, 'CL-0006', 'active', 'Histoire-Géo', '4ème', 'groupe', 4, [4, 8], ['Mercredi', 'Vendredi'], '15:00', '16:00', '2026-01-15', null, ['presents' => 2, 'absents' => 0], [
                ['date' => '08 Août 2026', 'horaire' => '15:00 – 16:00', 'statut' => 'terminee', 'presents' => 2, 'absents' => 0],
                ['date' => '06 Août 2026', 'horaire' => '15:00 – 16:00', 'statut' => 'terminee', 'presents' => 1, 'absents' => 1],
            ]),
            $build(7, 'CL-0007', 'suspendue', 'Mathématiques', 'Terminale', 'individuelle', 1, [5], ['Samedi'], '09:00', '10:30', '2026-04-01', null, ['presents' => 0, 'absents' => 1], [
                ['date' => '09 Août 2026', 'horaire' => '09:00 – 10:30', 'statut' => 'terminee', 'presents' => 0, 'absents' => 1],
            ]),
            $build(8, 'CL-0008', 'active', 'Français', '5ème', 'groupe', 3, [2, 8], ['Mardi', 'Jeudi'], '18:30', '19:30', '2026-05-01', null, ['presents' => 2, 'absents' => 0], [
                ['date' => '12 Août 2026', 'horaire' => '18:30 – 19:30', 'statut' => 'programmee', 'presents' => 0, 'absents' => 0],
                ['date' => '07 Août 2026', 'horaire' => '18:30 – 19:30', 'statut' => 'terminee', 'presents' => 2, 'absents' => 0],
            ]),
        ];
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
        return [
            [
                'id' => 1,
                'nom' => 'Mme Alami',
                'matieres' => ['Mathématiques', 'Physique-Chimie'],
                'niveaux' => ['2nde', '1ère', 'Terminale'],
                'statut' => 'validé',
            ],
            [
                'id' => 2,
                'nom' => 'M. Benali',
                'matieres' => ['Physique-Chimie', 'SVT'],
                'niveaux' => ['1ère', 'Terminale'],
                'statut' => 'validé',
            ],
            [
                'id' => 3,
                'nom' => 'Mme Carter',
                'matieres' => ['Anglais', 'Français'],
                'niveaux' => ['5ème', '4ème', '3ème', '2nde'],
                'statut' => 'validé',
            ],
            [
                'id' => 4,
                'nom' => 'M. Idrissi',
                'matieres' => ['Français', 'Histoire-Géo'],
                'niveaux' => ['6ème', '5ème', '4ème', '3ème'],
                'statut' => 'validé',
            ],
            // Non validé — ne doit jamais apparaître dans la sélection
            [
                'id' => 99,
                'nom' => 'M. Candidat',
                'matieres' => ['Mathématiques'],
                'niveaux' => ['Terminale'],
                'statut' => 'en_attente',
            ],
        ];
    }

    private function students(): array
    {
        return [
            ['id' => 1, 'nom' => 'Yassine Bennani', 'niveau' => '2nde'],
            ['id' => 2, 'nom' => 'Sara Mansouri', 'niveau' => '3ème'],
            ['id' => 3, 'nom' => 'Amine Kabbaj', 'niveau' => '1ère'],
            ['id' => 4, 'nom' => 'Nour Lahlou', 'niveau' => '4ème'],
            ['id' => 5, 'nom' => 'Rania El Fassi', 'niveau' => 'Terminale'],
            ['id' => 6, 'nom' => 'Omar Tazi', 'niveau' => '2nde'],
            ['id' => 7, 'nom' => 'Imane Chraibi', 'niveau' => '1ère'],
            ['id' => 8, 'nom' => 'Mehdi Alaoui', 'niveau' => '3ème'],
        ];
    }
}

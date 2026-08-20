<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentApplication;
use App\Support\EcopiloteIdentity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function applications(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $demandes = StudentApplication::query()
            ->with('student')
            ->latest()
            ->get();

        return view('admin.students.applications', [
            'demandes' => $demandes,
            'matieres' => $this->subjects(),
            'niveaux' => $this->levels(),
            'emailSuffix' => EcopiloteIdentity::emailSuffix(),
        ]);
    }

    public function index(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $eleves = Student::query()
            ->orderBy('id')
            ->get();

        return view('admin.students.index', [
            'eleves' => $eleves,
        ]);
    }

    public function technical(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return view('admin.students.technical', [
            'eleves' => Student::query()->orderBy('id')->get(),
            'matieres' => $this->subjects(),
            'niveaux' => $this->levels(),
        ]);
    }

    public function storeTechnical(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $request->merge([
            'login' => EcopiloteIdentity::email((string) $request->input('login', '')),
        ]);

        $data = $request->validate([
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'nom_complet' => ['required', 'string', 'max:120'],
            'contact' => ['required', 'string', 'max:120'],
            'tuteur_nom' => ['nullable', 'string', 'max:120'],
            'contact_tuteur' => ['required', 'string', 'max:120'],
            'matieres' => ['required', 'array', 'min:1'],
            'matieres.*' => ['required', 'string', 'distinct', Rule::in($this->subjects())],
            'niveau_scolaire' => ['required', 'string', Rule::in(array_keys($this->levels()))],
            'paiement' => ['required', 'numeric', 'min:0'],
            'mode_paiement' => ['required', Rule::in(['virement', 'cheque', 'especes', 'versement'])],
            'periode_paiement' => ['required', Rule::in(['mois', 'trimestre', 'semestre', 'annuel'])],
            'login' => ['required', 'email', 'max:120', Rule::unique('students', 'login')->ignore($request->integer('student_id') ?: null)],
            'access_password' => ['required', 'string', 'min:6', 'max:120'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ], [
            'matieres.required' => 'Sélectionnez au moins une matière.',
        ]);

        $payload = [
            'nom_complet' => $data['nom_complet'],
            'tuteur_nom' => ($data['tuteur_nom'] ?? null) ?: null,
            'contact' => $data['contact'],
            'contact_tuteur' => $data['contact_tuteur'],
            'matiere' => implode(', ', $data['matieres']),
            'niveau_scolaire' => $data['niveau_scolaire'],
            'paiement' => number_format((float) $data['paiement'], 2, '.', ''),
            'mode_paiement' => $data['mode_paiement'],
            'periode_paiement' => $data['periode_paiement'],
            'login' => $data['login'],
            'access_password' => $data['access_password'],
        ];

        if ($request->hasFile('photo')) {
            $payload['photo_path'] = $request->file('photo')->store('profiles/students', 'public');
        }

        if (! empty($data['student_id'])) {
            $student = Student::findOrFail($data['student_id']);
            $student->update($payload);
            $message = 'Fiche de '.$student->displayId().' mise à jour.';
        } else {
            $student = Student::create([
                ...$payload,
                'ville' => 'Non renseignée',
                'type_cours' => 'en_groupe',
                'etat' => Student::ETAT_ACTIF,
            ]);
            $message = 'Fiche de '.$student->displayId().' ajoutée.';
        }

        return redirect()
            ->route('admin.students.technical')
            ->with('status', $message);
    }

    public function print(Request $request, Student $eleve)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return view('admin.students.print', ['eleve' => $eleve]);
    }

    public function show(Request $request, Student $eleve)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return view('admin.students.show', [
            'eleve' => $eleve,
        ]);
    }

    public function edit(Request $request, Student $eleve)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return view('admin.students.edit', [
            'eleve' => $eleve,
        ]);
    }

    public function update(Request $request, Student $eleve)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $data = $request->validate([
            'nom_complet' => ['required', 'string', 'max:120'],
            'access_password' => ['required', 'string', 'min:6', 'max:120'],
            'contact' => ['required', 'string', 'max:120'],
            'tuteur_nom' => ['nullable', 'string', 'max:120'],
            'contact_tuteur' => ['required', 'string', 'max:120'],
            'ville' => ['required', 'string', 'max:120'],
            'niveau_scolaire' => ['required', 'string', 'max:120'],
            'matiere' => ['required', 'string', 'max:120'],
            'type_cours' => ['required', 'in:individuel,en_groupe'],
            'etat' => ['required', Rule::in([Student::ETAT_ACTIF, Student::ETAT_EN_ATTENTE, Student::ETAT_SUSPENDU])],
            'paiement' => ['nullable', 'numeric', 'min:0'],
            'echeance' => ['nullable', 'date'],
        ]);

        if (isset($data['paiement']) && $data['paiement'] !== null && $data['paiement'] !== '') {
            $data['paiement'] = number_format((float) $data['paiement'], 2, '.', '');
        } else {
            $data['paiement'] = null;
        }

        $data['login'] = $this->uniqueLogin($data['nom_complet'], $eleve->id);
        $eleve->update($data);

        return redirect()
            ->route('admin.page.eleves')
            ->with('status', 'Élève '.$eleve->displayId().' mis à jour.');
    }

    public function suspend(Request $request, Student $eleve)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $eleve->update([
            'etat' => Student::ETAT_SUSPENDU,
        ]);

        return back()->with('status', 'Élève '.$eleve->displayId().' suspendu.');
    }

    public function validateApplication(Request $request, StudentApplication $application)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $request->merge([
            'login' => EcopiloteIdentity::email((string) $request->input('login', '')),
        ]);

        $data = $request->validate([
            'login' => [
                'required',
                'email',
                'max:120',
                Rule::unique('students', 'login')->ignore($application->student_id),
            ],
            'access_password' => ['required', 'string', 'min:6', 'max:120'],
        ]);

        DB::transaction(function () use ($application, $data) {
            if ($application->student_id) {
                $student = Student::findOrFail($application->student_id);
                $student->update([
                    'etat' => Student::ETAT_ACTIF,
                    'nom_complet' => $application->nom_complet,
                    'login' => $data['login'],
                    'access_password' => $data['access_password'],
                    'contact' => $application->contact,
                    'contact_tuteur' => $application->contact_tuteur,
                    'ville' => $application->ville,
                    'niveau_scolaire' => $application->niveau_scolaire,
                    'matiere' => $application->matiere,
                    'type_cours' => $application->type_cours,
                ]);
                if ($application->photo_path && ! $student->photo_path) {
                    $student->update(['photo_path' => $application->photo_path]);
                }
            } else {
                $student = Student::create([
                    'nom_complet' => $application->nom_complet,
                    'login' => $data['login'],
                    'access_password' => $data['access_password'],
                    'contact' => $application->contact,
                    'contact_tuteur' => $application->contact_tuteur,
                    'ville' => $application->ville,
                    'niveau_scolaire' => $application->niveau_scolaire,
                    'matiere' => $application->matiere,
                    'type_cours' => $application->type_cours,
                    'photo_path' => $application->photo_path,
                    'etat' => Student::ETAT_ACTIF,
                ]);
                $application->student_id = $student->id;
            }

            $application->login = $data['login'];
            $application->access_password = $data['access_password'];
            $application->etat = StudentApplication::ETAT_VALIDEE;
            $application->save();
        });

        return back()->with(
            'status',
            'Demande validée — accès élève : '.$data['login'].' / '.$data['access_password']
        );
    }

    public function updateApplication(Request $request, StudentApplication $application)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $request->merge([
            'login' => filled($request->input('login'))
                ? EcopiloteIdentity::email((string) $request->input('login'))
                : null,
        ]);

        $data = $request->validate([
            'nom_complet' => ['required', 'string', 'max:120'],
            'contact' => ['required', 'string', 'max:120'],
            'contact_tuteur' => ['required', 'string', 'max:120'],
            'ville' => ['required', 'string', 'max:120'],
            'niveau_scolaire' => ['required', 'string', 'max:120'],
            'matiere' => ['required', 'string', 'max:255'],
            'type_cours' => ['required', Rule::in(['individuel', 'en_groupe'])],
            'login' => [
                'nullable',
                'email',
                'max:120',
                Rule::unique('students', 'login')->ignore($application->student_id),
            ],
            'access_password' => ['nullable', 'string', 'min:6', 'max:120'],
        ]);

        DB::transaction(function () use ($application, $data) {
            $application->fill([
                'nom_complet' => $data['nom_complet'],
                'contact' => $data['contact'],
                'contact_tuteur' => $data['contact_tuteur'],
                'ville' => $data['ville'],
                'niveau_scolaire' => $data['niveau_scolaire'],
                'matiere' => $data['matiere'],
                'type_cours' => $data['type_cours'],
            ]);

            if (! blank($data['login'] ?? null)) {
                $application->login = $data['login'];
            }
            if (! blank($data['access_password'] ?? null)) {
                $application->access_password = $data['access_password'];
            }
            $application->save();

            if ($application->student_id) {
                $student = Student::query()->find($application->student_id);
                if ($student) {
                    $payload = [
                        'nom_complet' => $application->nom_complet,
                        'contact' => $application->contact,
                        'contact_tuteur' => $application->contact_tuteur,
                        'ville' => $application->ville,
                        'niveau_scolaire' => $application->niveau_scolaire,
                        'matiere' => $application->matiere,
                        'type_cours' => $application->type_cours,
                    ];
                    if ($application->login) {
                        $payload['login'] = $application->login;
                    }
                    if (! blank($data['access_password'] ?? null)) {
                        $payload['access_password'] = $data['access_password'];
                    }
                    $student->update($payload);
                }
            }
        });

        return redirect()
            ->route('admin.page.demandes-eleves')
            ->with('status', 'Demande '.$application->displayId().' modifiée.');
    }

    public function pendingApplication(Request $request, StudentApplication $application)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $application->update([
            'etat' => StudentApplication::ETAT_EN_ATTENTE,
        ]);

        if ($application->student_id) {
            Student::whereKey($application->student_id)->update([
                'etat' => Student::ETAT_EN_ATTENTE,
            ]);
        }

        return back()->with('status', 'Demande remise en attente.');
    }

    public function suspendApplication(Request $request, StudentApplication $application)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $application->update([
            'etat' => StudentApplication::ETAT_SUSPENDUE,
        ]);

        if ($application->student_id) {
            Student::whereKey($application->student_id)->update([
                'etat' => Student::ETAT_SUSPENDU,
            ]);
        }

        return back()->with('status', 'Demande suspendue.');
    }

    private function uniqueLogin(string $name, ?int $exceptId = null): string
    {
        $base = EcopiloteIdentity::loginFromName($name);
        $query = Student::query()->where('login', $base);
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        if (! $query->exists()) {
            return $base;
        }

        $local = EcopiloteIdentity::localPart($base);
        $suffix = $exceptId ?: (Student::query()->max('id') + 1);

        return EcopiloteIdentity::email($local.'.'.$suffix);
    }

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
}

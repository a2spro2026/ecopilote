<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\TeacherApplication;
use App\Support\EcopiloteIdentity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function applications(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $candidatures = TeacherApplication::query()
            ->latest()
            ->get();

        return view('admin.teachers.applications', [
            'candidatures' => $candidatures,
        ]);
    }

    public function index(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $professeurs = Teacher::query()
            ->orderBy('id')
            ->get();

        return view('admin.teachers.index', [
            'professeurs' => $professeurs,
        ]);
    }

    public function technical(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return view('admin.teachers.technical', [
            'professeurs' => Teacher::query()->orderBy('id')->get(),
            'matieres' => $this->subjects(),
        ]);
    }

    public function storeTechnical(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $request->merge([
            'login' => EcopiloteIdentity::email((string) $request->input('login', '')),
        ]);

        $data = $request->validate([
            'teacher_id' => ['nullable', 'integer', 'exists:teachers,id'],
            'nom_complet' => ['required', 'string', 'max:120'],
            'contact' => ['required', 'string', 'max:120'],
            'ville' => ['required', 'string', 'max:120'],
            'statut' => ['required', Rule::in(['public', 'prive'])],
            'matieres' => ['required', 'array', 'min:1'],
            'matieres.*' => ['required', 'string', 'distinct', Rule::in($this->subjects())],
            'paiement' => ['required', Rule::in(['salaire', 'commission', 'pourcentage'])],
            'paiement_valeur' => ['required', 'numeric', 'min:0'],
            'periode_paiement' => ['required', Rule::in(['mois', 'trimestre', 'semestre', 'annuel'])],
            'login' => ['required', 'email', 'max:120', Rule::unique('teachers', 'login')->ignore($request->integer('teacher_id') ?: null)],
            'access_password' => ['required', 'string', 'min:6', 'max:120'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ], [
            'matieres.required' => 'Sélectionnez au moins une matière.',
        ]);

        $payload = [
            'nom_complet' => $data['nom_complet'],
            'contact' => $data['contact'],
            'ville' => $data['ville'],
            'statut' => $data['statut'],
            'matiere' => implode(', ', $data['matieres']),
            'paiement' => $data['paiement'],
            'paiement_valeur' => number_format((float) $data['paiement_valeur'], 2, '.', ''),
            'periode_paiement' => $data['periode_paiement'],
            'login' => $data['login'],
            'access_password' => $data['access_password'],
        ];

        if ($request->hasFile('photo')) {
            $payload['photo_path'] = $request->file('photo')->store('profiles/teachers', 'public');
        }

        if (! empty($data['teacher_id'])) {
            $teacher = Teacher::findOrFail($data['teacher_id']);
            $teacher->update($payload);
            $message = 'Fiche de '.$teacher->displayId().' mise à jour.';
        } else {
            $teacher = Teacher::create([
                ...$payload,
                'niveau' => 'college',
                'disponibilite' => 'immediat',
                'etat' => Teacher::ETAT_ACTIF,
            ]);
            $message = 'Fiche de '.$teacher->displayId().' ajoutée.';
        }

        return redirect()
            ->route('admin.teachers.technical')
            ->with('status', $message);
    }

    public function print(Request $request, Teacher $professeur)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return view('admin.teachers.print', ['professeur' => $professeur]);
    }

    public function show(Request $request, Teacher $professeur)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return view('admin.teachers.show', [
            'professeur' => $professeur,
        ]);
    }

    public function edit(Request $request, Teacher $professeur)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return view('admin.teachers.edit', [
            'professeur' => $professeur,
        ]);
    }

    public function update(Request $request, Teacher $professeur)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $data = $request->validate([
            'nom_complet' => ['required', 'string', 'max:120'],
            'access_password' => ['required', 'string', 'min:6', 'max:120'],
            'contact' => ['required', 'string', 'max:120'],
            'ville' => ['required', 'string', 'max:120'],
            'statut' => ['required', 'in:public,prive'],
            'matiere' => ['required', 'string', 'max:120'],
            'disponibilite' => ['required', 'in:immediat,a_negocier'],
            'etat' => ['required', Rule::in([Teacher::ETAT_ACTIF, Teacher::ETAT_EN_ATTENTE, Teacher::ETAT_SUSPENDU])],
            'paiement' => ['nullable', 'in:salaire,commission,pourcentage'],
            'paiement_valeur' => [
                Rule::requiredIf(fn () => filled($request->input('paiement'))),
                'nullable',
                'numeric',
                'min:0',
            ],
            'type_paiement' => ['nullable', 'in:vir,chq,vers,esp'],
        ], [
            'paiement_valeur.required' => 'Indiquez le montant du paiement sélectionné.',
        ]);

        if (empty($data['paiement'])) {
            $data['paiement'] = null;
            $data['paiement_valeur'] = null;
        } elseif (isset($data['paiement_valeur'])) {
            $data['paiement_valeur'] = number_format((float) $data['paiement_valeur'], 2, '.', '');
        }

        $data['login'] = EcopiloteIdentity::loginFromName($data['nom_complet']);
        $professeur->update($data);

        return redirect()
            ->route('admin.page.professeurs')
            ->with('status', 'Professeur '.$professeur->displayId().' mis à jour.');
    }

    public function suspend(Request $request, Teacher $professeur)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $professeur->update([
            'etat' => Teacher::ETAT_SUSPENDU,
        ]);

        return back()->with('status', 'Professeur '.$professeur->displayId().' suspendu.');
    }

    public function validateApplication(Request $request, TeacherApplication $application)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        DB::transaction(function () use ($application) {
            if ($application->teacher_id) {
                $teacher = Teacher::findOrFail($application->teacher_id);
                $teacher->update([
                    'etat' => Teacher::ETAT_ACTIF,
                    'nom_complet' => $application->nom_complet,
                    'login' => EcopiloteIdentity::loginFromName($application->nom_complet),
                    'access_password' => $teacher->access_password ?: (string) random_int(10000000, 99999999),
                    'contact' => $application->contact,
                    'ville' => $application->ville,
                    'statut' => $application->statut,
                    'matiere' => $application->matiere,
                    'niveau' => $application->niveau,
                    'disponibilite' => $application->disponibilite,
                ]);
            } else {
                $teacher = Teacher::create([
                    'nom_complet' => $application->nom_complet,
                    'login' => EcopiloteIdentity::loginFromName($application->nom_complet),
                    'access_password' => (string) random_int(10000000, 99999999),
                    'contact' => $application->contact,
                    'ville' => $application->ville,
                    'statut' => $application->statut,
                    'matiere' => $application->matiere,
                    'niveau' => $application->niveau,
                    'disponibilite' => $application->disponibilite,
                    'etat' => Teacher::ETAT_ACTIF,
                ]);
                $application->teacher_id = $teacher->id;
            }

            $application->etat = TeacherApplication::ETAT_VALIDEE;
            $application->save();
        });

        return back()->with('status', 'Candidature validée — le professeur a été ajouté à la liste.');
    }

    public function pendingApplication(Request $request, TeacherApplication $application)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $application->update([
            'etat' => TeacherApplication::ETAT_EN_ATTENTE,
        ]);

        if ($application->teacher_id) {
            Teacher::whereKey($application->teacher_id)->update([
                'etat' => Teacher::ETAT_EN_ATTENTE,
            ]);
        }

        return back()->with('status', 'Candidature remise en attente.');
    }

    public function suspendApplication(Request $request, TeacherApplication $application)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $application->update([
            'etat' => TeacherApplication::ETAT_SUSPENDUE,
        ]);

        if ($application->teacher_id) {
            Teacher::whereKey($application->teacher_id)->update([
                'etat' => Teacher::ETAT_SUSPENDU,
            ]);
        }

        return back()->with('status', 'Candidature suspendue.');
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
}

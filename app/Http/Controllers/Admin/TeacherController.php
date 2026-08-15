<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\TeacherApplication;
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

        $data['login'] = $data['nom_complet'];
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
                    'login' => $application->nom_complet,
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
                    'login' => $application->nom_complet,
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
}

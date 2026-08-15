<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function applications(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $demandes = StudentApplication::query()
            ->latest()
            ->get();

        return view('admin.students.applications', [
            'demandes' => $demandes,
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
            'contact' => ['required', 'string', 'max:120'],
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

        DB::transaction(function () use ($application) {
            if ($application->student_id) {
                Student::whereKey($application->student_id)->update([
                    'etat' => Student::ETAT_ACTIF,
                    'nom_complet' => $application->nom_complet,
                    'contact' => $application->contact,
                    'contact_tuteur' => $application->contact_tuteur,
                    'ville' => $application->ville,
                    'niveau_scolaire' => $application->niveau_scolaire,
                    'matiere' => $application->matiere,
                    'type_cours' => $application->type_cours,
                ]);
            } else {
                $student = Student::create([
                    'nom_complet' => $application->nom_complet,
                    'contact' => $application->contact,
                    'contact_tuteur' => $application->contact_tuteur,
                    'ville' => $application->ville,
                    'niveau_scolaire' => $application->niveau_scolaire,
                    'matiere' => $application->matiere,
                    'type_cours' => $application->type_cours,
                    'etat' => Student::ETAT_ACTIF,
                ]);
                $application->student_id = $student->id;
            }

            $application->etat = StudentApplication::ETAT_VALIDEE;
            $application->save();
        });

        return back()->with('status', 'Demande validée — l’élève a été ajouté à la liste.');
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
}

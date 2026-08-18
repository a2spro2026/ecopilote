<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentApplication;
use App\Support\EcopiloteIdentity;
use App\Support\WorkspaceSession;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($request->session()->get('student_id')) {
            $student = Student::query()->find($request->session()->get('student_id'));
            if ($student && $student->etat === Student::ETAT_ACTIF) {
                return redirect()->route('student.dashboard');
            }
        }

        return view('pages.portail-etudiant');
    }

    public function login(Request $request)
    {
        $request->merge([
            'email' => EcopiloteIdentity::email((string) $request->input('email', '')),
        ]);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $student = Student::query()
            ->where('login', $credentials['email'])
            ->where('etat', Student::ETAT_ACTIF)
            ->first();

        if (! $student || ! hash_equals((string) $student->access_password, (string) $credentials['password'])) {
            return back()
                ->withErrors(['email' => 'Identifiant ou mot de passe incorrect.']);
        }

        WorkspaceSession::enterStudent($request);
        $request->session()->regenerate();
        $request->session()->put('student_id', $student->id);

        return redirect()->route('student.dashboard');
    }

    public function register(Request $request)
    {
        $subjects = [
            'Mathématiques',
            'Physique-Chimie',
            'Français',
            'Anglais',
            'SVT',
            'Histoire-Géographie',
            'Informatique',
            'Arabe',
        ];

        $data = $request->validate([
            'nom_complet' => ['required', 'string', 'max:120'],
            'contact' => ['required', 'string', 'max:120'],
            'contact_tuteur' => ['required', 'string', 'max:120'],
            'ville' => ['required', 'string', 'max:120'],
            'niveau_scolaire' => ['required', 'string', 'max:120'],
            'matieres' => ['required', 'array', 'min:1'],
            'matieres.*' => ['required', 'string', 'distinct', Rule::in($subjects)],
            'type_cours' => ['required', 'in:individuel,en_groupe'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ], [
            'matieres.required' => 'Sélectionnez au moins une matière.',
            'matieres.min' => 'Sélectionnez au moins une matière.',
        ]);

        unset($data['photo']);
        $data['matiere'] = implode(', ', $data['matieres']);
        unset($data['matieres']);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('profiles/applications', 'public');
        }

        StudentApplication::create([
            ...$data,
            'photo_path' => $photoPath,
            'etat' => StudentApplication::ETAT_EN_ATTENTE,
        ]);

        return back()->with('status', 'Inscription envoyée. Notre équipe vous recontactera bientôt.');
    }

    public function logout(Request $request)
    {
        WorkspaceSession::forgetAdmin($request);
        $request->session()->forget('student_id');
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\TeacherApplication;
use App\Support\EcopiloteIdentity;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($request->session()->get('teacher_id')) {
            $teacher = Teacher::query()->find($request->session()->get('teacher_id'));
            if ($teacher && $teacher->etat === Teacher::ETAT_ACTIF) {
                return redirect()->route('teacher.bureau');
            }
        }

        return view('pages.portail-profs');
    }

    public function login(Request $request)
    {
        $request->merge([
            'login' => EcopiloteIdentity::email((string) $request->input('login', '')),
        ]);

        $credentials = $request->validate([
            'login' => ['required', 'string', 'max:120'],
            'password' => ['required'],
        ]);

        $login = $credentials['login'];
        $local = EcopiloteIdentity::localPart($login);

        $teacher = Teacher::query()
            ->where('etat', Teacher::ETAT_ACTIF)
            ->where(function ($query) use ($login, $local) {
                $query->where('login', $login)
                    ->orWhere('login', $local);
            })
            ->first();

        if (! $teacher || ! hash_equals((string) $teacher->access_password, (string) $credentials['password'])) {
            return back()
                ->withInput($request->only('login'))
                ->withErrors(['login' => 'Identifiant ou mot de passe incorrect.']);
        }

        $request->session()->regenerate();
        $request->session()->put('teacher_id', $teacher->id);

        return redirect()->route('teacher.bureau');
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
            'ville' => ['required', 'string', 'max:120'],
            'matieres' => ['required', 'array', 'min:1', 'max:8'],
            'matieres.*' => ['required', 'string', 'distinct', 'in:'.implode(',', $subjects)],
            'niveau' => ['required', 'in:primaire,college,lycee,universitaire'],
            'statut' => ['required', 'in:public,prive'],
            'disponibilite' => ['required', 'in:immediat,a_negocier'],
        ], [
            'matieres.required' => 'Sélectionnez au moins une matière.',
            'matieres.min' => 'Sélectionnez au moins une matière.',
            'matieres.*.in' => 'Une des matières sélectionnées n’est pas disponible.',
        ]);

        $data['matiere'] = implode(', ', $data['matieres']);
        unset($data['matieres']);

        TeacherApplication::create([
            ...$data,
            'etat' => TeacherApplication::ETAT_EN_ATTENTE,
        ]);

        return back()->with('status', 'Candidature envoyée. Notre équipe vous recontactera bientôt.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('teacher_id');
        $request->session()->regenerateToken();

        return redirect()->route('portail.profs');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudyGroup;
use App\Models\StudySession;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $niveaux = $this->levels();

        $sessions = StudySession::query()
            ->with(['group.teacher', 'group.students'])
            ->orderByDesc('date')
            ->orderBy('heure_debut')
            ->get();

        return view('admin.sessions.index', [
            'sessions' => $sessions,
            'nextCode' => 'SE-'.str_pad((string) ((int) StudySession::query()->max('id') + 1), 4, '0', STR_PAD_LEFT),
            'defaultDate' => now()->format('Y-m-d'),
            'niveaux' => $niveaux,
            'groups' => $this->groupsPayload($niveaux),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $data = $request->validate([
            'study_group_id' => ['required', 'integer', 'exists:study_groups,id'],
            'date' => ['required', 'date'],
            'heure_debut' => ['required', 'date_format:H:i'],
            'heure_fin' => ['required', 'date_format:H:i'],
            'numero_salle' => ['required', 'string', 'max:32'],
        ]);

        if ($data['heure_fin'] <= $data['heure_debut']) {
            throw ValidationException::withMessages([
                'heure_fin' => 'L’heure de fin doit être après l’heure de début.',
            ]);
        }

        $session = StudySession::create([
            'study_group_id' => $data['study_group_id'],
            'date' => $data['date'],
            'heure_debut' => $data['heure_debut'],
            'heure_fin' => $data['heure_fin'],
            'numero_salle' => $data['numero_salle'],
            'statut' => StudySession::STATUT_ACTIF,
        ]);

        return redirect()
            ->route('admin.page.seances')
            ->with('status', 'Séance '.$session->displayId().' ajoutée.');
    }

    public function update(Request $request, StudySession $session)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $data = $request->validate([
            'statut' => ['required', Rule::in([
                StudySession::STATUT_ACTIF,
                StudySession::STATUT_REPORTEE,
                StudySession::STATUT_ANNULEE,
            ])],
            'remarque' => ['nullable', 'string', 'max:500'],
        ]);

        if ($data['statut'] === StudySession::STATUT_ACTIF) {
            $data['remarque'] = null;
        } elseif (blank($data['remarque'] ?? null)) {
            return back()
                ->withErrors(['remarque' => 'Indiquez une remarque pour une séance reportée ou annulée.'])
                ->withInput();
        }

        $session->update($data);

        return redirect()
            ->route('admin.page.seances')
            ->with('status', 'Séance '.$session->displayId().' mise à jour.');
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
     * @return list<array<string, mixed>>
     */
    private function groupsPayload(array $niveaux): array
    {
        return StudyGroup::query()
            ->with(['teacher', 'students'])
            ->orderBy('id')
            ->get()
            ->map(fn (StudyGroup $group) => [
                'id' => $group->id,
                'code' => $group->displayId(),
                'matiere' => $group->matiere,
                'matiereLabel' => \App\Support\SubjectAbbreviation::display($group->matiere),
                'niveau' => $group->niveau,
                'niveauLabel' => $niveaux[$group->niveau] ?? $group->niveau,
                'teacher' => $group->teacher?->nom_complet,
                'effectif' => $group->effectif(),
            ])
            ->all();
    }
}

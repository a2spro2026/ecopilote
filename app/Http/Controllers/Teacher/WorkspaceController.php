<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Support\TeacherDemoData;
use App\Support\TeacherWorkspaceData;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    private function teacher(Request $request): Teacher
    {
        /** @var Teacher $teacher */
        $teacher = $request->attributes->get('teacher');

        return $teacher;
    }

    private function demo(Request $request): TeacherDemoData
    {
        return new TeacherDemoData($this->teacher($request));
    }

    private function workspace(Request $request): TeacherWorkspaceData
    {
        return TeacherWorkspaceData::for($this->teacher($request));
    }

    public function bureau(Request $request)
    {
        $workspace = $this->workspace($request);

        return view('teacher.bureau', [
            'kpis' => $workspace->kpis(),
            'next' => $workspace->nextSession(),
            'sessionsToday' => $workspace->sessionsToday(),
            'classes' => $workspace->classes(),
        ]);
    }

    public function seances(Request $request)
    {
        $filter = $request->string('filtre')->toString() ?: 'toutes';
        $workspace = $this->workspace($request);

        return view('teacher.seances', [
            'filtre' => $filter,
            'seances' => $workspace->sessions($filter),
        ]);
    }

    public function classes(Request $request)
    {
        return view('teacher.classes', [
            'classes' => $this->demo($request)->classes(),
        ]);
    }

    public function classeShow(Request $request, int $classe)
    {
        $demo = $this->demo($request);
        $item = $demo->classById($classe);
        abort_unless($item !== null, 404);

        return view('teacher.classe-show', [
            'classe' => $item,
            'eleves' => $demo->students($classe),
        ]);
    }

    public function eleves(Request $request)
    {
        return view('teacher.eleves', [
            'eleves' => $this->demo($request)->students(),
        ]);
    }

    public function eleveShow(Request $request, int $eleve)
    {
        $item = $this->demo($request)->studentById($eleve);
        abort_unless($item !== null, 404);

        return view('teacher.eleve-show', [
            'eleve' => $item,
        ]);
    }

    public function bibliotheque(Request $request)
    {
        $section = $request->string('section')->toString();
        $docs = $this->demo($request)->library();
        if ($section !== '') {
            $docs = array_values(array_filter($docs, fn ($d) => $d['section'] === $section));
        }

        return view('teacher.bibliotheque', [
            'documents' => $docs,
            'section' => $section,
        ]);
    }

    public function exercices(Request $request)
    {
        return view('teacher.exercices', [
            'exercices' => $this->demo($request)->exercises(),
        ]);
    }

    public function archives(Request $request)
    {
        return view('teacher.archives', [
            'archives' => $this->demo($request)->archives(),
        ]);
    }

    public function suivi(Request $request)
    {
        return view('teacher.suivi', [
            'pedagogy' => $this->demo($request)->pedagogy(),
            'eleves' => $this->demo($request)->students(),
        ]);
    }

    public function notifications(Request $request)
    {
        return view('teacher.notifications', [
            'notifications' => $this->demo($request)->notifications(),
        ]);
    }

    public function profil(Request $request)
    {
        return view('teacher.profil');
    }

    public function salle(Request $request)
    {
        $workspace = $this->workspace($request);
        $next = $workspace->nextSession();

        if (! $next['joinable'] || empty($next['id'])) {
            return redirect()
                ->route('teacher.bureau')
                ->with('status', 'Aucune salle active ne vous est affectée pour le moment.');
        }

        return redirect()->route('teacher.salle.show', $next['id']);
    }

    public function salleShow(Request $request, int $session)
    {
        $workspace = $this->workspace($request);
        $studySession = $workspace->sessionForTeacher($session);
        abort_unless($studySession !== null, 404);
        abort_unless($workspace->canJoin($studySession), 403);

        return view('teacher.salle', [
            'session' => $workspace->toRoomSession($studySession),
            'eleves' => $workspace->roomStudents($studySession),
            'documents' => $this->demo($request)->courseDocuments(),
        ]);
    }

    public function terminer(Request $request)
    {
        return view('teacher.seance-terminee', [
            'resume' => $this->demo($request)->lastSessionSummary(),
        ]);
    }
}

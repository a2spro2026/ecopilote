<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Support\TeacherDemoData;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    private function demo(Request $request): TeacherDemoData
    {
        /** @var Teacher $teacher */
        $teacher = $request->attributes->get('teacher');

        return new TeacherDemoData($teacher);
    }

    public function bureau(Request $request)
    {
        $demo = $this->demo($request);

        return view('teacher.bureau', [
            'kpis' => $demo->kpis(),
            'next' => $demo->nextSession(),
            'sessionsToday' => $demo->sessionsToday(),
            'classes' => $demo->classes(),
        ]);
    }

    public function seances(Request $request)
    {
        $filter = $request->string('filtre')->toString() ?: 'toutes';
        $demo = $this->demo($request);

        return view('teacher.seances', [
            'filtre' => $filter,
            'seances' => $demo->sessions($filter),
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
        $demo = $this->demo($request);
        $next = $demo->nextSession();

        return view('teacher.salle', [
            'session' => $next,
            'eleves' => $demo->roomStudents(),
            'documents' => $demo->courseDocuments(),
        ]);
    }

    public function terminer(Request $request)
    {
        return view('teacher.seance-terminee', [
            'resume' => $this->demo($request)->lastSessionSummary(),
        ]);
    }
}

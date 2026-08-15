<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Support\StudentWorkspaceData;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function dashboard(Request $request)
    {
        return $this->view($request, 'student.dashboard');
    }

    public function classes(Request $request)
    {
        return $this->view($request, 'student.classes');
    }

    public function sessions(Request $request)
    {
        return $this->view($request, 'student.sessions');
    }

    public function assignments(Request $request)
    {
        return $this->view($request, 'student.assignments');
    }

    public function submitAssignment(Request $request, int $assignment)
    {
        $request->validate([
            'submission' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);

        return back()->with('status', 'Devoir transmis à votre professeur avec succès.');
    }

    public function documents(Request $request)
    {
        return $this->view($request, 'student.documents');
    }

    public function progress(Request $request)
    {
        return $this->view($request, 'student.progress');
    }

    public function archives(Request $request)
    {
        return $this->view($request, 'student.archives');
    }

    public function notifications(Request $request)
    {
        return $this->view($request, 'student.notifications');
    }

    public function profile(Request $request)
    {
        return $this->view($request, 'student.profile');
    }

    public function room(Request $request)
    {
        return $this->view($request, 'student.room');
    }

    private function view(Request $request, string $view)
    {
        /** @var Student $student */
        $student = $request->attributes->get('student');

        return view($view, StudentWorkspaceData::for($student));
    }
}

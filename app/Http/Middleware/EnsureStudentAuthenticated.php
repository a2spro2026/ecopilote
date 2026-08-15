<?php

namespace App\Http\Middleware;

use App\Models\Student;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $studentId = $request->session()->get('student_id');
        $student = $studentId ? Student::query()->find($studentId) : null;

        if (! $student || $student->etat !== Student::ETAT_ACTIF) {
            $request->session()->forget('student_id');

            return redirect()
                ->route('portail.etudiant')
                ->withErrors(['email' => 'Connectez-vous avec un compte élève validé.']);
        }

        $request->attributes->set('student', $student);
        view()->share('currentStudent', $student);

        return $next($request);
    }
}

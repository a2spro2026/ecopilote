<?php

namespace App\Http\Middleware;

use App\Models\Teacher;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeacherAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $teacherId = $request->session()->get('teacher_id');
        $teacher = $teacherId ? Teacher::query()->find($teacherId) : null;

        if (! $teacher || $teacher->etat !== Teacher::ETAT_ACTIF) {
            $request->session()->forget('teacher_id');

            return redirect()
                ->route('portail.profs')
                ->withErrors(['login' => 'Connectez-vous avec un compte professeur validé.']);
        }

        $request->attributes->set('teacher', $teacher);
        view()->share('currentTeacher', $teacher);

        return $next($request);
    }
}

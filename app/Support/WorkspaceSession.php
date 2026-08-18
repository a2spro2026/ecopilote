<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkspaceSession
{
    public static function enterStudent(Request $request): void
    {
        self::forgetAdmin($request);
        $request->session()->forget('teacher_id');
    }

    public static function enterTeacher(Request $request): void
    {
        self::forgetAdmin($request);
        $request->session()->forget('student_id');
    }

    public static function enterAdmin(Request $request): void
    {
        $request->session()->forget(['student_id', 'teacher_id', 'url.intended']);
    }

    public static function forgetAdmin(Request $request): void
    {
        if (Auth::check()) {
            Auth::logout();
        }

        $request->session()->forget('url.intended');
    }
}

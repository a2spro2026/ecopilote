<?php

namespace App\Support;

use App\Models\Student;

class StudentWorkspaceData
{
    public static function for(Student $student): array
    {
        return [
            'student' => $student,
            'classes' => [],
            'sessions' => [],
            'assignments' => [],
            'documents' => [],
            'progress' => [],
            'archives' => [],
            'notifications' => [],
        ];
    }
}

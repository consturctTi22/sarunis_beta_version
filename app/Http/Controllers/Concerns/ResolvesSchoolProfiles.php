<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

trait ResolvesSchoolProfiles
{
    protected function teacherFromRequest(Request $request): Teacher
    {
        $teacher = $request->user()?->teacherProfile;

        abort_if($teacher === null, 403, 'Akun ini belum terhubung ke data guru.');

        return $teacher;
    }

    protected function studentFromRequest(Request $request): Student
    {
        $student = $request->user()?->studentProfile;

        abort_if($student === null, 403, 'Akun ini belum terhubung ke data siswa.');

        return $student;
    }
}

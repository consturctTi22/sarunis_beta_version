<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Controllers\Concerns\ResolvesSchoolProfiles;
use App\Models\Student;
use App\Services\ClassAttendanceService;
use App\Services\TeachingAssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentPortalController extends Controller
{
    use ResolvesSchoolProfiles;

    public function __construct(
        protected TeachingAssignmentService $teachingAssignmentService,
        protected ClassAttendanceService $classAttendanceService,
    ) {
    }

    public function schedule(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'student_id' => ['nullable', 'integer', Rule::exists('students', 'id')],
        ]);

        if ($request->user()->hasRole(UserRole::ADMIN)) {
            if (($filters['student_id'] ?? null) !== null) {
                $student = Student::query()->findOrFail($filters['student_id']);

                return response()->json([
                    'data' => $this->teachingAssignmentService->scheduleForStudent($student),
                ]);
            }

            return response()->json([
                'data' => $this->teachingAssignmentService->schedules(),
            ]);
        }

        $student = $this->studentFromRequest($request);

        return response()->json([
            'data' => $this->teachingAssignmentService->scheduleForStudent($student),
        ]);
    }

    public function classAttendance(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'student_id' => ['nullable', 'integer', Rule::exists('students', 'id')],
            'school_class_id' => ['nullable', 'integer', Rule::exists('school_classes', 'id')],
            'attendance_date' => ['nullable', 'date'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        if ($request->user()->hasRole(UserRole::ADMIN)) {
            return response()->json([
                'data' => $this->classAttendanceService->recap($filters),
            ]);
        }

        $student = $this->studentFromRequest($request);

        return response()->json([
            'data' => $this->classAttendanceService->recapForStudent($student, $filters),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\AcademicCalendarService;
use App\Services\AppSettingService;
use App\Services\SemesterLockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicCalendarPortalController extends Controller
{
    public function __construct(
        protected AcademicCalendarService $academicCalendarService,
        protected AppSettingService $appSettingService,
        protected SemesterLockService $semesterLockService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'academic_year' => ['nullable', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['nullable', Rule::in(['ganjil', 'genap'])],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $filters['academic_year'] ??= $this->appSettingService->value('academic_year', '2025/2026') ?: '2025/2026';
        $filters['semester'] ??= $this->appSettingService->value('active_semester', 'ganjil') ?: 'ganjil';
        $filters['is_active'] = true;

        return response()->json([
            'data' => $this->academicCalendarService->list($filters),
        ]);
    }

    public function attendanceStatus(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'date' => ['required', 'date'],
            'academic_year' => ['nullable', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['nullable', Rule::in(['ganjil', 'genap'])],
        ]);
        $academicYear = $payload['academic_year'] ?? $this->appSettingService->value('academic_year', '2025/2026') ?: '2025/2026';
        $semester = $payload['semester'] ?? $this->appSettingService->value('active_semester', 'ganjil') ?: 'ganjil';

        if ($this->semesterLockService->isLocked($academicYear, $semester)) {
            return response()->json([
                'data' => [
                    'allowed' => false,
                    'locked' => true,
                    'message' => 'Semester '.$semester.' '.$academicYear.' sudah ditutup. Absensi bersifat readonly.',
                    'events' => [],
                ],
            ]);
        }

        return response()->json([
            'data' => [
                ...$this->academicCalendarService->attendanceStatusForDate($academicYear, $semester, $payload['date']),
                'locked' => false,
            ],
        ]);
    }
}

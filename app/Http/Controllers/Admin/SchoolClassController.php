<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminListRequest;
use App\Http\Requests\Admin\UpsertSchoolClassRequest;
use App\Models\SchoolClass;
use App\Services\SchoolClassService;
use Illuminate\Http\JsonResponse;

class SchoolClassController extends Controller
{
    public function __construct(
        protected SchoolClassService $schoolClassService,
    ) {
    }

    public function index(AdminListRequest $request): JsonResponse
    {
        return response()->json(
            $this->schoolClassService->paginate($request->integer('per_page', 15))
        );
    }

    public function store(UpsertSchoolClassRequest $request): JsonResponse
    {
        $schoolClass = $this->schoolClassService->create($request->validated());

        return response()->json([
            'message' => 'Data kelas berhasil dibuat.',
            'data' => $schoolClass,
        ], 201);
    }

    public function show(SchoolClass $schoolClass): JsonResponse
    {
        return response()->json([
            'data' => $schoolClass->load(['homeroomTeacher', 'students', 'teachingAssignments.subject', 'teachingAssignments.teacher']),
        ]);
    }

    public function update(UpsertSchoolClassRequest $request, SchoolClass $schoolClass): JsonResponse
    {
        $schoolClass = $this->schoolClassService->update($schoolClass, $request->validated());

        return response()->json([
            'message' => 'Data kelas berhasil diperbarui.',
            'data' => $schoolClass,
        ]);
    }

    public function destroy(SchoolClass $schoolClass): JsonResponse
    {
        $this->schoolClassService->delete($schoolClass);

        return response()->json([
            'message' => 'Data kelas berhasil dihapus.',
        ]);
    }
}

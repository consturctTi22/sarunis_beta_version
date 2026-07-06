<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentViolation;
use Illuminate\Http\Request;

class StudentViolationController extends Controller
{
    public function index(Request $request)
    {
        // Query builder
        $query = StudentViolation::with(['student.schoolClass', 'reporter'])
            ->latest('violation_date');

        // Optional filtering by class or student name could go here

        $violations = $query->paginate(20);
        $classes = \App\Models\SchoolClass::with(['students' => fn($q) => $q->orderBy('name')])->orderBy('name')->get();

        // Depending on active portal, return different view if necessary.
        // Actually, let's use a shared view for Kesiswaan/Guru Piket
        $activePortal = $request->attributes->get('active_portal');
        if (!$activePortal) {
            if ($request->is('admin/*') || $request->is('admin')) {
                $activePortal = 'admin';
            } elseif ($request->is('wakasek-kesiswaan/*') || $request->is('wakasek-kesiswaan')) {
                $activePortal = 'wakasek-kesiswaan';
            } elseif ($request->is('guru-piket/*') || $request->is('guru-piket')) {
                $activePortal = 'guru-piket';
            } else {
                $activePortal = 'admin';
            }
        }

        return view('dashboard.student-violations', [
            'pageTitle' => 'Data Pelanggaran',
            'directoryTitle' => 'Data Pelanggaran',
            'directorySubtitle' => 'Kelola rekam jejak pelanggaran siswa.',
            'violations' => $violations,
            'classes' => $classes,
            'activePortal' => $activePortal,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'violation_date' => 'required|date',
            'violation_type' => 'required|string|max:255',
            'description' => 'required|string',
            'points' => 'required|integer|min:0',
            'action_taken' => 'nullable|string',
        ]);

        $validated['reported_by_id'] = $request->user()->id;

        StudentViolation::create($validated);

        return back()->with('success', 'Data pelanggaran berhasil ditambahkan.');
    }

    public function update(Request $request, StudentViolation $pelanggaran)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'violation_date' => 'required|date',
            'violation_type' => 'required|string|max:255',
            'description' => 'required|string',
            'points' => 'required|integer|min:0',
            'action_taken' => 'nullable|string',
        ]);

        $pelanggaran->update($validated);

        return back()->with('success', 'Data pelanggaran berhasil diperbarui.');
    }

    public function destroy(StudentViolation $pelanggaran)
    {
        $pelanggaran->delete();

        return back()->with('success', 'Data pelanggaran berhasil dihapus.');
    }
}

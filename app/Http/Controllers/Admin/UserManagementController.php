<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Services\UserRoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function __construct(
        protected UserRoleService $userRoleService,
    ) {
    }

    public function page(Request $request): View
    {
        return view('dashboard.admin-users', [
            'pageTitle' => 'Manajemen Pengguna',
            'menuSections' => $this->adminPageMenu('manajemen-pengguna'),
            'users' => User::query()
                ->with(['teacherProfile', 'studentProfile'])
                ->orderBy('name')
                ->get(),
            'roleOptions' => UserRole::values(),
            'teacherOptions' => Teacher::query()->with('user')->orderBy('name')->get(),
            'studentOptions' => Student::query()->with('user')->orderBy('name')->get(),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 15);

        return response()->json(
            User::query()
                ->with(['teacherProfile', 'studentProfile'])
                ->latest()
                ->paginate($perPage)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', Password::min(8)->letters()->numbers()],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', Rule::in(UserRole::values())],
            'email_verified' => ['nullable', 'boolean'],
            'teacher_id' => ['nullable', 'integer', Rule::exists('teachers', 'id')],
            'student_id' => ['nullable', 'integer', Rule::exists('students', 'id')],
        ]);

        $user = User::query()->create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => $payload['password'],
            'roles' => array_values(array_unique($payload['roles'] ?? [])),
        ]);
        $user->forceFill([
            'email_verified_at' => ($payload['email_verified'] ?? true) ? now() : null,
        ])->save();
        $this->syncProfiles($user, $payload);

        return response()->json([
            'message' => 'Pengguna berhasil dibuat.',
            'data' => $user->load(['teacherProfile', 'studentProfile']),
        ], 201);
    }

    public function show(User $pengguna): JsonResponse
    {
        return response()->json([
            'data' => $pengguna->load(['teacherProfile', 'studentProfile']),
        ]);
    }

    public function update(Request $request, User $pengguna): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($pengguna->id)],
            'password' => ['nullable', 'string', Password::min(8)->letters()->numbers()],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', Rule::in(UserRole::values())],
            'email_verified' => ['nullable', 'boolean'],
            'teacher_id' => ['nullable', 'integer', Rule::exists('teachers', 'id')],
            'student_id' => ['nullable', 'integer', Rule::exists('students', 'id')],
        ]);

        if ($pengguna->hasRole(UserRole::ADMIN) && ! in_array(UserRole::ADMIN->value, $payload['roles'] ?? [], true) && $this->adminCount() <= 1) {
            abort(422, 'Role admin terakhir tidak dapat dicabut.');
        }

        $data = [
            'name' => $payload['name'],
            'email' => $payload['email'],
            'roles' => array_values(array_unique($payload['roles'] ?? [])),
            'email_verified_at' => ($payload['email_verified'] ?? $pengguna->email_verified_at !== null) ? ($pengguna->email_verified_at ?? now()) : null,
        ];

        if (($payload['password'] ?? null) !== null) {
            $data['password'] = $payload['password'];
        }

        $pengguna->forceFill($data)->save();
        $this->syncProfiles($pengguna, $payload);

        return response()->json([
            'message' => 'Pengguna berhasil diperbarui.',
            'data' => $pengguna->refresh()->load(['teacherProfile', 'studentProfile']),
        ]);
    }

    public function destroy(Request $request, User $pengguna): JsonResponse
    {
        abort_if($request->user()->is($pengguna), 422, 'Akun yang sedang digunakan tidak dapat dihapus.');
        abort_if($pengguna->hasRole(UserRole::ADMIN) && $this->adminCount() <= 1, 422, 'Admin terakhir tidak dapat dihapus.');

        $pengguna->delete();

        return response()->json([
            'message' => 'Pengguna berhasil dihapus.',
        ]);
    }

    /**
     * @return array<int, array{title:string,items:array<int, array{label:string,icon:string,href:string,active:bool}>}>
     */
    protected function adminPageMenu(string $activeItem): array
    {
        return [
            [
                'title' => 'Menu',
                'items' => [
                    ['label' => 'Beranda', 'icon' => 'home', 'href' => url('/admin/dashboard'), 'active' => $activeItem === 'beranda'],
                    ['label' => 'Data Siswa', 'icon' => 'students', 'href' => url('/admin/data-siswa'), 'active' => $activeItem === 'data-siswa'],
                    ['label' => 'Data Guru', 'icon' => 'teacher', 'href' => url('/admin/data-guru'), 'active' => $activeItem === 'data-guru'],
                    ['label' => 'Data Kelas', 'icon' => 'class', 'href' => url('/admin/data-kelas'), 'active' => $activeItem === 'data-kelas'],
                    ['label' => 'Mata Pelajaran', 'icon' => 'subject', 'href' => url('/admin/mata-pelajaran'), 'active' => $activeItem === 'mata-pelajaran'],
                    ['label' => 'Kalender Akademik', 'icon' => 'calendar', 'href' => url('/admin/kalender-akademik'), 'active' => $activeItem === 'kalender-akademik'],
                    ['label' => 'Jadwal Pelajaran', 'icon' => 'schedule', 'href' => url('/admin/schedule/generate'), 'active' => $activeItem === 'jadwal-pelajaran'],
                    ['label' => 'Rekap Kehadiran', 'icon' => 'recap', 'href' => url('/admin/rekap-kehadiran'), 'active' => $activeItem === 'rekap-kehadiran'],
                    ['label' => 'Laporan Statistik', 'icon' => 'chart', 'href' => url('/admin/laporan-statistik'), 'active' => $activeItem === 'laporan-tren'],
                ],
            ],
            [
                'title' => 'Lainnya',
                'items' => [
                    ['label' => 'Manajemen Pengguna', 'icon' => 'users', 'href' => url('/admin/manajemen-pengguna'), 'active' => $activeItem === 'manajemen-pengguna'],
                    ['label' => 'Pengaturan', 'icon' => 'settings', 'href' => url('/admin/pengaturan'), 'active' => $activeItem === 'pengaturan'],
                    ['label' => 'Catatan Siswa', 'icon' => 'note', 'href' => url('/admin/catatan-siswa'), 'active' => $activeItem === 'catatan-siswa'],
                    ['label' => 'Pengumuman', 'icon' => 'announcement', 'href' => url('/admin/pengumuman'), 'active' => $activeItem === 'pengumuman'],
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function syncProfiles(User $user, array $payload): void
    {
        $teacherId = $payload['teacher_id'] ?? null;
        $studentId = $payload['student_id'] ?? null;

        if ($teacherId !== null) {
            Teacher::query()
                ->where('user_id', $user->id)
                ->whereKeyNot($teacherId)
                ->update(['user_id' => null]);

            $teacher = Teacher::query()->findOrFail($teacherId);
            abort_if($teacher->user_id !== null && (int) $teacher->user_id !== (int) $user->id, 422, 'Profil guru sudah terhubung ke pengguna lain.');
            $teacher->forceFill(['user_id' => $user->id])->save();
            $teacher->load('user');
            $this->userRoleService->syncTeacherRoles($teacher);
        } else {
            Teacher::query()->where('user_id', $user->id)->update(['user_id' => null]);
        }

        if ($studentId !== null) {
            Student::query()
                ->where('user_id', $user->id)
                ->whereKeyNot($studentId)
                ->update(['user_id' => null]);

            $student = Student::query()->findOrFail($studentId);
            abort_if($student->user_id !== null && (int) $student->user_id !== (int) $user->id, 422, 'Profil siswa sudah terhubung ke pengguna lain.');
            $student->forceFill(['user_id' => $user->id])->save();
            $student->load('user');
            $this->userRoleService->syncStudentRole($student);
        } else {
            Student::query()->where('user_id', $user->id)->update(['user_id' => null]);
        }
    }

    protected function adminCount(): int
    {
        return User::query()
            ->whereJsonContains('roles', UserRole::ADMIN->value)
            ->count();
    }
}

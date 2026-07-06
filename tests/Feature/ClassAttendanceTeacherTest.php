<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassAttendanceTeacherTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable semester lock or calendar restrictions for our test date (a Monday)
        // Let's use 2026-06-01 (which is a Monday, day_of_week = 1)
        // Ensure academic calendar allows it or we mock/setup the settings
        \Illuminate\Support\Facades\DB::table('app_settings')->updateOrInsert(
            ['key' => 'academic_year'],
            ['label' => 'Academic Year', 'value' => '2025/2026', 'created_at' => now(), 'updated_at' => now()]
        );
        \Illuminate\Support\Facades\DB::table('app_settings')->updateOrInsert(
            ['key' => 'active_semester'],
            ['label' => 'Active Semester', 'value' => 'ganjil', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    public function test_first_period_teacher_can_record_daily_class_attendance(): void
    {
        // 1. Setup Class and Students
        $class = SchoolClass::query()->create([
            'name' => '10A',
            'level' => 10,
            'academic_year' => '2025/2026',
            'homeroom_teacher_id' => null, // No homeroom teacher assigned yet
        ]);

        $student = Student::query()->create([
            'school_class_id' => $class->id,
            'name' => 'Budi',
            'nik' => '123456',
        ]);

        // 2. Setup Teachers
        $userA = User::factory()->guruMapel()->create();
        $teacherA = Teacher::query()->create([
            'user_id' => $userA->id,
            'nip' => 'T-A',
            'name' => 'Teacher A',
            'is_subject_teacher' => true,
        ]);

        $userB = User::factory()->guruMapel()->create();
        $teacherB = Teacher::query()->create([
            'user_id' => $userB->id,
            'nip' => 'T-B',
            'name' => 'Teacher B',
            'is_subject_teacher' => true,
        ]);

        $subject = Subject::query()->create([
            'code' => 'M01',
            'name' => 'Matematika',
        ]);

        // 3. Setup Teaching Assignments
        // Teacher A teaches 1st period (earliest: 07:00) on Monday (day_of_week = 1)
        TeachingAssignment::query()->create([
            'teacher_id' => $teacherA->id,
            'subject_id' => $subject->id,
            'school_class_id' => $class->id,
            'academic_year' => '2025/2026',
            'day_of_week' => 1,
            'start_time' => '07:00:00',
            'end_time' => '08:30:00',
        ]);

        // Teacher B teaches 2nd period (08:30) on Monday
        TeachingAssignment::query()->create([
            'teacher_id' => $teacherB->id,
            'subject_id' => $subject->id,
            'school_class_id' => $class->id,
            'academic_year' => '2025/2026',
            'day_of_week' => 1,
            'start_time' => '08:30:00',
            'end_time' => '10:00:00',
        ]);

        $testDate = '2026-06-01'; // Monday

        // 4. Test Teacher A (1st period) - SHOULD BE ALLOWED
        $this->actingAs($userA);

        $response = $this->postJson('/guru-mapel/absensi-kelas', [
            'school_class_id' => $class->id,
            'attendance_date' => $testDate,
            'attendances' => [
                [
                    'student_id' => $student->id,
                    'status' => 'hadir',
                    'notes' => 'Tepat waktu',
                ]
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Absensi kelas berhasil disimpan.');

        $this->assertDatabaseHas('class_attendances', [
            'school_class_id' => $class->id,
            'student_id' => $student->id,
            'attendance_date' => $testDate,
            'recorded_by_teacher_id' => $teacherA->id,
            'status' => 'hadir',
        ]);

        // 5. Test Teacher B (2nd period) - SHOULD BE FORBIDDEN (403)
        $this->actingAs($userB);

        $response = $this->postJson('/guru-mapel/absensi-kelas', [
            'school_class_id' => $class->id,
            'attendance_date' => $testDate,
            'attendances' => [
                [
                    'student_id' => $student->id,
                    'status' => 'hadir',
                ]
            ],
        ]);

        $response->assertStatus(403);
    }

    public function test_homeroom_teacher_can_record_daily_class_attendance_regardless_of_period(): void
    {
        $userC = User::factory()->guruMapel()->create();
        $teacherC = Teacher::query()->create([
            'user_id' => $userC->id,
            'nip' => 'T-C',
            'name' => 'Teacher C',
            'is_subject_teacher' => true,
        ]);

        $class = SchoolClass::query()->create([
            'name' => '10B',
            'level' => 10,
            'academic_year' => '2025/2026',
            'homeroom_teacher_id' => $teacherC->id, // Teacher C is homeroom teacher
        ]);

        $student = Student::query()->create([
            'school_class_id' => $class->id,
            'name' => 'Caca',
            'nik' => '234567',
        ]);

        // User C (Homeroom) tries to record daily class attendance
        $this->actingAs($userC);

        $testDate = '2026-06-01'; // Monday

        // Wait! In routes/web.php:
        // /walikelas prefix uses 'homeroom-class' middleware. Since User C is homeroom teacher, they can access it.
        $response = $this->postJson('/walikelas/absensi-kelas', [
            'school_class_id' => $class->id,
            'attendance_date' => $testDate,
            'attendances' => [
                [
                    'student_id' => $student->id,
                    'status' => 'hadir',
                ]
            ],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('class_attendances', [
            'school_class_id' => $class->id,
            'student_id' => $student->id,
            'attendance_date' => $testDate,
            'recorded_by_teacher_id' => $teacherC->id,
            'status' => 'hadir',
        ]);
    }
}

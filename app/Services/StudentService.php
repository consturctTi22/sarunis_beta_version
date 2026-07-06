<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentService
{
    public function __construct(
        protected UserRoleService $userRoleService,
        protected ProfilePhotoService $profilePhotoService,
    ) {
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Student::query()
            ->with(['user', 'schoolClass', 'detailSiswa'])
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Student
    {
        /** @var UploadedFile|null $photo */
        $photo = $data['photo'] ?? null;
        $detailSiswaData = $this->extractDetailSiswaData($data);
        unset($data['photo'], $data['remove_photo']);

        $storedPhotoPath = null;

        try {
            return DB::transaction(function () use ($data, $photo, $detailSiswaData, &$storedPhotoPath): Student {
                if ($photo !== null) {
                    $storedPhotoPath = $this->profilePhotoService->store($photo, 'students');
                    $data['photo_path'] = $storedPhotoPath;
                }

                $student = Student::create($data);
                $this->syncDetailSiswa($student, $detailSiswaData);
                $student->load('user');

                $this->userRoleService->syncStudentRole($student);

                return $student->load(['user', 'schoolClass', 'detailSiswa']);
            });
        } catch (\Throwable $throwable) {
            $this->profilePhotoService->delete($storedPhotoPath);

            throw $throwable;
        }
    }

    public function update(Student $student, array $data): Student
    {
        /** @var UploadedFile|null $photo */
        $photo = $data['photo'] ?? null;
        $removePhoto = (bool) ($data['remove_photo'] ?? false);
        $detailSiswaData = $this->extractDetailSiswaData($data);
        unset($data['photo'], $data['remove_photo']);

        $oldPhotoPath = $student->photo_path;
        $newPhotoPath = $oldPhotoPath;
        $storedPhotoPath = null;

        try {
            if ($photo !== null) {
                $storedPhotoPath = $this->profilePhotoService->store($photo, 'students');
                $newPhotoPath = $storedPhotoPath;
            } elseif ($removePhoto) {
                $newPhotoPath = null;
            }

            $updatedStudent = DB::transaction(function () use ($student, $data, $detailSiswaData, $newPhotoPath): Student {
                $oldUserId = $student->user_id;

                $student->update([
                    ...$data,
                    'photo_path' => $newPhotoPath,
                ]);
                $this->syncDetailSiswa($student, $detailSiswaData);
                $student->load('user');

                if ($oldUserId !== null && $oldUserId !== $student->user_id) {
                    $this->userRoleService->detachStudentRole(User::find($oldUserId));
                }

                $this->userRoleService->syncStudentRole($student);
                $this->syncLinkedUserPasswordFromBirthDate($student);

                return $student->load(['user', 'schoolClass', 'detailSiswa']);
            });

            if ($storedPhotoPath !== null && $oldPhotoPath !== null && $oldPhotoPath !== $storedPhotoPath) {
                $this->profilePhotoService->delete($oldPhotoPath);
            }

            if ($removePhoto && $photo === null && $oldPhotoPath !== null) {
                $this->profilePhotoService->delete($oldPhotoPath);
            }

            return $updatedStudent;
        } catch (\Throwable $throwable) {
            if ($storedPhotoPath !== null) {
                $this->profilePhotoService->delete($storedPhotoPath);
            }

            throw $throwable;
        }
    }

    public function delete(Student $student): void
    {
        DB::transaction(function () use ($student): void {
            $user = $student->user;
            $photoPath = $student->photo_path;
            $student->delete();

            $this->userRoleService->detachStudentRole($user);
            $this->profilePhotoService->delete($photoPath);
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function extractDetailSiswaData(array &$data): ?array
    {
        if (! array_key_exists('detail_siswa', $data)) {
            return null;
        }

        $detailSiswa = is_array($data['detail_siswa']) ? $data['detail_siswa'] : [];
        unset($data['detail_siswa']);

        $fields = [
            'religion',
            'birth_place',
            'address_street',
            'address_village',
            'address_district',
            'address_province',
            'address_city',
            'father_name',
            'father_education',
            'father_occupation',
            'mother_name',
            'mother_education',
            'mother_occupation',
            'parent_address',
            'parent_province',
            'parent_city',
            'postal_code',
            'parent_phone',
            'previous_school',
        ];

        $normalized = [];

        foreach ($fields as $field) {
            $value = $detailSiswa[$field] ?? null;

            if (is_string($value)) {
                $value = trim($value);
            }

            $normalized[$field] = $value === '' ? null : $value;
        }

        return $normalized;
    }

    /**
     * @param array<string, mixed>|null $detailSiswaData
     */
    protected function syncDetailSiswa(Student $student, ?array $detailSiswaData): void
    {
        if ($detailSiswaData === null) {
            return;
        }

        $hasFilledValue = collect($detailSiswaData)->contains(static fn (mixed $value): bool => $value !== null);

        if (! $hasFilledValue) {
            $student->detailSiswa()->delete();

            return;
        }

        if ($student->detailSiswa()->exists()) {
            $student->detailSiswa()->update($detailSiswaData);

            return;
        }

        $student->detailSiswa()->create($detailSiswaData);
    }

    protected function syncLinkedUserPasswordFromBirthDate(Student $student): void
    {
        if ($student->user === null || $student->birth_date === null) {
            return;
        }

        $defaultPassword = $student->birth_date->format('dmY');

        if (Hash::check($defaultPassword, $student->user->password)) {
            return;
        }

        $student->user->forceFill([
            'password' => $defaultPassword,
        ])->save();
    }
}

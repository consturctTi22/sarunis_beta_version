<?php

namespace App\Http\Requests\Admin;

use App\Models\TeachingAssignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpsertTeachingAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'academic_year' => is_string($this->academic_year) ? trim($this->academic_year) : $this->academic_year,
            'room' => is_string($this->room) ? trim($this->room) : $this->room,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'teacher_id' => ['required', 'integer', 'exists:teachers,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'school_class_id' => ['required', 'integer', 'exists:school_classes,id'],
            'academic_year' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'day_of_week' => ['required', 'integer', 'between:1,7'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:50'],
            'substitute_teacher_id' => ['nullable', 'integer', 'exists:teachers,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            /** @var TeachingAssignment|null $teachingAssignment */
            $teachingAssignment = $this->route('teachingAssignment');

            $overlapQuery = TeachingAssignment::query()
                ->where('academic_year', $this->input('academic_year'))
                ->where('day_of_week', $this->integer('day_of_week'))
                ->where('start_time', '<', $this->input('end_time'))
                ->where('end_time', '>', $this->input('start_time'));

            if ($teachingAssignment !== null) {
                $overlapQuery->whereKeyNot($teachingAssignment->id);
            }

            $teacherConflict = (clone $overlapQuery)
                ->where('teacher_id', $this->integer('teacher_id'))
                ->exists();

            if ($teacherConflict) {
                $validator->errors()->add('teacher_id', 'Guru sudah memiliki jadwal lain pada hari dan jam tersebut.');
            }

            $classConflict = (clone $overlapQuery)
                ->where('school_class_id', $this->integer('school_class_id'))
                ->exists();

            if ($classConflict) {
                $validator->errors()->add('school_class_id', 'Kelas sudah memiliki jadwal lain pada hari dan jam tersebut.');
            }
        });
    }
}

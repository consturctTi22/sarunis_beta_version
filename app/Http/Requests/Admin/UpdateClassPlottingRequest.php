<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClassPlottingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'homeroom_teacher_id' => ['nullable', 'integer', Rule::exists('teachers', 'id')],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', 'distinct', Rule::exists('students', 'id')],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['integer', 'distinct', Rule::exists('subjects', 'id')],
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpsertAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => is_string($this->title) ? trim($this->title) : $this->title,
            'content' => is_string($this->content) ? trim($this->content) : $this->content,
            'target_roles' => $this->filled('target_roles') ? (array) $this->target_roles : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'content' => ['required', 'string', 'min:5'],
            'target_roles' => ['nullable', 'array'],
            'target_roles.*' => ['string', 'in:admin,guru_mapel,siswa,wakasek_kesiswaan,guru_piket,orang_tua'],
        ];
    }
}

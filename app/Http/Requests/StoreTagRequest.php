<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        // sesuaikan kalau pakai gate/policy
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:tags,name',
            'slug' => [
                'nullable', // agar bisa kosong dan dibuat otomatis
                'string',
                Rule::unique('posts', 'slug'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama tag wajib diisi.',
            'name.unique'   => 'Nama tag sudah digunakan.',
        ];
    }
}

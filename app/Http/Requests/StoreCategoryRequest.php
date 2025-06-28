<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // sesuaikan kalau pakai gate/policy
        return true;
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:categories,name',
            'slug' => [
                'nullable', // agar bisa kosong dan dibuat otomatis
                'string',
                Rule::unique('posts', 'slug'),
            ],
            'image'       => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'is_active'   => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama tag wajib diisi.',
            'name.unique'   => 'Nama tag sudah digunakan.',
            'slug.unique'   => 'Slug tag sudah digunakan, silakan gunakan yang lain.',
            'image.image'   => 'File harus berupa gambar.',
            'image.mimes'   => 'Format gambar harus jpeg, jpg, atau png.',
            'image.max'     => 'Ukuran gambar maksimal 2MB.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
            'is_active.boolean' => 'Status aktif harus berupa boolean.',
            'is_featured.boolean' => 'Status unggulan harus berupa boolean.',
        ];
    }
}

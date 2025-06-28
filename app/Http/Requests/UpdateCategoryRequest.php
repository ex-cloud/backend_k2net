<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $category = $this->route('category');

        $categoryId = $category instanceof Category ? $category->id : $category;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
            'slug' => [
                'nullable',
                'string',
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ],
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Nama kategori sudah digunakan.',
            'slug.unique'   => 'Slug kategori sudah digunakan, silakan gunakan yang lain.',
            'image.image'   => 'File harus berupa gambar.',
            'image.mimes'   => 'Format gambar harus jpeg, jpg, png, atau webp.',
            'image.max'     => 'Ukuran gambar maksimal 2MB.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
            'is_active.boolean' => 'Status aktif harus berupa boolean.',
            'is_featured.boolean' => 'Status unggulan harus berupa boolean.',
        ];
    }
}

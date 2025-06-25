<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $category = $this->route('category');
        Log::info('Category in UpdateCategoryRequest:', ['category' => $category]);

        $categoryId = $category instanceof Category ? $category->id : $category;

        return [
            'name' => [
                'required',
                'string',
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
            'slug' => [
                'required',
                'string',
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ],
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2000',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama category wajib diisi.',
            'name.unique'   => 'Nama category sudah digunakan.',
        ];
    }
}

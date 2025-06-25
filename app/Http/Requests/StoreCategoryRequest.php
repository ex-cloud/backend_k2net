<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // sesuaikan kalau pakai gate/policy
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:categories,name',
            'image'    => 'required|image|mimes:jpeg,jpg,png|max:2000',
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

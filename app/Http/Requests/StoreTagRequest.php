<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
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

<?php

namespace App\Http\Requests;

use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tag = $this->route('tag');
        Log::info('Tag in UpdateTagRequest:', ['tag' => $tag]);

        $tagId = $tag instanceof Tag ? $tag->id : $tag;

        return [
            'name' => [
                'nullable',
                'string',
                Rule::unique('tags', 'name')->ignore($tagId),
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

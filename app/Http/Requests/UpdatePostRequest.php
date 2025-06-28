<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $post = $this->route('post'); // Route Model Binding

        $postId = $post instanceof Post ? $post->id : $post;

        return [
            'title'         => 'required|string|max:255',
            'slug'          => [
                'nullable',
                'string',
                Rule::unique('posts', 'slug')->ignore($postId),
            ],
            'content'       => 'required|string',
            'image'         => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'category_id'   => 'required|exists:categories,id',
            'tags'          => 'nullable|array',
            'tags.*'        => 'exists:tags,id',
            'description'   => 'nullable|string|max:1000',
            'meta_title'    => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'status'        => 'nullable|string|in:draft,published,archived', // sesuaikan jika ada enum
            'published_at'  => 'nullable|date',
            'is_published'  => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul wajib diisi.',
            'slug.required' => 'Slug wajib diisi.',
            'slug.unique'   => 'Slug sudah digunakan, silakan gunakan yang lain.',
            'content.required' => 'Konten tidak boleh kosong.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, jpg, atau png.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'tags.array' => 'Tags harus berupa array.',
            'tags.*.exists' => 'Tag yang dipilih tidak valid.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
            'meta_title.max' => 'Meta title maksimal 255 karakter.',
            'meta_description.max' => 'Meta description maksimal 500 karakter.',
            'meta_keywords.max' => 'Meta keywords maksimal 255 karakter.',
            'status.in' => 'Status harus salah satu dari: draft, published, archived.',
            'published_at.date' => 'Tanggal publikasi harus berupa tanggal yang valid.',
            'is_published.boolean' => 'Is Published harus berupa nilai boolean (true/false) or (1/0).',
        ];
    }
}

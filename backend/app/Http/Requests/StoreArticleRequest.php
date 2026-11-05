<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'slug'         => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:articles,slug'],
            'category_id'  => ['nullable', 'integer', 'exists:categories,id'],
            'excerpt'      => ['required', 'string', 'max:500'],
            'content'      => ['required', 'string'],
            'status'       => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
            'thumbnail'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex'      => 'Slug hanya boleh huruf kecil, angka, dan tanda hubung.',
            'slug.unique'     => 'Slug sudah dipakai artikel lain.',
            'thumbnail.image' => 'File harus berupa gambar.',
            'thumbnail.mimes' => 'Gambar harus berformat JPG, PNG, atau WebP.',
            'thumbnail.max'   => 'Ukuran gambar maksimal 2 MB.',
        ];
    }
}

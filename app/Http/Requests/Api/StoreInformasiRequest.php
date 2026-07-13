<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreInformasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof \App\Models\Administrator;
    }

    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'kategori' => 'required|string|max:100',
            'cover_image' => 'nullable|string|max:500',
            'meta_description' => 'nullable|string|max:500',
            'kata_kunci' => 'nullable|string|max:500',
            'is_published' => 'boolean',
        ];
    }
}

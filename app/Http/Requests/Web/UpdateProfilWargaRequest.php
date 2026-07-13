<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilWargaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof \App\Models\Penduduk;
    }

    public function rules(): array
    {
        return [
            'foto_profil' => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:1024',
            'foto_ktp' => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:2048',
            'foto_kk' => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'foto_profil.max' => 'Foto profil maksimal 1MB',
            'foto_ktp.max' => 'Foto KTP maksimal 2MB',
            'foto_kk.max' => 'Foto KK maksimal 2MB',
        ];
    }
}

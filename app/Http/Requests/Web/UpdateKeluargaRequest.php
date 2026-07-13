<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKeluargaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof \App\Models\Penduduk;
    }

    public function rules(): array
    {
        return [
            'alamat' => 'nullable|string|max:500',
            'desa_kel' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
        ];
    }
}

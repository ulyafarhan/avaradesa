<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StorePengajuanSuratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof \App\Models\Penduduk;
    }

    public function rules(): array
    {
        return [
            'kategori_surat_id' => 'required|exists:kategori_surat,id',
            'data_isian' => 'required|array',
            'data_isian.*' => 'string|max:5000',
            'file_syarat' => 'required|array',
            'file_syarat.*' => 'string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_surat_id.exists' => 'Kategori surat tidak ditemukan',
            'data_isian.required' => 'Data isian surat wajib diisi',
            'file_syarat.required' => 'File syarat wajib dilampirkan',
        ];
    }
}

<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StorePengajuanSuratWebRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof \App\Models\Penduduk;
    }

    public function rules(): array
    {
        return [
            'kategori_surat_id' => 'required|exists:kategori_surat,id',
            'data_isian.*' => 'required|string|max:5000',
            'file_syarat.*' => 'required|file|mimes:jpg,jpeg,png,pdf,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'file_syarat.*.mimes' => 'File syarat harus berupa JPG, PNG, atau PDF',
            'file_syarat.*.max' => 'File syarat maksimal 2MB',
        ];
    }
}

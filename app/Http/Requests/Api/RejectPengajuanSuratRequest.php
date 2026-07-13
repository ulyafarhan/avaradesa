<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RejectPengajuanSuratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof \App\Models\Administrator;
    }

    public function rules(): array
    {
        return [
            'catatan_penolakan' => 'required|string|min:5|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'catatan_penolakan.required' => 'Catatan penolakan wajib diisi',
            'catatan_penolakan.min' => 'Catatan penolakan minimal 5 karakter',
        ];
    }
}

<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LoginWargaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nik' => 'required|string|size:16|regex:/^\d{16}$/',
            'no_kk' => 'required|string|size:16|regex:/^\d{16}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'nik.regex' => 'NIK harus berupa 16 digit angka',
            'no_kk.regex' => 'No KK harus berupa 16 digit angka',
        ];
    }
}

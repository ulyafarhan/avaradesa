<?php

namespace App\Http\Requests\AppApi\Auth;

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
            'nik' => 'required|string|size:16',
            'no_kk' => 'required|string|size:16',
        ];
    }
}

<?php

namespace App\Http\Requests\AppApi\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nik' => 'required|string|size:16',
            'pin' => 'required|string|digits:6',
        ];
    }
}

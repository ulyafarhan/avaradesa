<?php

namespace App\Http\Requests\AppApi\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterPinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pin' => 'required|string|digits:6|confirmed',
        ];
    }
}

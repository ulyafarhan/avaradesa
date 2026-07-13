<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class BindTelegramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof \App\Models\Penduduk;
    }

    public function rules(): array
    {
        return [
            'telegram_chat_id' => 'required|string|unique:penduduk,telegram_chat_id',
        ];
    }
}

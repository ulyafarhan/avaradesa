<?php

namespace App\Http\Resources\AppApi;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Penduduk
 */
class WargaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'nik' => $this->nik,
            'nama_lengkap' => $this->nama_lengkap,
            'jenis_kelamin' => $this->jenis_kelamin,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir?->toDateString(),
            'no_kk' => $this->no_kk,
            'has_pin' => $this->pin_hash !== null,
            'telegram_linked' => $this->telegram_chat_id !== null,
        ];
    }
}

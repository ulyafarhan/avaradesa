<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KategoriSuratResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_surat' => $this->nama_surat,
            'slug' => $this->slug,
            'kode_surat' => $this->kode_surat,
            'deskripsi' => $this->deskripsi,
            'template_view' => $this->template_view,
            'skema_data' => $this->skema_data,
            'syarat' => $this->syarat,
            'is_active' => $this->is_active,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengajuanSuratResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $isOwner = $user && $user instanceof \App\Models\Penduduk && $user->nik === $this->nik_pemohon;
        $isAdmin = $user && $user instanceof \App\Models\Administrator;

        return [
            'id' => $this->id,
            'nomor_registrasi' => $this->nomor_registrasi,
            'kategori' => new KategoriSuratResource($this->whenLoaded('kategori')),
            'status' => $this->status,
            'data_isian' => $isOwner || $isAdmin ? $this->data_isian : null,
            'file_syarat' => $isOwner || $isAdmin ? $this->file_syarat : null,
            'catatan_penolakan' => $this->when($isOwner || $isAdmin, $this->catatan_penolakan),
            'file_pdf_url' => $this->file_pdf_url,
            'qr_hash' => $this->when($isOwner || $isAdmin, $this->qr_hash),
            'pemohon' => new PendudukResource($this->whenLoaded('pemohon')),
            'tracking' => TrackingPengajuanSuratResource::collection($this->whenLoaded('tracking')),
            'created_at' => $this->created_at,
            'created_at_formatted' => $this->created_at_formatted,
            'updated_at' => $this->updated_at,
            'updated_at_formatted' => $this->updated_at_formatted,
        ];
    }
}

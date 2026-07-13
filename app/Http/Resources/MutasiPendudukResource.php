<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MutasiPendudukResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $isOwner = $user && $user instanceof \App\Models\Penduduk && $user->nik === $this->nik;
        $isAdmin = $user && $user instanceof \App\Models\Administrator;

        return [
            'id' => $this->id,
            'nik' => $this->nik,
            'jenis_mutasi' => $this->jenis_mutasi,
            'tanggal_mutasi' => $this->tanggal_mutasi,
            'tanggal_mutasi_formatted' => $this->tanggal_mutasi_formatted,
            'keterangan' => $this->keterangan,
            'dokumen_bukti' => $isOwner || $isAdmin ? $this->dokumen_bukti : null,
            'status_verifikasi' => $this->status_verifikasi,
            'penduduk' => new PendudukResource($this->whenLoaded('penduduk')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

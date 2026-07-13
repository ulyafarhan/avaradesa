<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PendudukResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $isOwner = $user && $user instanceof \App\Models\Penduduk && $user->nik === $this->nik;
        $isAdmin = $user && $user instanceof \App\Models\Administrator;

        return [
            'nik' => $this->nik,
            'no_kk' => $this->no_kk,
            'nama_lengkap' => $this->nama_lengkap,
            'nama_lengkap_formatted' => $this->nama_lengkap_formatted,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir,
            'tanggal_lahir_formatted' => $this->tanggal_lahir_formatted,
            'jenis_kelamin' => $this->jenis_kelamin,
            'jenis_kelamin_label' => $this->jenis_kelamin_label,
            'agama' => $this->agama,
            'pendidikan' => $this->pendidikan,
            'pekerjaan' => $this->pekerjaan,
            'status_perkawinan' => $this->status_perkawinan,
            'status_keluarga' => $this->status_keluarga,
            'status_mutasi' => $this->status_mutasi,
            'foto_profil' => $this->foto_profil,
            'foto_ktp' => $isOwner || $isAdmin ? $this->foto_ktp : null,
            'foto_kk' => $isOwner || $isAdmin ? $this->foto_kk : null,
        ];
    }
}

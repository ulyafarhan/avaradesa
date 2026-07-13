<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackingPengajuanSuratResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status_sebelumnya' => $this->status_sebelumnya,
            'status_baru' => $this->status_baru,
            'keterangan_update' => $this->keterangan_update,
            'updater' => $this->when($this->relationLoaded('updater') && $this->updater, function () {
                return [
                    'username' => $this->updater->username,
                ];
            }),
            'created_at' => $this->created_at,
            'created_at_formatted' => $this->created_at_formatted,
        ];
    }
}

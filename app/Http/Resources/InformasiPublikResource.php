<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InformasiPublikResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'judul' => $this->judul,
            'slug' => $this->slug,
            'konten' => $this->konten,
            'kategori' => $this->kategori,
            'cover_image' => $this->cover_image,
            'meta_description' => $this->meta_description,
            'kata_kunci' => $this->kata_kunci,
            'is_published' => $this->is_published,
            'author' => new AdministratorResource($this->whenLoaded('author')),
            'created_at' => $this->created_at,
            'created_at_formatted' => $this->created_at_formatted,
        ];
    }
}

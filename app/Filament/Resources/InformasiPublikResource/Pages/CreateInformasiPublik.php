<?php

namespace App\Filament\Resources\InformasiPublikResource\Pages;

use App\Filament\Resources\InformasiPublikResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * Halaman pembuatan artikel informasi publik desa.
 *
 * Menangani logika khusus sebelum menyimpan data, yaitu normalisasi field cover_image
 * yang dapat berasal dari upload file (cover_image_file) maupun URL eksternal (cover_image_url),
 * sehingga hanya satu nilai yang disimpan ke database.
 *
 * @see \App\Filament\Resources\InformasiPublikResource
 * @see \App\Models\InformasiPublik
 */
class CreateInformasiPublik extends CreateRecord
{
    protected static string $resource = InformasiPublikResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['cover_image_file'])) {
            $data['cover_image'] = $data['cover_image_file'];
        } elseif (!empty($data['cover_image_url'])) {
            $data['cover_image'] = $data['cover_image_url'];
        } else {
            $data['cover_image'] = null;
        }
        unset($data['cover_image_file'], $data['cover_image_url']);
        return $data;
    }
}

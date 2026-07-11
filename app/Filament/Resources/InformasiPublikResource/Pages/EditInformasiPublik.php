<?php

namespace App\Filament\Resources\InformasiPublikResource\Pages;

use App\Filament\Resources\InformasiPublikResource;
use Filament\Resources\Pages\EditRecord;

/**
 * Halaman edit artikel informasi publik desa.
 *
 * Menerapkan logika normalisasi cover_image yang sama dengan halaman create,
 * yaitu menyatukan input file upload (cover_image_file) atau URL eksternal
 * (cover_image_url) menjadi satu field sebelum penyimpanan.
 *
 * @see \App\Filament\Resources\InformasiPublikResource
 * @see \App\Models\InformasiPublik
 */
class EditInformasiPublik extends EditRecord
{
    protected static string $resource = InformasiPublikResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
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

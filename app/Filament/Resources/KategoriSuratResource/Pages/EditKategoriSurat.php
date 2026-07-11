<?php

namespace App\Filament\Resources\KategoriSuratResource\Pages;

use App\Filament\Resources\KategoriSuratResource;
use Filament\Resources\Pages\EditRecord;

/**
 * Halaman edit kategori/jenis surat layanan desa.
 *
 * Menyediakan form untuk mengubah data master kategori surat, termasuk kode surat,
 * nama, template view, serta konfigurasi schema isian dan persyaratan dokumen.
 *
 * @see \App\Filament\Resources\KategoriSuratResource
 * @see \App\Models\KategoriSurat
 */
class EditKategoriSurat extends EditRecord
{
    protected static string $resource = KategoriSuratResource::class;
}

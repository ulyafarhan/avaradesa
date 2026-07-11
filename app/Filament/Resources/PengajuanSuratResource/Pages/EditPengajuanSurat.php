<?php

namespace App\Filament\Resources\PengajuanSuratResource\Pages;

use App\Filament\Resources\PengajuanSuratResource;
use Filament\Resources\Pages\EditRecord;

/**
 * Halaman edit pengajuan surat.
 *
 * Menyediakan form untuk mengubah data pengajuan surat warga, termasuk
 * status verifikasi. Setelah perubahan tersimpan, pengguna dialihkan
 * kembali ke halaman daftar (index).
 *
 * @see \App\Filament\Resources\PengajuanSuratResource
 * @see \App\Models\PengajuanSurat
 */
class EditPengajuanSurat extends EditRecord
{
    protected static string $resource = PengajuanSuratResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

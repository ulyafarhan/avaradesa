<?php

namespace App\Filament\Resources\PendudukResource\Pages;

use App\Filament\Resources\PendudukResource;
use Filament\Resources\Pages\EditRecord;

/**
 * Halaman edit data penduduk.
 *
 * Menyediakan form untuk mengubah data identitas dan kependudukan warga.
 * Setelah perubahan tersimpan, pengguna dialihkan kembali ke halaman daftar (index).
 *
 * @see \App\Filament\Resources\PendudukResource
 * @see \App\Models\Penduduk
 */
class EditPenduduk extends EditRecord
{
    protected static string $resource = PendudukResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

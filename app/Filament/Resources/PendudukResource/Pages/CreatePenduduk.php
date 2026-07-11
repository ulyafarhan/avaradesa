<?php

namespace App\Filament\Resources\PendudukResource\Pages;

use App\Filament\Resources\PendudukResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * Halaman pembuatan data penduduk.
 *
 * Menyediakan form untuk mencatat data identitas dan kependudukan warga.
 * Setelah tersimpan, pengguna dialihkan kembali ke halaman daftar (index).
 *
 * @see \App\Filament\Resources\PendudukResource
 * @see \App\Models\Penduduk
 */
class CreatePenduduk extends CreateRecord
{
    protected static string $resource = PendudukResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

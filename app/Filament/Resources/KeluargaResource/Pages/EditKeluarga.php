<?php

namespace App\Filament\Resources\KeluargaResource\Pages;

use App\Filament\Resources\KeluargaResource;
use Filament\Resources\Pages\EditRecord;

/**
 * Halaman edit data Kartu Keluarga (KK).
 *
 * Menyediakan form untuk mengubah data KK, termasuk nomor KK,
 * kepala keluarga, dan alamat lengkap.
 *
 * @see \App\Filament\Resources\KeluargaResource
 * @see \App\Models\Keluarga
 */
class EditKeluarga extends EditRecord
{
    protected static string $resource = KeluargaResource::class;
}

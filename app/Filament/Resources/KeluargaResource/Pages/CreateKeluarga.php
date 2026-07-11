<?php

namespace App\Filament\Resources\KeluargaResource\Pages;

use App\Filament\Resources\KeluargaResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * Halaman pembuatan data Kartu Keluarga (KK).
 *
 * Menyediakan form untuk mencatat data KK baru, termasuk nomor KK,
 * kepala keluarga, dan alamat lengkap.
 *
 * @see \App\Filament\Resources\KeluargaResource
 * @see \App\Models\Keluarga
 */
class CreateKeluarga extends CreateRecord
{
    protected static string $resource = KeluargaResource::class;
}

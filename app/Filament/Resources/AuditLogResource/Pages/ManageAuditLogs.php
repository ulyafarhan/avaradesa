<?php

namespace App\Filament\Resources\AuditLogResource\Pages;

use App\Filament\Resources\AuditLogResource;
use Filament\Resources\Pages\ManageRecords;

/**
 * Halaman manajemen log audit sistem (read-only).
 *
 * Menampilkan seluruh catatan aktivitas (audit trail) yang tercatat oleh model AuditLog.
 * Tersedia fitur pencarian, filter (berdasarkan tipe user / tindakan), dan tampilan detail
 * perubahan data dalam format diff (sebelum vs sesudah).
 *
 * @see \App\Filament\Resources\AuditLogResource
 * @see \App\Models\AuditLog
 */
class ManageAuditLogs extends ManageRecords
{
    protected static string $resource = AuditLogResource::class;
}

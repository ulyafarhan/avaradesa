<?php

/**
 * SALURAN BROADCAST — AvaraDesa
 *
 * Mendefinisikan kanal real-time untuk event update status:
 *
 * Publik  (tanpa auth): pengajuan, mutasi, dashboard, informasi
 * Privat (auth NIK)  : warga.{nik} — notifikasi per warga
 *
 * @see \App\Events\PengajuanStatusUpdated
 * @see \App\Events\MutasiStatusUpdated
 * @see \App\Events\DashboardStatsUpdated
 * @see \App\Events\InformasiBaru
 */

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Public channels for real-time updates in AvaraDesa.
| These channels broadcast status changes and notifications.
|
*/

// Protected channels — only authenticated users with valid session
Broadcast::channel('surat.{id}', function ($user, $id) {
    return $user->nik === $id || $user->role === 'admin';
});

Broadcast::channel('pengajuan', function ($user) {
    return $user !== null;
});

Broadcast::channel('mutasi', function ($user) {
    return $user !== null;
});

Broadcast::channel('dashboard', function ($user) {
    return $user !== null;
});

// Public channel for general information (safe for public broadcast)
Broadcast::channel('informasi', function () {
    return true;
});

// Private channel for specific warga notifications
Broadcast::channel('warga.{nik}', function ($user, $nik) {
    return $user->nik === $nik;
});

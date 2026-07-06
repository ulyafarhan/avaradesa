<?php

/**
 * RUTE API — AvaraDesa v1
 *
 * Struktur endpoint REST API untuk aplikasi AvaraDesa.
 *
 * Publik  : auth login, informasi publik, statistik, verifikasi QR, webhook Telegram
 * Terotentikasi : logout, profil
 * Warga   : bind Telegram, pengajuan surat, mutasi penduduk
 * Admin   : manajemen surat, mutasi, informasi publik, statistik
 *
 * @see \App\Http\Controllers\Api\
 */

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InformasiPublikController;
use App\Http\Controllers\Api\MutasiPendudukController;
use App\Http\Controllers\Api\AdminPendudukController;
use App\Http\Controllers\Api\AdminResourceController;
use App\Http\Controllers\Api\PengajuanSuratController;
use App\Http\Controllers\Api\StatistikController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\TelegramWebhookController;
use App\Http\Controllers\Api\VerifikasiController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/auth/login/warga', [AuthController::class, 'loginWarga'])->middleware('throttle:login');
    Route::post('/auth/login/admin', [AuthController::class, 'loginAdmin'])->middleware('throttle:login');

    // Auth PIN & Biometric
    Route::post('/auth/register-pin', [AuthController::class, 'registerPin'])->middleware('throttle:5,1');
    Route::post('/auth/login-pin', [AuthController::class, 'loginPin'])->middleware('throttle:5,1');
    Route::post('/auth/login-biometric', [AuthController::class, 'loginBiometric'])->middleware('throttle:5,1');
    Route::post('/auth/reset-pin', [AuthController::class, 'resetPin'])->middleware('throttle:3,1');

    // Informasi Publik
    Route::get('/informasi', [InformasiPublikController::class, 'index']);
    Route::get('/informasi/{slug}', [InformasiPublikController::class, 'show']);

    // Statistik Publik
    Route::get('/statistik/demografi', [StatistikController::class, 'demografi']);
    Route::get('/statistik/layanan', [StatistikController::class, 'layanan']);

    // Verifikasi QR Code
    Route::get('/verifikasi/{hash}', [VerifikasiController::class, 'verify']);

    // Telegram Webhook
    Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle'])->middleware('throttle:60,1');

    // WhatsApp Webhook
    Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'handle'])->middleware('throttle:60,1');
});

// Protected Routes - Shared Auth
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::get('/surat/pengajuan/{id}', [PengajuanSuratController::class, 'show'])->middleware('throttle:30,1');
    Route::get('/surat/pengajuan/{id}/download', [PengajuanSuratController::class, 'downloadPdf'])->middleware('throttle:30,1');

    // Chatbot
    Route::post('/chat', [ChatbotController::class, 'chat'])->middleware('throttle:10,1');
});

// Protected Routes - Warga
Route::prefix('v1')->middleware(['auth:sanctum', 'ability:warga'])->group(function () {

    // Auth
    Route::post('/auth/bind-telegram', [AuthController::class, 'bindTelegram'])->middleware('throttle:5,1');
    Route::post('/auth/register-biometric', [AuthController::class, 'registerBiometric'])->middleware('throttle:5,1');

    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Api\DashboardController::class, 'index'])->middleware('throttle:30,1');

    // Pengajuan Surat
    Route::get('/surat/kategori', [PengajuanSuratController::class, 'kategori'])->middleware('throttle:30,1');
    Route::get('/surat/kategori/{id}', [PengajuanSuratController::class, 'detailKategori'])->middleware('throttle:30,1');
    Route::post('/surat/pengajuan', [PengajuanSuratController::class, 'store'])->middleware('throttle:5,1');
    Route::get('/surat/pengajuan', [PengajuanSuratController::class, 'index'])->middleware('throttle:30,1');

    // Mutasi Penduduk
    Route::post('/mutasi', [MutasiPendudukController::class, 'store'])->middleware('throttle:3,1');
    Route::get('/mutasi', [MutasiPendudukController::class, 'index'])->middleware('throttle:30,1');

    // Sync
    Route::post('/sync/push', [SyncController::class, 'push'])->middleware('throttle:10,1');
    Route::get('/sync/pull', [SyncController::class, 'pull'])->middleware('throttle:30,1');
});

// Protected Routes - Admin Only
Route::prefix('v1/admin')->middleware(['auth:sanctum', 'ability:admin'])->group(function () {

    // Manajemen Penduduk
    Route::get('/penduduk', [AdminPendudukController::class, 'index']);
    Route::get('/penduduk/{id}', [AdminPendudukController::class, 'show']);
    Route::post('/penduduk', [AdminPendudukController::class, 'store']);
    Route::put('/penduduk/{id}', [AdminPendudukController::class, 'update']);
    Route::delete('/penduduk/{id}', [AdminPendudukController::class, 'destroy']);

    // Pengajuan Surat Management
    Route::get('/surat/pengajuan', [PengajuanSuratController::class, 'adminIndex'])->middleware('throttle:30,1');
    Route::post('/surat/pengajuan/{id}/approve', [PengajuanSuratController::class, 'approve'])->middleware('throttle:30,1');
    Route::post('/surat/pengajuan/{id}/reject', [PengajuanSuratController::class, 'reject'])->middleware('throttle:30,1');

    // Mutasi Penduduk Management
    Route::get('/mutasi', [MutasiPendudukController::class, 'adminIndex'])->middleware('throttle:30,1');
    Route::post('/mutasi/{id}/approve', [MutasiPendudukController::class, 'approve'])->middleware('throttle:30,1');
    Route::post('/mutasi/{id}/reject', [MutasiPendudukController::class, 'reject'])->middleware('throttle:30,1');

    // Informasi Publik Management
    Route::get('/informasi', [InformasiPublikController::class, 'adminIndex']);
    Route::get('/informasi/{id}', [InformasiPublikController::class, 'adminShow']);
    Route::post('/informasi', [InformasiPublikController::class, 'store']);
    Route::put('/informasi/{id}', [InformasiPublikController::class, 'update']);
    Route::delete('/informasi/{id}', [InformasiPublikController::class, 'destroy']);

    // Statistik Management
    Route::post('/statistik/clear-cache', [StatistikController::class, 'clearCache']);

    // Extra Management (Keluarga, Kategori Surat, Inventaris, Audit Log)
    Route::get('/keluarga', [AdminResourceController::class, 'keluargaIndex']);
    Route::post('/keluarga', [AdminResourceController::class, 'keluargaStore']);
    Route::delete('/keluarga/{no_kk}', [AdminResourceController::class, 'keluargaDestroy']);

    Route::get('/kategori-surat', [AdminResourceController::class, 'kategoriSuratIndex']);
    Route::post('/kategori-surat', [AdminResourceController::class, 'kategoriSuratStore']);
    Route::put('/kategori-surat/{id}', [AdminResourceController::class, 'kategoriSuratUpdate']);
    Route::delete('/kategori-surat/{id}', [AdminResourceController::class, 'kategoriSuratDestroy']);

    Route::get('/fasilitas', [AdminResourceController::class, 'fasilitasIndex']);
    Route::post('/fasilitas', [AdminResourceController::class, 'fasilitasStore']);
    Route::delete('/fasilitas/{id}', [AdminResourceController::class, 'fasilitasDestroy']);

    Route::get('/audit-log', [AdminResourceController::class, 'auditLogIndex']);
});

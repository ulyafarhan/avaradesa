<?php

use App\Http\Controllers\Web\CitizenAuthController;
use App\Http\Controllers\Web\CitizenDashboardController;
use App\Http\Controllers\Web\CitizenFamilyController;
use App\Http\Controllers\Web\CitizenProfileController;
use App\Http\Controllers\Web\CitizenSubmissionController;
use App\Http\Controllers\Web\PublicPortalController;
use App\Http\Controllers\Admin\NotificationSettingsController;
use App\Http\Controllers\Admin\NotificationApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPortalController::class, 'home'])->name('home');
Route::get('/profil', [PublicPortalController::class, 'profile'])->name('profile');
Route::get('/informasi', [PublicPortalController::class, 'information'])->name('information.index');
Route::get('/informasi/{slug}', [PublicPortalController::class, 'informationShow'])->name('information.show');
Route::get('/verifikasi', [PublicPortalController::class, 'verifyIndex'])->name('verify.index');
Route::get('/verifikasi/{hash}', [PublicPortalController::class, 'verify'])->name('verify');
Route::get('/fasilitas', [PublicPortalController::class, 'fasilitas'])->name('fasilitas');
Route::get('/fasilitas/{fasilitas}', [PublicPortalController::class, 'fasilitasShow'])->name('fasilitas.show');
Route::get('/statistik', [PublicPortalController::class, 'statistik'])->name('statistik');
Route::post('/aspirasi', [PublicPortalController::class, 'storeAspirasi'])
    ->middleware('throttle:5,1')
    ->name('aspirasi.store');

Route::middleware('guest:penduduk')->group(function () {
    Route::get('/login', [CitizenAuthController::class, 'create'])->name('login');
    Route::post('/login', [CitizenAuthController::class, 'store'])->name('login.store')->middleware('throttle:5,1');
});

Route::post('/logout', [CitizenAuthController::class, 'destroy'])
    ->middleware('auth:penduduk')
    ->name('logout');

Route::prefix('warga')
    ->name('warga.')
    ->middleware('auth:penduduk')
    ->group(function () {
        Route::get('/dashboard', CitizenDashboardController::class)->name('dashboard');
        Route::get('/profil', [CitizenProfileController::class, 'show'])->name('profil.show');
        Route::post('/profil', [CitizenProfileController::class, 'update'])->name('profil.update');
        Route::get('/keluarga', [CitizenFamilyController::class, 'index'])->name('keluarga.index');
        Route::put('/keluarga/{nik}', [CitizenFamilyController::class, 'update'])->name('keluarga.update');
        Route::get('/surat/ajukan/{kategori}', [CitizenSubmissionController::class, 'create'])->name('surat.create');
        Route::post('/surat/pengajuan', [CitizenSubmissionController::class, 'store'])->name('surat.store');
        Route::get('/pengajuan/{pengajuan}', [CitizenSubmissionController::class, 'show'])->name('pengajuan.show');
        Route::get('/pengajuan/{pengajuan}/print', [CitizenSubmissionController::class, 'print'])->name('pengajuan.print');
    });

Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/notifications', NotificationSettingsController::class)->name('notifications');
    Route::get('/notifications/wa/status', [NotificationApiController::class, 'waStatus'])->name('notifications.wa.status');
    Route::get('/notifications/wa/qr', [NotificationApiController::class, 'waQr'])->name('notifications.wa.qr');
    Route::post('/notifications/wa/test', [NotificationApiController::class, 'waTest'])->name('notifications.wa.test');
    Route::get('/notifications/telegram/status', [NotificationApiController::class, 'tgStatus'])->name('notifications.tg.status');
    Route::post('/notifications/telegram/broadcast', [NotificationApiController::class, 'tgBroadcast'])->name('notifications.tg.broadcast');
});

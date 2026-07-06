<?php

/**
 * RUTE WEB — AvaraDesa
 *
 * Halaman portal publik dan area warga (frontend web).
 *
 * Publik (tanpa auth) : beranda, profil desa, informasi, verifikasi QR, statistik
 * Guest (belum login) : login warga (NIK-based)
 * Warga (auth:penduduk): dashboard, profil, keluarga, pengajuan surat & cetak
 *
 * @see \App\Http\Controllers\Web\
 */

use App\Http\Controllers\Web\CitizenAuthController;
use App\Http\Controllers\Web\CitizenDashboardController;
use App\Http\Controllers\Web\CitizenFamilyController;
use App\Http\Controllers\Web\CitizenProfileController;
use App\Http\Controllers\Web\CitizenSubmissionController;
use App\Http\Controllers\Web\PublicPortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPortalController::class, 'home'])->name('home');
Route::get('/profil', [PublicPortalController::class, 'profile'])->name('profile');
Route::get('/informasi', [PublicPortalController::class, 'information'])->name('information.index');
Route::get('/informasi/{slug}', [PublicPortalController::class, 'informationShow'])->name('information.show');
Route::get('/verifikasi', [PublicPortalController::class, 'verifyIndex'])->name('verify.index');
Route::get('/verifikasi/{hash}', [PublicPortalController::class, 'verify'])->name('verify');
Route::get('/fasilitas', [PublicPortalController::class, 'fasilitas'])->name('fasilitas');
Route::get('/statistik', [PublicPortalController::class, 'statistik'])->name('statistik');

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

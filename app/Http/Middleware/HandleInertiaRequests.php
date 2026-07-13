<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

/**
 * Middleware untuk membagikan data global secara dinamis dari backend Laravel ke frontend Vue via Inertia.js.
 */
class HandleInertiaRequests extends Middleware
{
    /**
     * @var string Root view yang digunakan untuk memuat aset frontend utama.
     */
    protected $rootView = 'app';

    /**
     * Mendefinisikan kumpulan data bersama (shared data) yang akan diakses di seluruh komponen Vue.
     */
    public function share(Request $request): array
    {
        $rawNamaDesa = \App\Models\PengaturanDesa::get('nama_desa', 'Udeung');
        $cleanNamaDesa = preg_replace('/^(desa|desa)\s+/i', '', $rawNamaDesa);

        return [
            ...parent::share($request),
            'auth' => [
                'warga' => fn () => $request->user('penduduk'),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'settings' => [
                'nama_desa' => $cleanNamaDesa,
                'nama_desa' => $cleanNamaDesa,
                'kecamatan' => \App\Models\PengaturanDesa::get('kecamatan', 'Bandar Baru'),
                'kabupaten' => \App\Models\PengaturanDesa::get('kabupaten', 'Pidie Jaya'),
                'provinsi' => \App\Models\PengaturanDesa::get('provinsi', 'Aceh'),
                'kode_pos' => \App\Models\PengaturanDesa::get('kode_pos', '24186'),
                'telepon' => \App\Models\PengaturanFrontend::get('telepon_operator') ?? \App\Models\PengaturanDesa::get('telepon', '-'),
                'email' => \App\Models\PengaturanDesa::get('email', '-'),
                'alamat' => \App\Models\PengaturanFrontend::get('alamat_kantor') ?? \App\Models\PengaturanDesa::get('alamat', '-'),
                'nama_kepala_desa' => \App\Models\PengaturanDesa::get('nama_kepala_desa', 'Nama Kepala Desa'),
                'nama_kepala desa' => \App\Models\PengaturanDesa::get('nama_kepala_desa', 'Nama Kepala Desa'),
                'nip_kepala_desa' => \App\Models\PengaturanDesa::get('nip_kepala_desa', ''),
                'nip_kepala desa' => \App\Models\PengaturanDesa::get('nip_kepala_desa', ''),
                'nama_sekdes' => \App\Models\PengaturanFrontend::get('nama_sekdes') ?? \App\Models\PengaturanDesa::get('nama_sekdes', 'Nama Sekretaris Desa'),
                'nama_operator' => \App\Models\PengaturanFrontend::get('nama_operator') ?? \App\Models\PengaturanDesa::get('nama_operator', 'Nama Operator'),
                'medsos_facebook' => \App\Models\PengaturanFrontend::get('medsos_facebook') ?? \App\Models\PengaturanDesa::get('medsos_facebook', ''),
                'medsos_instagram' => \App\Models\PengaturanFrontend::get('medsos_instagram') ?? \App\Models\PengaturanDesa::get('medsos_instagram', ''),
                'medsos_twitter' => \App\Models\PengaturanFrontend::get('medsos_twitter') ?? \App\Models\PengaturanDesa::get('medsos_twitter', ''),
                'medsos_youtube' => \App\Models\PengaturanFrontend::get('medsos_youtube') ?? \App\Models\PengaturanDesa::get('medsos_youtube', ''),
                'tahun_anggaran' => \App\Models\PengaturanFrontend::get('tahun_anggaran') ?? \App\Models\PengaturanDesa::get('tahun_anggaran', date('Y')),
                'logo_desa' => (\App\Models\PengaturanDesa::get('logo_desa') && \Illuminate\Support\Facades\Storage::disk('public')->exists(\App\Models\PengaturanDesa::get('logo_desa'))) ? \Illuminate\Support\Facades\Storage::url(\App\Models\PengaturanDesa::get('logo_desa')) : '/logo.svg',
                'logo_desa' => (\App\Models\PengaturanDesa::get('logo_desa') && \Illuminate\Support\Facades\Storage::disk('public')->exists(\App\Models\PengaturanDesa::get('logo_desa'))) ? \Illuminate\Support\Facades\Storage::url(\App\Models\PengaturanDesa::get('logo_desa')) : '/logo.svg',
                'logo_fav' => (\App\Models\PengaturanDesa::get('logo_fav') && \Illuminate\Support\Facades\Storage::disk('public')->exists(\App\Models\PengaturanDesa::get('logo_fav'))) ? \Illuminate\Support\Facades\Storage::url(\App\Models\PengaturanDesa::get('logo_fav')) : '/logo-fav.svg',
                'banner_desa' => (\App\Models\PengaturanDesa::get('banner_desa') && \Illuminate\Support\Facades\Storage::disk('public')->exists(\App\Models\PengaturanDesa::get('banner_desa'))) ? \Illuminate\Support\Facades\Storage::url(\App\Models\PengaturanDesa::get('banner_desa')) : null,
                'banner_desa' => (\App\Models\PengaturanDesa::get('banner_desa') && \Illuminate\Support\Facades\Storage::disk('public')->exists(\App\Models\PengaturanDesa::get('banner_desa'))) ? \Illuminate\Support\Facades\Storage::url(\App\Models\PengaturanDesa::get('banner_desa')) : null,
            ],
        ];
    }
}

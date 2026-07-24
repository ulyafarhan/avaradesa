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
        return [
            ...parent::share($request),
            'auth' => [
                'warga' => fn () => $request->user('penduduk'),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'settings' => fn () => $this->getSettings(),
        ];
    }

    private function getSettings(): array
    {
        $rawNamaDesa = \App\Models\PengaturanDesa::get('nama_desa', 'Udeung');
        $cleanNamaDesa = preg_replace('/^(desa|desa)\s+/i', '', $rawNamaDesa);

        $getFotoUrl = function ($kunci) {
            $val = \App\Models\PengaturanFrontend::get($kunci);
            if (!$val) return null;
            if (str_starts_with($val, 'http://') || str_starts_with($val, 'https://')) return $val;
            return \Illuminate\Support\Facades\Storage::exists('public/' . $val)
                ? \Illuminate\Support\Facades\Storage::url($val)
                : null;
        };

        return [
            'nama_desa' => $cleanNamaDesa,
            'kecamatan' => \App\Models\PengaturanDesa::get('kecamatan', 'Bandar Baru'),
            'kabupaten' => \App\Models\PengaturanDesa::get('kabupaten', 'Pidie Jaya'),
            'provinsi' => \App\Models\PengaturanDesa::get('provinsi', 'Aceh'),
            'kode_pos' => \App\Models\PengaturanDesa::get('kode_pos', '24186'),
            'telepon' => \App\Models\PengaturanFrontend::get('telepon_operator') ?? \App\Models\PengaturanDesa::get('telepon', '-'),
            'email' => \App\Models\PengaturanDesa::get('email', '-'),
            'alamat' => \App\Models\PengaturanFrontend::get('alamat_kantor') ?? \App\Models\PengaturanDesa::get('alamat', '-'),
            'nama_kepala_desa' => \App\Models\PengaturanFrontend::get('nama_kepala_desa') ?? \App\Models\PengaturanDesa::get('nama_kepala_desa', 'Nama Kepala Desa'),
            'nip_kepala_desa' => \App\Models\PengaturanDesa::get('nip_kepala_desa', ''),
            'foto_kepala_desa' => $getFotoUrl('foto_kepala_desa'),
            'nama_sekdes' => \App\Models\PengaturanFrontend::get('nama_sekdes') ?? \App\Models\PengaturanDesa::get('nama_sekdes', 'Nama Sekretaris Desa'),
            'foto_sekdes' => $getFotoUrl('foto_sekdes'),
            'foto_operator' => $getFotoUrl('foto_operator'),
            'foto_kantor' => $getFotoUrl('foto_kantor'),
            'nama_operator' => \App\Models\PengaturanFrontend::get('nama_operator') ?? \App\Models\PengaturanDesa::get('nama_operator', 'Nama Operator'),
            'medsos_facebook' => \App\Models\PengaturanFrontend::get('medsos_facebook') ?? \App\Models\PengaturanDesa::get('medsos_facebook', ''),
            'medsos_instagram' => \App\Models\PengaturanFrontend::get('medsos_instagram') ?? \App\Models\PengaturanDesa::get('medsos_instagram', ''),
            'medsos_twitter' => \App\Models\PengaturanFrontend::get('medsos_twitter') ?? \App\Models\PengaturanDesa::get('medsos_twitter', ''),
            'medsos_youtube' => \App\Models\PengaturanFrontend::get('medsos_youtube') ?? \App\Models\PengaturanDesa::get('medsos_youtube', ''),
            'tahun_anggaran' => \App\Models\PengaturanFrontend::get('tahun_anggaran') ?? \App\Models\PengaturanDesa::get('tahun_anggaran', date('Y')),
            'logo_desa' => (\App\Models\PengaturanDesa::get('logo_desa') && \Illuminate\Support\Facades\Storage::disk('public')->exists(\App\Models\PengaturanDesa::get('logo_desa'))) ? \Illuminate\Support\Facades\Storage::url(\App\Models\PengaturanDesa::get('logo_desa')) : '/logo.svg',
            'logo_fav' => (\App\Models\PengaturanDesa::get('logo_fav') && \Illuminate\Support\Facades\Storage::disk('public')->exists(\App\Models\PengaturanDesa::get('logo_fav'))) ? \Illuminate\Support\Facades\Storage::url(\App\Models\PengaturanDesa::get('logo_fav')) : '/logo-fav.svg',
            'banner_desa' => (\App\Models\PengaturanDesa::get('banner_desa') && \Illuminate\Support\Facades\Storage::disk('public')->exists(\App\Models\PengaturanDesa::get('banner_desa'))) ? \Illuminate\Support\Facades\Storage::url(\App\Models\PengaturanDesa::get('banner_desa')) : null,
            'visi' => \App\Models\PengaturanDesa::get('visi', '"Mewujudkan Desa yang Mandiri, Sejahtera, Transparan, Bebas Narkoba, dan Religius berbasis Pelayanan Digital Prima."'),
            'misi' => \App\Models\PengaturanDesa::get('misi', [
                ['misi_item' => 'Tata Kelola & Digitalisasi Administrasi: Mengoptimalkan pelayanan administrasi kependudukan desa secara cepat dan akuntabel melalui Portal Mandiri Warga.'],
                ['misi_item' => 'Imunitas Bahaya Narkotika (KBN): Membangun daya tangkal dan pengawasan sosial pemuda secara aktif guna mempertahankan status Kampung Bebas Narkoba.'],
                ['misi_item' => 'Ekonomi Agraris & Kemitraan UMKM: Meningkatkan produktivitas komoditas padi sawah, jagung, kakao, serta kemasan produk rumah tangga (kue tradisional Aceh).']
            ]),
            'sejarah_desa' => \App\Models\PengaturanFrontend::get('sejarah_desa', 'Desa didirikan sejak masa lampau di kecamatan ini. Nama wilayah disematkan karena letak historis dan geografisnya yang melimpah akan kekayaan alam lokal.\n\nKini, aksesibilitas warga semakin kuat dengan hadirnya jembatan gantung Garuda yang melintasi Sungai Lueng Putu, menghubungkan langsung Desa dengan Desa Ara. Jembatan gantung ini menjadi urat nadi perekonomian, peribadahan santri menuju Pesantren Raudhatul Ulum, serta mobilitas anak-anak menuju SD Negeri yang telah mendidik anak-anak desa sejak tahun 1977.'),
            'kbn_tanggal_resmi' => \App\Models\PengaturanFrontend::get('kbn_tanggal_resmi', 'Senin, 14 Oktober 2024'),
            'kbn_jumlah_desa' => \App\Models\PengaturanFrontend::get('kbn_jumlah_desa', '221 desa'),
            'geo_koordinat' => \App\Models\PengaturanFrontend::get('geo_koordinat', '5.277732, 96.102347'),
            'geo_komoditas' => \App\Models\PengaturanFrontend::get('geo_komoditas', 'Padi, Jagung, Kakao'),
            'geo_batas_utara' => \App\Models\PengaturanFrontend::get('geo_batas_utara', 'Desa Ara (Jembatan)'),
            'geo_batas_selatan' => \App\Models\PengaturanFrontend::get('geo_batas_selatan', 'Desa Blang Baro'),
            'geo_orbitasi' => \App\Models\PengaturanFrontend::get('geo_orbitasi', '3.5 Km ke Lueng Putu'),
            'sebutan_desa' => \App\Models\PengaturanFrontend::get('sebutan_desa') ?? \App\Models\PengaturanDesa::get('sebutan_desa', 'Pemerintah Desa'),
        ];
    }
}

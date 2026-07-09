<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\InformasiPublik;
use App\Models\KategoriSurat;
use App\Models\PengajuanSurat;
use App\Services\StatistikService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller untuk mengelola halaman publik dan layanan portal umum.
 *
 * Menangani tampilan beranda, profil desa, informasi publik,
 * verifikasi surat, statistik, dan aspirasi masyarakat.
 */
class PublicPortalController extends Controller
{
    /**
     * Menyajikan halaman beranda publik dengan statistik desa dan berita terbaru.
     *
     * Menampilkan ringkasan data demografi, layanan publik yang tersedia,
     * berita/pengumuman terbaru, dan daftar kategori surat yang aktif.
     *
     * @param  \App\Services\StatistikService  $statistik  Service untuk mengambil data statistik desa
     * @return \Inertia\Response  Halaman beranda publik
     */
    public function home(StatistikService $statistik): Response
    {
        return Inertia::render('Public/Home', [
            'demografi' => $statistik->getDemografi(),
            'layanan' => $statistik->getLayanan(),
            'berita' => InformasiPublik::query()
                ->published()
                ->with('author:id,username')
                ->latest('created_at')
                ->limit(3)
                ->get(),
            'kategoriSurat' => KategoriSurat::query()
                ->active()
                ->orderBy('nama_surat')
                ->limit(6)
                ->get(['id', 'kode_surat', 'nama_surat']),
        ]);
    }

    /**
     * Menyajikan halaman profil desa dan daftar perangkat desa.
     *
     * Mengambil data nama dan foto perangkat desa (Kepala Desa, Sekretaris Desa,
     * Operator Layanan) dari pengaturan frontend dan desa.
     *
     * @return \Inertia\Response  Halaman profil desa
     */
    public function profile(): Response
    {
        $getFotoUrl = function ($kunci, $fallbackKey = null) {
            $val = \App\Models\PengaturanFrontend::get($kunci);
            if (!$val && $fallbackKey) {
                $val = \App\Models\PengaturanDesa::get($fallbackKey);
            }
            if (empty($val)) {
                return '/images/default-avatar.png';
            }
            if (str_starts_with($val, 'http://') || str_starts_with($val, 'https://') || str_starts_with($val, '/images/')) {
                return $val;
            }
            return \Illuminate\Support\Facades\Storage::url($val);
        };

        return Inertia::render('Public/Profile', [
            'perangkat' => [
                [
                    'jabatan' => 'Kepala Desa',
                    'nama' => \App\Models\PengaturanDesa::get('nama_kepala_desa', 'Nama Kepala Desa'),
                    'foto' => $getFotoUrl('foto_kepala_desa', 'foto_kepala_desa'),
                ],
                [
                    'jabatan' => 'Sekretaris Desa',
                    'nama' => \App\Models\PengaturanFrontend::get('nama_sekdes') ?? \App\Models\PengaturanDesa::get('nama_sekdes', 'Nama Sekretaris Desa'),
                    'foto' => $getFotoUrl('foto_sekdes', 'foto_sekdes'),
                ],
                [
                    'jabatan' => 'Operator Layanan',
                    'nama' => \App\Models\PengaturanFrontend::get('nama_operator') ?? \App\Models\PengaturanDesa::get('nama_operator', 'Nama Operator'),
                    'foto' => $getFotoUrl('foto_operator', 'foto_operator'),
                ],
            ],
        ]);
    }

    /**
     * Menyajikan halaman indeks kumpulan informasi publik/berita/pengumuman desa.
 *
     * Menampilkan daftar informasi publik yang sudah dipublikasikan dengan
     * mendukung filter berdasarkan kategori dan pencarian berdasarkan judul/konten.
     *
     * @return \Inertia\Response  Halaman indeks informasi publik dengan data terpaginasi
     */
    public function information(): Response
    {
        $query = InformasiPublik::query()
            ->published()
            ->with('author:id,username')
            ->latest('created_at');

        $query->when(request('kategori'), function ($q, $kategori) {
            return $q->where('kategori', $kategori);
        });

        $query->when(request('search'), function ($q, $search) {
            return $q->where(function ($sub) use ($search) {
                $sub->where('judul', 'like', '%' . $search . '%')
                    ->orWhere('konten', 'like', '%' . $search . '%');
            });
        });

        return Inertia::render('Public/Information/Index', [
            'informasi' => $query->paginate(9)->withQueryString(),
            'kategori' => InformasiPublik::query()
                ->published()
                ->select('kategori')
                ->distinct()
                ->orderBy('kategori')
                ->pluck('kategori'),
            'filters' => request()->only(['kategori', 'search']),
        ]);
    }

    /**
     * Menyajikan detail dari suatu berita/pengumuman publik berdasarkan slug.
     *
     * @param  string  $slug  Slug unik dari informasi publik yang ingin ditampilkan
     * @return \Inertia\Response  Halaman detail informasi publik
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException  Jika informasi dengan slug tidak ditemukan
     */
    public function informationShow(string $slug): Response
    {
        return Inertia::render('Public/Information/Show', [
            'informasi' => InformasiPublik::query()
                ->published()
                ->with('author:id,username')
                ->where('slug', $slug)
                ->firstOrFail(),
        ]);
    }

    /**
     * Menyajikan halaman beranda verifikasi keabsahan surat lewat QR Code.
     *
     * Menampilkan form kosong untuk memulai proses verifikasi dokumen surat.
     *
     * @return \Inertia\Response  Halaman verifikasi surat
     */
    public function verifyIndex(): Response
    {
        return Inertia::render('Public/Verify', [
            'result' => null
        ]);
    }

    /**
     * Memproses verifikasi detail data surat berdasarkan hash tanda tangan elektronik.
     *
     * Mencari pengajuan surat berdasarkan qr_hash, nomor registrasi, atau nomor surat.
     * Mengembalikan data validitas beserta detail surat jika ditemukan dan statusnya Selesai.
     *
     * @param  string  $hash  Hash QR code, nomor registrasi, atau nomor surat yang akan diverifikasi
     * @return \Inertia\Response  Halaman verifikasi dengan hasil pencarian surat
     */
    public function verify(string $hash): Response
    {
        $pengajuan = PengajuanSurat::query()
            ->with(['kategori:id,nama_surat', 'pemohon:nik,nama_lengkap', 'verifikator:id,username'])
            ->where(function ($query) use ($hash) {
                $query->where('qr_hash', $hash)
                      ->orWhere('nomor_registrasi', $hash)
                      ->orWhere('nomor_surat', $hash);
            })
            ->first();

        return Inertia::render('Public/Verify', [
            'result' => $pengajuan && $pengajuan->status === 'Selesai'
                ? [
                    'valid' => true,
                    'message' => 'Dokumen terverifikasi.',
                    'nomor_registrasi' => $pengajuan->nomor_registrasi,
                    'jenis_surat' => $pengajuan->kategori?->nama_surat,
                    'nama_pemohon' => $pengajuan->pemohon?->nama_lengkap,
                    'nik_pemohon' => $pengajuan->nik_pemohon,
                    'tanggal_terbit' => $pengajuan->updated_at?->format('d-m-Y'),
                    'diverifikasi_oleh' => $pengajuan->verifikator?->username,
                ]
                : [
                    'valid' => false,
                    'message' => $pengajuan ? 'Dokumen belum selesai diproses.' : 'Dokumen tidak ditemukan atau tidak valid.',
                ],
        ]);
    }

    /**
     * Menyajikan halaman visualisasi data demografi dan statistik layanan desa.
     *
     * Menampilkan grafik dan ringkasan data demografi serta statistik
     * penggunaan layanan publik di desa.
     *
     * @param  \App\Services\StatistikService  $statistik  Service untuk mengambil data statistik desa
     * @return \Inertia\Response  Halaman statistik desa
     */
    public function statistik(StatistikService $statistik): Response
    {
        return Inertia::render('Public/Statistik', [
            'demografi' => $statistik->getDemografi(),
            'layanan' => $statistik->getLayanan(),
        ]);
    }

    /**
     * Menyajikan halaman daftar fasilitas umum/sosial desa.
     *
     * @return \Inertia\Response  Halaman daftar fasilitas publik
     */
    public function fasilitas(): Response
    {
        $fasilitas = \App\Models\InventarisFasilitas::query()
            ->where('is_publik', true)
            ->orderBy('nama_fasilitas')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_fasilitas' => $item->nama_fasilitas,
                    'jenis_fasilitas' => $item->jenis_fasilitas,
                    'deskripsi' => $item->deskripsi,
                    'lokasi' => $item->lokasi,
                    'kondisi' => $item->kondisi,
                    'tahun_dibangun' => $item->tahun_dibangun,
                    'foto' => $item->foto_url ?? $item->foto,
                    'latitude' => $item->latitude,
                    'longitude' => $item->longitude,
                    'status_penggunaan' => $item->status_penggunaan,
                ];
            });

        return Inertia::render('Public/Fasilitas', [
            'fasilitas' => $fasilitas,
        ]);
    }

    /**
     * Menyimpan aspirasi dari pengunjung portal publik.
     *
     * Menerima pesan aspirasi dari pengunjung yang tidak terautentikasi,
     * mencatatnya ke dalam audit log, dan mengembalikan pesan sukses.
     *
     * @param  \Illuminate\Http\Request  $request  Request yang berisi field 'pesan'
     * @return \Illuminate\Http\RedirectResponse  Redirect kembali dengan pesan sukses
     * @throws \Illuminate\Validation\ValidationException  Jika pesan tidak valid atau melebihi batas karakter
     */
    public function storeAspirasi(Request $request): RedirectResponse
    {
        $request->validate([
            'pesan' => 'required|string|max:1000',
        ]);

        \App\Models\AuditLog::log('public', request()->ip(), 'aspirasi', 'aspirasi', null, null, [
            'pesan' => $request->pesan,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Aspirasi terkirim. Terima kasih!');
    }
}

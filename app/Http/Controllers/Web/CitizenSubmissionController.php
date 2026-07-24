<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Jobs\SendStatusWhatsappJob;
use App\Models\KategoriSurat;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\TrackingPengajuanSurat;
use App\Services\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller untuk mengelola siklus hidup pengajuan surat oleh warga.
 *
 * Menangani pembuatan pengajuan baru, penyimpanan data beserta lampiran,
 * tampilan detail tracking, dan cetak surat yang telah selesai diproses.
 */
class CitizenSubmissionController extends Controller
{
    /**
     * Menampilkan form pembuatan pengajuan surat berdasarkan kategori tertentu.
     *
     * Mengambil data warga pemohon, data anggota keluarga (jika kepala keluarga),
     * dan data kategori surat untuk ditampilkan di form pengajuan.
     *
     * @param  \Illuminate\Http\Request  $request  Request yang mengandung data warga terautentikasi
     * @param  \App\Models\KategoriSurat  $kategori  Model kategori surat berdasarkan route parameter
     * @return \Inertia\Response  Halaman form pembuatan pengajuan surat
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException  Jika kategori surat tidak aktif
     */
    public function create(Request $request, KategoriSurat $kategori): Response
    {
        abort_unless($kategori->is_active, 404);

        $warga = $request->user('penduduk');
        $keluarga = $warga->keluarga;
        $isKepalaKeluarga = $keluarga && $keluarga->kepala_keluarga_nik === $warga->nik;

        $anggotaKeluarga = collect();
        if ($isKepalaKeluarga && $keluarga) {
            $anggotaKeluarga = Penduduk::where('no_kk', $keluarga->no_kk)
                ->aktif()
                ->get()
                ->map(fn ($p) => [
                    'nik' => $p->nik,
                    'nama_lengkap' => $p->nama_lengkap,
                    'jenis_kelamin' => $p->jenis_kelamin,
                    'tempat_lahir' => $p->tempat_lahir,
                    'tanggal_lahir' => $p->tanggal_lahir?->format('Y-m-d'),
                    'agama' => $p->agama,
                    'pendidikan' => $p->pendidikan,
                    'pekerjaan' => $p->pekerjaan,
                    'status_perkawinan' => $p->status_perkawinan,
                    'status_keluarga' => $p->status_keluarga,
                    'alamat' => $keluarga->alamat,
                    'dusun' => $keluarga->dusun,
                    'rt_rw' => $keluarga->rt_rw,
                    'no_kk' => $keluarga->no_kk,
                    'foto_profil' => $p->foto_profil,
                    'foto_ktp' => $p->foto_ktp,
                    'foto_kk' => $p->foto_kk,
                ]);
        }

        $wargaData = [
            'nik' => $warga->nik,
            'nama_lengkap' => $warga->nama_lengkap,
            'jenis_kelamin' => $warga->jenis_kelamin,
            'tempat_lahir' => $warga->tempat_lahir,
            'tanggal_lahir' => $warga->tanggal_lahir?->format('Y-m-d'),
            'agama' => $warga->agama,
            'pendidikan' => $warga->pendidikan,
            'pekerjaan' => $warga->pekerjaan,
            'status_perkawinan' => $warga->status_perkawinan,
            'status_keluarga' => $warga->status_keluarga,
            'alamat' => $keluarga?->alamat,
            'dusun' => $keluarga?->dusun,
            'rt_rw' => $keluarga?->rt_rw,
            'no_kk' => $keluarga?->no_kk,
            'foto_profil' => $warga->foto_profil,
            'foto_ktp' => $warga->foto_ktp,
            'foto_kk' => $warga->foto_kk,
        ];

        return Inertia::render('Citizen/Submission/Create', [
            'kategori' => $kategori,
            'wargaData' => $wargaData,
            'anggotaKeluarga' => $anggotaKeluarga,
            'isKepalaKeluarga' => $isKepalaKeluarga,
        ]);
    }

    /**
     * Memproses penyimpanan berkas pengajuan surat baru beserta lampiran dokumennya.
     *
     * Memvalidasi input, memeriksa otorisasi pemohon (perorangan atau anggota keluarga),
     * memvalidasi field wajib berdasarkan schema kategori surat, mengunggah berkas syarat,
     * membuat record pengajuan dan tracking status dalam satu transaksi database.
     *
     * @param  \Illuminate\Http\Request  $request  Request yang berisi data isian, file syarat, dan kategori surat
     * @return \Illuminate\Http\RedirectResponse  Redirect ke halaman detail pengajuan yang baru dibuat
     * @throws \Illuminate\Validation\ValidationException  Jika validasi input gagal
     * @throws \Exception  Jika dokumen syarat wajib tidak diunggah
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException  Jika pengguna tidak berwenang mengajukan untuk pemohon
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kategori_surat_id' => ['required', 'exists:kategori_surat,id'],
            'nik_pemohon' => ['required', 'string', 'size:16', 'exists:penduduk,nik'],
            'data_isian' => ['required', 'array'],
            'file_syarat' => ['required', 'array'],
        ]);

        $warga = $request->user('penduduk');
        $kategori = KategoriSurat::query()->active()->findOrFail($validated['kategori_surat_id']);

        $nikPemohon = $validated['nik_pemohon'];
        if ($nikPemohon !== $warga->nik) {
            $keluarga = $warga->keluarga;
            abort_unless(
                $keluarga
                && $keluarga->kepala_keluarga_nik === $warga->nik
                && Penduduk::where('nik', $nikPemohon)->where('no_kk', $keluarga->no_kk)->exists(),
                403,
                'Anda hanya dapat mengajukan surat untuk anggota keluarga Anda sendiri.'
            );
        }

        foreach ($kategori->schema_isian ?? [] as $field) {
            if (($field['required'] ?? false) && blank(data_get($validated, 'data_isian.' . $field['field']))) {
                return back()->withErrors(['data_isian.' . $field['field'] => 'Kolom ini wajib diisi.'])->withInput();
            }
        }

        $uploadedPaths = [];
        $pengajuan = null;

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $nikPemohon, $kategori, $validated, &$uploadedPaths, &$pengajuan) {
            $pemohon = Penduduk::where('nik', $nikPemohon)->lockForUpdate()->firstOrFail();

            foreach ($kategori->syarat_dokumen ?? [] as $dokumen) {
                $key = str($dokumen)->lower()->slug('_')->toString();

                if ($request->hasFile("file_syarat.$key")) {
                    $request->validate([
                        "file_syarat.$key" => ['file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:2048'],
                    ]);
                    $file = $request->file("file_syarat.$key");
                    $path = $file->store('submissions/documents', 'public');
                    $uploadedPaths[$key] = $path;

                    if ($key === 'ktp' || $key === 'foto_ktp') {
                        $pemohon->update(['foto_ktp' => $path]);
                    } elseif ($key === 'kk' || $key === 'kartu_keluarga' || $key === 'foto_kk') {
                        $pemohon->update(['foto_kk' => $path]);
                    }
                } else {
                    $value = data_get($validated, "file_syarat.$key");
                    if (is_string($value) && !blank($value)) {
                        $storageUrl = asset('storage/');
                        if (str_starts_with($value, $storageUrl)) {
                            $value = substr($value, strlen($storageUrl));
                        }
                        $uploadedPaths[$key] = $value;
                    } else {
                        throw new \Exception("Dokumen $dokumen wajib diunggah.");
                    }
                }
            }

            $pengajuan = PengajuanSurat::create([
                'nik_pemohon' => $nikPemohon,
                'kategori_surat_id' => $kategori->id,
                'data_isian' => $validated['data_isian'],
                'file_syarat' => $uploadedPaths,
                'status' => 'Pending',
            ]);

            TrackingPengajuanSurat::create([
                'pengajuan_surat_id' => $pengajuan->id,
                'status_baru' => 'Pending',
                'keterangan_update' => 'Pengajuan surat dibuat',
            ]);
        });

        app(TelegramService::class)->notifyPengajuanStatus(
            $pengajuan->nik_pemohon, 'Pending', $pengajuan->nomor_registrasi
        );

        return redirect()
            ->route('warga.pengajuan.show', $pengajuan)
            ->with('success', 'Pengajuan surat berhasil dibuat.');
    }

    /**
     * Menampilkan detail tracking dan status pengajuan surat warga.
     *
     * Memeriksa otorisasi pengguna (pemohon langsung atau kepala keluarga
     * dari anggota yang mengajukan), lalu menampilkan detail pengajuan
     * beserta riwayat tracking status.
     *
     * @param  \Illuminate\Http\Request  $request  Request yang mengandung data warga terautentikasi
     * @param  \App\Models\PengajuanSurat  $pengajuan  Model pengajuan surat berdasarkan route parameter
     * @return \Inertia\Response  Halaman detail pengajuan surat dengan data tracking
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException  Jika pengguna tidak berwenang melihat pengajuan
     */
    public function show(Request $request, PengajuanSurat $pengajuan): Response
    {
        $warga = $request->user('penduduk');
        $keluarga = $warga->keluarga;
        $isKepalaKeluarga = $keluarga && $keluarga->kepala_keluarga_nik === $warga->nik;

        $canView = $pengajuan->nik_pemohon === $warga->nik;
        if (!$canView && $isKepalaKeluarga && $keluarga) {
            $canView = Penduduk::where('nik', $pengajuan->nik_pemohon)
                ->where('no_kk', $keluarga->no_kk)
                ->exists();
        }
        abort_unless($canView, 403);

        return Inertia::render('Citizen/Submission/Show', [
            'pengajuan' => $pengajuan->load(['kategori', 'pemohon.keluarga', 'tracking.updater']),
        ]);
    }

    /**
     * Menampilkan halaman cetak hasil surat yang telah disetujui (selesai).
     *
     * Memeriksa otorisasi pengguna dan status pengajuan (harus 'Selesai'),
     * mengambil data pengajuan beserta kategori dan pemohon, menghasilkan
     * QR Code verifikasi, serta mengambil pengaturan desa untuk
     * keperluan cetak surat resmi.
     *
     * @param  \Illuminate\Http\Request  $request  Request yang mengandung data warga terautentikasi
     * @param  \App\Models\PengajuanSurat  $pengajuan  Model pengajuan surat berdasarkan route parameter
     * @return \Inertia\Response  Halaman cetak surat dengan data pengajuan, QR Code, dan pengaturan desa
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException  Jika pengguna tidak berwenang melihat pengajuan
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException  Jika status pengajuan bukan 'Selesai'
     */
    public function print(Request $request, PengajuanSurat $pengajuan): Response
    {
        $warga = $request->user('penduduk');
        $keluarga = $warga->keluarga;
        $isKepalaKeluarga = $keluarga && $keluarga->kepala_keluarga_nik === $warga->nik;

        $canView = $pengajuan->nik_pemohon === $warga->nik;
        if (!$canView && $isKepalaKeluarga && $keluarga) {
            $canView = Penduduk::where('nik', $pengajuan->nik_pemohon)
                ->where('no_kk', $keluarga->no_kk)
                ->exists();
        }
        abort_unless($canView, 403);
        abort_unless($pengajuan->status === 'Selesai', 404, 'Surat belum selesai diproses.');

        $pengajuan->load(['kategori', 'pemohon.keluarga']);

        $verificationUrl = config('app.url') . '/verifikasi/' . ($pengajuan->qr_hash ?? 'invalid');
        $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->generate($verificationUrl)->toHtml();

        $rawNamaDesa = \App\Models\PengaturanDesa::get('nama_desa', 'Udeung');
        $cleanNamaDesa = preg_replace('/^(desa|desa)\s+/i', '', $rawNamaDesa);

        $namaDesa = $cleanNamaDesa;
        $kecamatan = \App\Models\PengaturanDesa::get('kecamatan', 'Bandar Baru');
        $kabupaten = \App\Models\PengaturanDesa::get('kabupaten', 'Pidie Jaya');
        $provinsi = \App\Models\PengaturanDesa::get('provinsi', 'Aceh');
        $kodePos = \App\Models\PengaturanDesa::get('kode_pos', '24186');
        $namaKepalaDesa = \App\Models\PengaturanDesa::get('nama_kepala_desa', 'Nama Kepala Desa');
        $nipKepalaDesa = \App\Models\PengaturanDesa::get('nip_kepala_desa', '');

        // ponytail: body_content per kategori surat — replace placeholder dari DB
        $bodyContent = $pengajuan->kategori?->body_content;
        if ($bodyContent) {
            $bodyContent = str_replace(
                ['{nama_desa}', '{kecamatan}', '{kabupaten}', '{provinsi}', '{kode_pos}', '{nama_kepala_desa}', '{nip_kepala_desa}'],
                [$namaDesa, $kecamatan, $kabupaten, $provinsi, $kodePos, $namaKepalaDesa, $nipKepalaDesa],
                $bodyContent
            );
        }

        return Inertia::render('Citizen/Submission/Print', [
            'pengajuan' => $pengajuan,
            'qrCodeSvg' => $qrCodeSvg,
            'bodyContent' => $bodyContent,
            'tanggalSurat' => $pengajuan->updated_at?->locale('id')->isoFormat('D MMMM YYYY') ?? now()->locale('id')->isoFormat('D MMMM YYYY'),
            'settings' => [
                'nama_desa' => $namaDesa,
                'kecamatan' => $kecamatan,
                'kabupaten' => $kabupaten,
                'provinsi' => $provinsi,
                'kode_pos' => $kodePos,
                'nama_kepala_desa' => $namaKepalaDesa,
                'nip_kepala_desa' => $nipKepalaDesa,
            ]
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\InformasiPublik;
use App\Models\InventarisFasilitas;
use App\Models\KategoriSurat;
use App\Models\PengajuanSurat;
use App\Services\StatistikService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller untuk halaman portal publik bagi pengunjung (guest).
 * Menangani rendering halaman beranda, profil desa, informasi publik,
 * verifikasi dokumen, statistik, fasilitas, dan aspirasi.
 */
class PublicPortalController extends Controller
{
    /**
     * Menampilkan halaman beranda dengan data demografi, layanan, berita terbaru, dan kategori surat.
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
     * Menampilkan halaman profil perangkat desa (Kepala Desa, Sekretaris, Operator).
     */
    public function profile(): Response
    {
        $getFotoUrl = function ($kunci, $fallbackKey = null) {
            $val = \App\Models\PengaturanFrontend::get($kunci);
            if (!$val && $fallbackKey) {
                $val = \App\Models\PengaturanDesa::get($fallbackKey);
            }
            if (empty($val)) {
                return "/images/default-avatar.png";
            }
            if (str_starts_with($val, "http://") || str_starts_with($val, "https://") || str_starts_with($val, "/images/")) {
                return $val;
            }
            return \Illuminate\Support\Facades\Storage::url($val);
        };

        return Inertia::render("Public/Profile", [
            "perangkat" => [
                [
                    "jabatan" => "Kepala Desa",
                    "nama" => \App\Models\PengaturanDesa::get("nama_kepala_desa", "Nama Kepala Desa"),
                    "foto" => $getFotoUrl("foto_kepala_desa", "foto_kepala_desa"),
                ],
                [
                    "jabatan" => "Sekretaris Desa",
                    "nama" => \App\Models\PengaturanFrontend::get("nama_sekdes") ?? \App\Models\PengaturanDesa::get("nama_sekdes", "Nama Sekretaris Desa"),
                    "foto" => $getFotoUrl("foto_sekdes", "foto_sekdes"),
                ],
                [
                    "jabatan" => "Operator Layanan",
                    "nama" => \App\Models\PengaturanFrontend::get("nama_operator") ?? \App\Models\PengaturanDesa::get("nama_operator", "Nama Operator"),
                    "foto" => $getFotoUrl("foto_operator", "foto_operator"),
                ],
            ],
        ]);
    }

    /**
     * Menampilkan daftar informasi publik dengan filter kategori dan pencarian.
     */
    public function information(): Response
    {
        $query = InformasiPublik::query()
            ->published()
            ->with("author:id,username")
            ->latest("created_at");

        $query->when(request("kategori"), function ($q, $kategori) {
            return $q->where("kategori", $kategori);
        });

$query->when(request("search"), function ($q, $search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], substr($search, 0, 100));
            return $q->where(function ($sub) use ($search) {
                $sub->where("judul", "like", "%" . $search . "%")
                    ->orWhere("konten", "like", "%" . $search . "%");
            });
        });

        return Inertia::render("Public/Information/Index", [
            "informasi" => $query->paginate(9)->withQueryString(),
            "kategori" => InformasiPublik::query()
                ->published()
                ->select("kategori")
                ->distinct()
                ->orderBy("kategori")
                ->pluck("kategori"),
            "filters" => request()->only(["kategori", "search"]),
        ]);
    }

    /**
     * Menampilkan detail informasi publik berdasarkan slug.
     */
    public function informationShow(string $slug): Response
    {
        return Inertia::render("Public/Information/Show", [
            "informasi" => InformasiPublik::query()
                ->published()
                ->with("author:id,username")
                ->where("slug", $slug)
                ->firstOrFail(),
        ]);
    }

    /**
     * Menampilkan halaman verifikasi dokumen (form input hash/kode QR).
     */
    public function verifyIndex(): Response
    {
        return Inertia::render("Public/Verify", [
            "result" => null
        ]);
    }

    /**
     * Memverifikasi dokumen surat berdasarkan QR hash dan menampilkan hasilnya.
     */
    public function verify(string $hash): Response
    {
        $pengajuan = PengajuanSurat::query()
            ->with(["kategori:id,nama_surat", "pemohon:nik,nama_lengkap", "verifikator:id,username"])
            ->where("qr_hash", $hash)
            ->first();

        return Inertia::render("Public/Verify", [
            "result" => $pengajuan && $pengajuan->status === "Selesai"
                ? [
                    "valid" => true,
                    "message" => "Dokumen terverifikasi.",
                    "nomor_registrasi" => $pengajuan->nomor_registrasi,
                    "jenis_surat" => $pengajuan->kategori?->nama_surat,
                    "nama_pemohon" => $pengajuan->pemohon?->nama_lengkap,
                    "nik_pemohon" => $pengajuan->nik_pemohon,
                    "tanggal_terbit" => $pengajuan->updated_at?->format("d-m-Y"),
                    "diverifikasi_oleh" => $pengajuan->verifikator?->username,
                ]
                : [
                    "valid" => false,
                    "message" => $pengajuan ? "Dokumen belum selesai diproses." : "Dokumen tidak ditemukan atau tidak valid.",
                ],
        ]);
    }

    /**
     * Menampilkan halaman statistik demografi dan layanan desa.
     */
    public function statistik(StatistikService $statistik): Response
    {
        return Inertia::render("Public/Statistik", [
            "demografi" => $statistik->getDemografi(),
            "layanan" => $statistik->getLayanan(),
        ]);
    }

    /**
     * Menampilkan daftar fasilitas desa yang bersifat publik.
     */
    public function fasilitas(): Response
    {
        $fasilitas = InventarisFasilitas::query()
            ->where("is_publik", true)
            ->orderBy("nama_fasilitas")
            ->get()
            ->map(function ($item) {
                return [
                    "id" => $item->id,
                    "nama_fasilitas" => $item->nama_fasilitas,
                    "jenis_fasilitas" => $item->jenis_fasilitas,
                    "deskripsi" => $item->deskripsi,
                    "lokasi" => $item->lokasi,
                    "kondisi" => $item->kondisi,
                    "tahun_dibangun" => $item->tahun_dibangun,
                    "foto" => $item->foto_url ?? $item->foto,
                    "latitude" => $item->latitude,
                    "longitude" => $item->longitude,
                    "status_penggunaan" => $item->status_penggunaan,
                ];
            });

        return Inertia::render("Public/Fasilitas", [
            "fasilitas" => $fasilitas,
        ]);
    }

    /**
     * Menampilkan detail fasilitas desa beserta daftar fasilitas terbaru lainnya.
     */
    public function fasilitasShow(InventarisFasilitas $fasilitas): Response
    {
        $fasilitasTerbaru = InventarisFasilitas::query()
            ->where("is_publik", true)
            ->where("id", "!=", $fasilitas->id)
            ->latest("created_at")
            ->limit(3)
            ->get()
            ->map(function ($item) {
                return [
                    "id" => $item->id,
                    "nama_fasilitas" => $item->nama_fasilitas,
                    "jenis_fasilitas" => $item->jenis_fasilitas,
                    "deskripsi" => $item->deskripsi,
                    "lokasi" => $item->lokasi,
                    "kondisi" => $item->kondisi,
                    "tahun_dibangun" => $item->tahun_dibangun,
                    "foto" => $item->foto_url ?? $item->foto,
                    "latitude" => $item->latitude,
                    "longitude" => $item->longitude,
                    "status_penggunaan" => $item->status_penggunaan,
                ];
            });

        return Inertia::render("Public/Fasilitas/Show", [
            "fasilitas" => [
                "id" => $fasilitas->id,
                "nama_fasilitas" => $fasilitas->nama_fasilitas,
                "jenis_fasilitas" => $fasilitas->jenis_fasilitas,
                "deskripsi" => $fasilitas->deskripsi,
                "lokasi" => $fasilitas->lokasi,
                "kondisi" => $fasilitas->kondisi,
                "tahun_dibangun" => $fasilitas->tahun_dibangun,
                "foto" => $fasilitas->foto_url ?? $fasilitas->foto,
                "latitude" => $fasilitas->latitude,
                "longitude" => $fasilitas->longitude,
                "status_penggunaan" => $fasilitas->status_penggunaan,
            ],
            "fasilitasTerbaru" => $fasilitasTerbaru,
        ]);
    }

    /**
     * Menyimpan aspirasi/pesan dari publik dan mencatatnya ke log sistem.
     */
    public function storeAspirasi(Request $request): RedirectResponse
    {
$request->validate([
            "pesan" => "required|string|max:1000",
        ]);

        if (!empty($request->_trap)) {
            return back()->with("success", "Aspirasi terkirim. Terima kasih!");
        }

        \App\Services\SystemLogger::log('aspirasi.kirim', 'Aspirasi dari publik', null, [
            'pesan' => $request->pesan,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with("success", "Aspirasi terkirim. Terima kasih!");
    }
}

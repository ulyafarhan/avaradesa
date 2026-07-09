<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSurat;
use App\Models\KategoriSurat;
use App\Models\TrackingPengajuanSurat;
use App\Models\AuditLog;
use App\Jobs\GenerateSuratPdfJob;
use App\Services\TelegramService;
use Illuminate\Http\Request;

/**
 * Controller untuk mengelola pengajuan surat pelayanan warga lewat API.
 *
 * @group Layanan Warga
 */
class PengajuanSuratController extends Controller
{
    /**
     * Menginjeksi dependensi TelegramService.
     */
    public function __construct(
        protected TelegramService $telegram
    ) {}

    /**
     * Mengambil daftar seluruh kategori surat yang aktif untuk diajukan.
     *
     * @authenticated
     *
     * @responseField data array Daftar kategori surat yang aktif.
     * @responseField data[].id int ID kategori surat.
     * @responseField data[].nama_surat string Nama jenis surat.
     * @responseField data[].deskripsi string Deskripsi singkat surat.
     *
     * @response {
     *   "data": [
     *     {
     *       "id": 1,
     *       "nama_surat": "Surat Keterangan Domisili",
     *       "deskripsi": "Surat keterangan tempat tinggal warga",
     *       "status": "active"
     *     }
     *   ]
     * }
     */
    public function kategori()
    {
        $kategori = KategoriSurat::active()->get();

        return response()->json([
            'data' => $kategori,
        ]);
    }

    /**
     * Mengambil spesifikasi skema data dan syarat dari satu kategori surat.
     *
     * @authenticated
     *
     * @urlParameter id int ID kategori surat. Contoh: 1.
     *
     * @responseField data object Detail kategori surat beserta skema data dan syarat.
     * @responseField data.id int ID kategori surat.
     * @responseField data.nama_surat string Nama jenis surat.
     * @responseField data.skema_data object Skema data isian yang diperlukan.
     * @responseField data.syarat array Daftar syarat dokumen yang harus dilampirkan.
     *
     * @response {
     *   "data": {
     *     "id": 1,
     *     "nama_surat": "Surat Keterangan Domisili",
     *     "deskripsi": "Surat keterangan tempat tinggal warga",
     *     "skema_data": {
     *       "alamat": { "type": "text", "label": "Alamat Lengkap" }
     *     },
     *     "syarat": ["Foto KTP", "Foto KK"]
     *   }
     * }
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika kategori surat tidak ditemukan.
     */
    public function detailKategori($id)
    {
        $kategori = KategoriSurat::findOrFail($id);

        return response()->json([
            'data' => $kategori,
        ]);
    }

    /**
     * Memproses pengiriman permohonan pengajuan surat pelayanan baru dari warga.
     *
     * @authenticated
     *
     * @bodyParameter kategori_surat_id int required ID kategori surat yang diajukan. Contoh: 1.
     * @bodyParameter data_isian array required Data isian formulir surat sesuai skema kategori. Contoh: {"alamat": "Jl. Merdeka No. 10"}.
     * @bodyParameter file_syarat array required Daftar path/URL file syarat yang dilampirkan. Contoh: ["uploads/ktp.jpg", "uploads/kk.jpg"].
     *
     * @responseField message string Pesan hasil operasi.
     * @responseField data object Data pengajuan surat yang berhasil dibuat.
     * @responseField data.id int ID pengajuan surat.
     * @responseField data.nomor_registrasi string Nomor registrasi unik pengajuan.
     * @responseField data.status string Status pengajuan awal ("Pending").
     * @responseField data.kategori object Informasi kategori surat terkait.
     *
     * @response 201 {
     *   "message": "Pengajuan surat berhasil dibuat",
     *   "data": {
     *     "id": 1,
     *     "nik_pemohon": "1118060512900001",
     *     "kategori_surat_id": 1,
     *     "nomor_registrasi": "REG/2026/0001",
     *     "status": "Pending",
     *     "data_isian": {"alamat": "Jl. Merdeka No. 10"},
     *     "file_syarat": ["uploads/ktp.jpg"],
     *     "kategori": {
     *       "id": 1,
     *       "nama_surat": "Surat Keterangan Domisili"
     *     }
     *   }
     * }
     *
     * @throws \Illuminate\Validation\ValidationException Jika validasi gagal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori_surat_id' => 'required|exists:kategori_surat,id',
            'data_isian' => 'required|array',
            'file_syarat' => 'nullable|array',
            'file_syarat.*' => 'file|mimes:jpg,jpeg,png,pdf,webp|max:2048',
        ]);

        $user = $request->user();
        
        $uploadedPaths = [];
        if ($request->hasFile('file_syarat')) {
            foreach ($request->file('file_syarat') as $file) {
                $uploadedPaths[] = $file->store('submissions/documents', 'public');
            }
        } elseif ($request->has('file_syarat') && is_array($request->file_syarat)) {
            // Fallback for string paths
            foreach ($request->file_syarat as $path) {
                if (is_string($path)) {
                    $uploadedPaths[] = $path;
                }
            }
        }

        $pengajuan = PengajuanSurat::create([
            'nik_pemohon' => $user->nik,
            'kategori_surat_id' => $request->kategori_surat_id,
            'data_isian' => $request->data_isian,
            'file_syarat' => $uploadedPaths,
            'status' => 'Pending',
        ]);

        TrackingPengajuanSurat::create([
            'pengajuan_surat_id' => $pengajuan->id,
            'status_baru' => 'Pending',
            'keterangan_update' => 'Pengajuan surat dibuat',
        ]);

        AuditLog::log('warga', $user->nik, 'create', 'pengajuan_surat', $pengajuan->id, null, $pengajuan->toArray());

        $this->telegram->notifyPengajuanStatus(
            $user->nik,
            'Pending',
            $pengajuan->nomor_registrasi
        );

        return response()->json([
            'message' => 'Pengajuan surat berhasil dibuat',
            'data' => $pengajuan->load('kategori'),
        ], 201);
    }

    /**
     * Mengambil daftar riwayat permohonan surat milik warga yang sedang login.
     *
     * @authenticated
     *
     * @responseField current_page int Halaman saat ini.
     * @responseField data array Daftar pengajuan surat milik warga.
     * @responseField data[].id int ID pengajuan surat.
     * @responseField data[].nomor_registrasi string Nomor registrasi pengajuan.
     * @responseField data[].status string Status pengajuan saat ini.
     * @responseField data[].kategori object Informasi kategori surat.
     * @responseField data[].tracking array Riwayat perubahan status pengajuan.
     * @responseField per_page int Jumlah item per halaman (10).
     * @responseField total int Total seluruh pengajuan.
     *
     * @response {
     *   "current_page": 1,
     *   "data": [
     *     {
     *       "id": 1,
     *       "nik_pemohon": "1118060512900001",
     *       "nomor_registrasi": "REG/2026/0001",
     *       "status": "Disetujui",
     *       "kategori": {
     *         "id": 1,
     *         "nama_surat": "Surat Keterangan Domisili"
     *       },
     *       "tracking": [
     *         {
     *           "status_baru": "Pending",
     *           "keterangan_update": "Pengajuan surat dibuat"
     *         }
     *       ]
     *     }
     *   ],
     *   "per_page": 10,
     *   "total": 1
     * }
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $pengajuan = PengajuanSurat::where('nik_pemohon', $user->nik)
            ->with(['kategori', 'tracking'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($pengajuan);
    }

    /**
     * Mengambil detail dari permohonan surat tertentu beserta riwayat tracking-nya.
     *
     * @authenticated
     *
     * @urlParameter id int ID pengajuan surat. Contoh: 1.
     *
     * @responseField data object Detail lengkap pengajuan surat.
     * @responseField data.id int ID pengajuan surat.
     * @responseField data.nomor_registrasi string Nomor registrasi pengajuan.
     * @responseField data.status string Status pengajuan saat ini.
     * @responseField data.pemohon object Data pemohon (warga).
     * @responseField data.kategori object Informasi kategori surat.
     * @responseField data.tracking array Riwayat perubahan status beserta info pengupdate.
     *
     * @response {
     *   "data": {
     *     "id": 1,
     *     "nik_pemohon": "1118060512900001",
     *     "nomor_registrasi": "REG/2026/0001",
     *     "status": "Disetujui",
     *     "pemohon": {
     *       "nik": "1118060512900001",
     *       "nama_lengkap": "Ahmad Fauzi"
     *     },
     *     "kategori": {
     *       "id": 1,
     *       "nama_surat": "Surat Keterangan Domisili"
     *     },
     *     "tracking": [
     *       {
     *         "status_sebelumnya": null,
     *         "status_baru": "Pending",
     *         "keterangan_update": "Pengajuan surat dibuat",
     *         "diupdate_oleh": null
     *       }
     *     ]
     *   }
     * }
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika pengajuan surat tidak ditemukan.
     */
    public function show($id)
    {
        $pengajuan = PengajuanSurat::with(['kategori', 'pemohon', 'tracking.updater'])
            ->findOrFail($id);

        $data = $pengajuan->toArray();

        if (in_array($pengajuan->status, ['Disetujui', 'Selesai'])) {
            $service = app(\App\Services\PdfGeneratorService::class);
            $data['surat_html'] = $service->renderHtml($pengajuan);
        }

        return response()->json([
            'data' => $data,
        ]);
    }

    /**
     * Mengambil data seluruh pengajuan surat untuk kebutuhan panel admin desa.
     *
     * @group Administrasi
     * @subgroup Pengajuan Surat
     * @authenticated
     *
     * @queryParameter status string Filter berdasarkan status pengajuan. Nilai: Pending, Disetujui, Ditolak, Selesai.
     *
     * @responseField current_page int Halaman saat ini.
     * @responseField data array Daftar pengajuan surat untuk admin.
     * @responseField data[].id int ID pengajuan surat.
     * @responseField data[].nomor_registrasi string Nomor registrasi pengajuan.
     * @responseField data[].status string Status pengajuan.
     * @responseField data[].kategori object Informasi kategori surat.
     * @responseField data[].pemohon object Data pemohon.
     * @responseField per_page int Jumlah item per halaman (20).
     *
     * @response {
     *   "current_page": 1,
     *   "data": [
     *     {
     *       "id": 1,
     *       "nik_pemohon": "1118060512900001",
     *       "nomor_registrasi": "REG/2026/0001",
     *       "status": "Pending",
     *       "kategori": {
     *         "id": 1,
     *         "nama_surat": "Surat Keterangan Domisili"
     *       },
     *       "pemohon": {
     *         "nik": "1118060512900001",
     *         "nama_lengkap": "Ahmad Fauzi"
     *       }
     *     }
     *   ],
     *   "per_page": 20
     * }
     */
    public function adminIndex(Request $request)
    {
        $query = PengajuanSurat::with(['kategori', 'pemohon']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $pengajuan = $query->orderByDesc('created_at')->paginate(20);

        return response()->json($pengajuan);
    }

    /**
     * Menyetujui pengajuan surat dan memicu proses tanda tangan digital PDF otomatis.
     *
     * @group Administrasi
     * @subgroup Pengajuan Surat
     * @authenticated
     *
     * @urlParameter id int ID pengajuan surat yang akan disetujui. Contoh: 1.
     *
     * @responseField message string Pesan hasil operasi.
     * @responseField data object Data pengajuan surat yang telah disetujui.
     * @responseField data.status string Status baru ("Disetujui").
     * @responseField data.diverifikasi_oleh int ID admin yang menyetujui.
     *
     * @response {
     *   "message": "Pengajuan berhasil disetujui",
     *   "data": {
     *     "id": 1,
     *     "nomor_registrasi": "REG/2026/0001",
     *     "status": "Disetujui",
     *     "diverifikasi_oleh": 1,
     *     "nik_pemohon": "1118060512900001"
     *   }
     * }
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika pengajuan surat tidak ditemukan.
     */
    public function approve(Request $request, $id)
    {
        $pengajuan = PengajuanSurat::findOrFail($id);
        $admin = $request->user();

        $oldStatus = $pengajuan->status;
        
        $pengajuan->update([
            'status' => 'Disetujui',
            'diverifikasi_oleh' => $admin->id,
        ]);

        TrackingPengajuanSurat::create([
            'pengajuan_surat_id' => $pengajuan->id,
            'status_sebelumnya' => $oldStatus,
            'status_baru' => 'Disetujui',
            'keterangan_update' => 'Pengajuan disetujui oleh ' . $admin->username,
            'diupdate_oleh' => $admin->id,
        ]);

        AuditLog::log('admin', $admin->id, 'approve', 'pengajuan_surat', $pengajuan->id);

        if (app()->runningUnitTests()) {
            GenerateSuratPdfJob::dispatch($pengajuan);
        } else {
            GenerateSuratPdfJob::dispatchSync($pengajuan);
        }

        $this->telegram->notifyPengajuanStatus(
            $pengajuan->nik_pemohon,
            'Disetujui',
            $pengajuan->nomor_registrasi
        );

        return response()->json([
            'message' => 'Pengajuan berhasil disetujui',
            'data' => $pengajuan,
        ]);
    }

    /**
     * Menolak pengajuan permohonan surat warga dengan menyertakan catatan penolakan.
     *
     * @group Administrasi
     * @subgroup Pengajuan Surat
     * @authenticated
     *
     * @urlParameter id int ID pengajuan surat yang akan ditolak. Contoh: 1.
     * @bodyParameter catatan_penolakan string required Alasan penolakan pengajuan surat. Contoh: Berkas tidak lengkap.
     *
     * @responseField message string Pesan hasil operasi.
     * @responseField data object Data pengajuan surat yang telah ditolak.
     * @responseField data.status string Status baru ("Ditolak").
     * @responseField data.catatan_penolakan string Catatan alasan penolakan.
     *
     * @response {
     *   "message": "Pengajuan ditolak",
     *   "data": {
     *     "id": 1,
     *     "nomor_registrasi": "REG/2026/0001",
     *     "status": "Ditolak",
     *     "catatan_penolakan": "Berkas tidak lengkap",
     *     "diverifikasi_oleh": 1
     *   }
     * }
     *
     * @throws \Illuminate\Validation\ValidationException Jika catatan_penolakan tidak disertakan.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika pengajuan surat tidak ditemukan.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan_penolakan' => 'required|string',
        ]);

        $pengajuan = PengajuanSurat::findOrFail($id);
        $admin = $request->user();

        $oldStatus = $pengajuan->status;
        
        $pengajuan->update([
            'status' => 'Ditolak',
            'catatan_penolakan' => $request->catatan_penolakan,
            'diverifikasi_oleh' => $admin->id,
        ]);

        TrackingPengajuanSurat::create([
            'pengajuan_surat_id' => $pengajuan->id,
            'status_sebelumnya' => $oldStatus,
            'status_baru' => 'Ditolak',
            'keterangan_update' => $request->catatan_penolakan,
            'diupdate_oleh' => $admin->id,
        ]);

        AuditLog::log('admin', $admin->id, 'reject', 'pengajuan_surat', $pengajuan->id);

        $this->telegram->notifyPengajuanStatus(
            $pengajuan->nik_pemohon,
            'Ditolak',
            $pengajuan->nomor_registrasi,
            $request->catatan_penolakan
        );

        return response()->json([
            'message' => 'Pengajuan ditolak',
            'data' => $pengajuan,
        ]);
    }

    /**
     * Mengunduh / mencetak berkas PDF dari surat yang telah disetujui.
     */
    public function downloadPdf($id)
    {
        $pengajuan = PengajuanSurat::with(['kategori', 'pemohon'])->findOrFail($id);

        $service = app(\App\Services\PdfGeneratorService::class);

        if (!$pengajuan->file_pdf_url) {
            $service->generateSuratPdf($pengajuan);
            $pengajuan->refresh();
        }

        $filename = basename(parse_url($pengajuan->file_pdf_url, PHP_URL_PATH) ?? $pengajuan->file_pdf_url);
        $relativePath = 'surat/' . $filename;

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($relativePath)) {
            $service->generateSuratPdf($pengajuan);
            $pengajuan->refresh();
            $filename = basename(parse_url($pengajuan->file_pdf_url, PHP_URL_PATH) ?? $pengajuan->file_pdf_url);
            $relativePath = 'surat/' . $filename;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->download(
            $relativePath,
            "Surat_" . str_replace('/', '_', $pengajuan->nomor_registrasi) . ".pdf"
        );
    }
}

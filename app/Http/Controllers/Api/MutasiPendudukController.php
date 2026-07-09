<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MutasiPenduduk;
use App\Models\Penduduk;
use App\Models\AuditLog;
use App\Services\TelegramService;
use App\Services\StatistikService;
use Illuminate\Http\Request;

/**
 * Controller untuk menangani pendaftaran, pengambilan, dan verifikasi riwayat mutasi penduduk lewat API.
 *
 * @group Layanan Warga
 */
class MutasiPendudukController extends Controller
{
    /**
     * Menginjeksi dependensi TelegramService dan StatistikService.
     */
    public function __construct(
        protected TelegramService $telegram,
        protected StatistikService $statistik
    ) {}

    /**
     * Memproses pengajuan mutasi penduduk baru (kelahiran/kematian/kedatangan/kepindahan).
     *
     * @authenticated
     *
     * @bodyParameter nik string required NIK penduduk yang mengajukan mutasi (16 digit). Contoh: 1118060512900001.
     * @bodyParameter jenis_mutasi string required Jenis mutasi penduduk. Nilai: Kelahiran, Kematian, Kedatangan, Kepindahan.
     * @bodyParameter tanggal_mutasi string required Tanggal kejadian mutasi (format: Y-m-d). Contoh: 2026-07-10.
     * @bodyParameter keterangan string required Keterangan detail mutasi. Contoh: Pindah domisili ke Kota Banda Aceh.
     * @bodyParameter dokumen_bukti string required Path/URL dokumen bukti mutasi. Contoh: documents/surat_pindah.pdf.
     *
     * @responseField message string Pesan hasil operasi.
     * @responseField data object Data pengajuan mutasi yang berhasil dibuat.
     * @responseField data.id int ID pengajuan mutasi.
     * @responseField data.nik string NIK penduduk.
     * @responseField data.jenis_mutasi string Jenis mutasi yang diajukan.
     * @responseField data.status_verifikasi string Status verifikasi awal ("Pending").
     *
     * @response 201 {
     *   "message": "Pengajuan mutasi berhasil dibuat",
     *   "data": {
     *     "id": 1,
     *     "nik": "1118060512900001",
     *     "jenis_mutasi": "Kepindahan",
     *     "tanggal_mutasi": "2026-07-10",
     *     "keterangan": "Pindah domisili ke Kota Banda Aceh",
     *     "dokumen_bukti": "documents/surat_pindah.pdf",
     *     "status_verifikasi": "Pending"
     *   }
     * }
     *
     * @throws \Illuminate\Validation\ValidationException Jika validasi gagal atau NIK tidak ditemukan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|exists:penduduk,nik',
            'jenis_mutasi' => 'required|in:Kelahiran,Kematian,Kedatangan,Kepindahan',
            'tanggal_mutasi' => 'required|date',
            'keterangan' => 'required|string',
            'dokumen_bukti' => 'required|string',
        ]);

        $user = $request->user();

        $mutasi = MutasiPenduduk::create([
            'nik' => $request->nik,
            'jenis_mutasi' => $request->jenis_mutasi,
            'tanggal_mutasi' => $request->tanggal_mutasi,
            'keterangan' => $request->keterangan,
            'dokumen_bukti' => $request->dokumen_bukti,
            'status_verifikasi' => 'Pending',
        ]);

        AuditLog::log('warga', $user->nik, 'create', 'mutasi_penduduk', $mutasi->id, null, $mutasi->toArray());

        $this->telegram->notifyMutasiStatus(
            $request->nik,
            $request->jenis_mutasi,
            'Pending'
        );

        return response()->json([
            'message' => 'Pengajuan mutasi berhasil dibuat',
            'data' => $mutasi,
        ], 201);
    }

    /**
     * Mengambil daftar riwayat mutasi milik warga yang sedang login.
     *
     * @authenticated
     *
     * @responseField current_page int Halaman saat ini.
     * @responseField data array Daftar pengajuan mutasi milik warga.
     * @responseField data[].id int ID pengajuan mutasi.
     * @responseField data[].nik string NIK penduduk.
     * @responseField data[].jenis_mutasi string Jenis mutasi.
     * @responseField data[].status_verifikasi string Status verifikasi saat ini.
     * @responseField data[].penduduk object Data penduduk terkait.
     * @responseField per_page int Jumlah item per halaman (10).
     *
     * @response {
     *   "current_page": 1,
     *   "data": [
     *     {
     *       "id": 1,
     *       "nik": "1118060512900001",
     *       "jenis_mutasi": "Kepindahan",
     *       "tanggal_mutasi": "2026-07-10",
     *       "status_verifikasi": "Pending",
     *       "penduduk": {
     *         "nik": "1118060512900001",
     *         "nama_lengkap": "Ahmad Fauzi"
     *       }
     *     }
     *   ],
     *   "per_page": 10
     * }
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $mutasi = MutasiPenduduk::where('nik', '=', $user->nik)
            ->with('penduduk')
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($mutasi);
    }

    /**
     * Mengambil seluruh data mutasi penduduk untuk panel admin desa.
     *
     * @group Administrasi
     * @subgroup Mutasi Penduduk
     * @authenticated
     *
     * @queryParameter status_verifikasi string Filter berdasarkan status verifikasi. Nilai: Pending, Disetujui, Ditolak.
     * @queryParameter jenis_mutasi string Filter berdasarkan jenis mutasi. Nilai: Kelahiran, Kematian, Kedatangan, Kepindahan.
     *
     * @responseField current_page int Halaman saat ini.
     * @responseField data array Daftar pengajuan mutasi untuk admin.
     * @responseField data[].id int ID pengajuan mutasi.
     * @responseField data[].nik string NIK penduduk.
     * @responseField data[].jenis_mutasi string Jenis mutasi.
     * @responseField data[].status_verifikasi string Status verifikasi.
     * @responseField data[].penduduk object Data penduduk terkait.
     * @responseField data[].verifikator object Data admin verifikator (jika sudah diverifikasi).
     * @responseField per_page int Jumlah item per halaman (20).
     *
     * @response {
     *   "current_page": 1,
     *   "data": [
     *     {
     *       "id": 1,
     *       "nik": "1118060512900001",
     *       "jenis_mutasi": "Kepindahan",
     *       "tanggal_mutasi": "2026-07-10",
     *       "status_verifikasi": "Pending",
     *       "penduduk": {
     *         "nik": "1118060512900001",
     *         "nama_lengkap": "Ahmad Fauzi"
     *       },
     *       "verifikator": null
     *     }
     *   ],
     *   "per_page": 20
     * }
     */
    public function adminIndex(Request $request)
    {
        $query = MutasiPenduduk::with(['penduduk', 'verifikator']);

        if ($request->has('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        if ($request->has('jenis_mutasi')) {
            $query->where('jenis_mutasi', $request->jenis_mutasi);
        }

        $mutasi = $query->orderByDesc('created_at')->paginate(20);

        return response()->json($mutasi);
    }

    /**
     * Menyetujui pengajuan mutasi dan memperbarui status aktif/pasif kependudukan warga terkait.
     *
     * @group Administrasi
     * @subgroup Mutasi Penduduk
     * @authenticated
     *
     * @urlParameter id int ID pengajuan mutasi yang akan disetujui. Contoh: 1.
     *
     * @responseField message string Pesan hasil operasi.
     * @responseField data object Data pengajuan mutasi yang telah disetujui.
     * @responseField data.status_verifikasi string Status verifikasi baru ("Disetujui").
     * @responseField data.diverifikasi_oleh int ID admin yang menyetujui.
     *
     * @response {
     *   "message": "Mutasi berhasil disetujui",
     *   "data": {
     *     "id": 1,
     *     "nik": "1118060512900001",
     *     "jenis_mutasi": "Kepindahan",
     *     "status_verifikasi": "Disetujui",
     *     "diverifikasi_oleh": 1
     *   }
     * }
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika pengajuan mutasi tidak ditemukan.
     */
    public function approve(Request $request, $id)
    {
        $mutasi = MutasiPenduduk::findOrFail($id);
        $admin = $request->user();

        $mutasi->update([
            'status_verifikasi' => 'Disetujui',
            'diverifikasi_oleh' => $admin->id,
        ]);

        $penduduk = Penduduk::find($mutasi->nik);
        
        if (in_array($mutasi->jenis_mutasi, ['Kematian', 'Kepindahan'])) {
            $penduduk->update([
                'status_mutasi' => $mutasi->jenis_mutasi === 'Kematian' ? 'Meninggal' : 'Pindah',
            ]);
        }

        AuditLog::log('admin', $admin->id, 'approve', 'mutasi_penduduk', $mutasi->id);

        $this->statistik->clearCache();

        $this->telegram->notifyMutasiStatus(
            $mutasi->nik,
            $mutasi->jenis_mutasi,
            'Disetujui'
        );

        return response()->json([
            'message' => 'Mutasi berhasil disetujui',
            'data' => $mutasi,
        ]);
    }

    /**
     * Menolak pengajuan mutasi penduduk.
     *
     * @group Administrasi
     * @subgroup Mutasi Penduduk
     * @authenticated
     *
     * @urlParameter id int ID pengajuan mutasi yang akan ditolak. Contoh: 1.
     *
     * @responseField message string Pesan hasil operasi.
     * @responseField data object Data pengajuan mutasi yang telah ditolak.
     * @responseField data.status_verifikasi string Status verifikasi baru ("Ditolak").
     * @responseField data.diverifikasi_oleh int ID admin yang menolak.
     *
     * @response {
     *   "message": "Mutasi ditolak",
     *   "data": {
     *     "id": 1,
     *     "nik": "1118060512900001",
     *     "jenis_mutasi": "Kepindahan",
     *     "status_verifikasi": "Ditolak",
     *     "diverifikasi_oleh": 1
     *   }
     * }
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika pengajuan mutasi tidak ditemukan.
     */
    public function reject(Request $request, $id)
    {
        $mutasi = MutasiPenduduk::findOrFail($id);
        $admin = $request->user();

        $mutasi->update([
            'status_verifikasi' => 'Ditolak',
            'diverifikasi_oleh' => $admin->id,
        ]);

        AuditLog::log('admin', $admin->id, 'reject', 'mutasi_penduduk', $mutasi->id);

        $this->telegram->notifyMutasiStatus(
            $mutasi->nik,
            $mutasi->jenis_mutasi,
            'Ditolak'
        );

        return response()->json([
            'message' => 'Mutasi ditolak',
            'data' => $mutasi,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSurat;

/**
 * Controller untuk menangani validasi keaslian dokumen surat melalui API.
 *
 * @group Statistik & Verifikasi
 */
class VerifikasiController extends Controller
{
    /**
     * Memverifikasi tanda tangan digital surat berdasarkan hash QR Code.
     *
     * @unauthenticated
     *
     * @urlParameter hash string Hash QR Code unik yang terdapat pada dokumen surat. Contoh: a1b2c3d4e5f6.
     *
     * @responseField valid boolean Status validitas dokumen.
     * @responseField message string Pesan hasil verifikasi.
     * @responseField data object Detail dokumen (hanya jika valid).
     * @responseField data.nomor_registrasi string Nomor registrasi pengajuan surat.
     * @responseField data.jenis_surat string Nama jenis surat.
     * @responseField data.nama_pemohon string Nama lengkap pemohon surat.
     * @responseField data.nik_pemohon string NIK pemohon surat.
     * @responseField data.tanggal_terbit string Tanggal terbit surat (format: d-m-Y).
     * @responseField data.diverifikasi_oleh string Username admin yang memverifikasi.
     *
     * @response {
     *   "valid": true,
     *   "message": "Dokumen valid",
     *   "data": {
     *     "nomor_registrasi": "REG/2026/0001",
     *     "jenis_surat": "Surat Keterangan Domisili",
     *     "nama_pemohon": "Ahmad Fauzi",
     *     "nik_pemohon": "1118060512900001",
     *     "tanggal_terbit": "10-07-2026",
     *     "diverifikasi_oleh": "admin_desa"
     *   }
     * }
     *
     * @response 404 {
     *   "valid": false,
     *   "message": "Dokumen tidak ditemukan atau tidak valid"
     * }
     *
     * @response 404 {
     *   "valid": false,
     *   "message": "Dokumen belum selesai diproses"
     * }
     */
    public function verify($hash)
    {
        $pengajuan = PengajuanSurat::where('qr_hash', $hash)
            ->with(['kategori', 'pemohon', 'verifikator'])
            ->first();

        if (!$pengajuan) {
            return response()->json([
                'valid' => false,
                'message' => 'Dokumen tidak ditemukan atau tidak valid',
            ], 404);
        }

        if ($pengajuan->status !== 'Selesai') {
            return response()->json([
                'valid' => false,
                'message' => 'Dokumen belum selesai diproses',
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Dokumen valid',
            'data' => [
                'nomor_registrasi' => $pengajuan->nomor_registrasi,
                'jenis_surat' => $pengajuan->kategori->nama_surat,
                'nama_pemohon' => $pengajuan->pemohon->nama_lengkap,
                'nik_pemohon' => $pengajuan->pemohon->nik,
                'tanggal_terbit' => $pengajuan->updated_at->format('d-m-Y'),
                'diverifikasi_oleh' => $pengajuan->verifikator?->username,
            ],
        ]);
    }
}

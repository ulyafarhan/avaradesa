<?php

namespace App\Services;

use App\Models\PengajuanSurat;
use App\Models\TrackingPengajuanSurat;
use App\Models\AuditLog;
use App\Jobs\GenerateSuratPdfJob;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class PengajuanSuratService
{
    public function __construct(
        protected TelegramService $telegram,
        protected WhatsAppService $whatsapp,
        protected PdfGeneratorService $pdfService
    ) {}

    /**
     * Menyetujui pengajuan surat.
     */
    public function approve(PengajuanSurat $pengajuan, $admin): void
    {
        if ($pengajuan->status !== 'Pending') {
            throw ValidationException::withMessages([
                'status' => ["Tidak dapat menyetujui pengajuan karena status saat ini adalah {$pengajuan->status}."]
            ]);
        }

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

        GenerateSuratPdfJob::dispatch($pengajuan);

        $this->telegram->notifyPengajuanStatus(
            $pengajuan->nik_pemohon,
            'Disetujui',
            $pengajuan->nomor_registrasi
        );
        $this->whatsapp->notifyPengajuanStatus(
            $pengajuan->nik_pemohon,
            'Disetujui',
            $pengajuan->nomor_registrasi
        );
    }

    /**
     * Menolak pengajuan surat.
     */
    public function reject(PengajuanSurat $pengajuan, $admin, string $catatan): void
    {
        if ($pengajuan->status !== 'Pending') {
            throw ValidationException::withMessages([
                'status' => ["Tidak dapat menolak pengajuan karena status saat ini adalah {$pengajuan->status}."]
            ]);
        }

        $oldStatus = $pengajuan->status;
        
        $pengajuan->update([
            'status' => 'Ditolak',
            'catatan_penolakan' => $catatan,
            'diverifikasi_oleh' => $admin->id,
        ]);

        TrackingPengajuanSurat::create([
            'pengajuan_surat_id' => $pengajuan->id,
            'status_sebelumnya' => $oldStatus,
            'status_baru' => 'Ditolak',
            'keterangan_update' => $catatan,
            'diupdate_oleh' => $admin->id,
        ]);

        AuditLog::log('admin', $admin->id, 'reject', 'pengajuan_surat', $pengajuan->id);

        $this->telegram->notifyPengajuanStatus(
            $pengajuan->nik_pemohon,
            'Ditolak',
            $pengajuan->nomor_registrasi,
            $catatan
        );
        $this->whatsapp->notifyPengajuanStatus(
            $pengajuan->nik_pemohon,
            'Ditolak',
            $pengajuan->nomor_registrasi,
            $catatan
        );
    }

    /**
     * Memastikan PDF surat di-generate dan mengembalikan path filenya.
     */
    public function getPdfPath(PengajuanSurat $pengajuan): string
    {
        if (!$pengajuan->file_pdf_url) {
            $this->pdfService->generateSuratPdf($pengajuan);
            $pengajuan->refresh();
        }

        $filename = basename(parse_url($pengajuan->file_pdf_url, PHP_URL_PATH) ?? $pengajuan->file_pdf_url);
        $relativePath = 'surat/' . $filename;

        if (!Storage::disk('public')->exists($relativePath)) {
            $this->pdfService->generateSuratPdf($pengajuan);
            $pengajuan->refresh();
            $filename = basename(parse_url($pengajuan->file_pdf_url, PHP_URL_PATH) ?? $pengajuan->file_pdf_url);
            $relativePath = 'surat/' . $filename;
        }

        return $relativePath;
    }
}

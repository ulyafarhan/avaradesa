<?php

namespace App\Jobs;

use App\Models\PengajuanSurat;
use App\Models\TrackingPengajuanSurat;
use App\Services\PdfGeneratorService;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job untuk memproses pembuatan tanda tangan digital (hash QR) dan pembaruan status surat menjadi Selesai.
 */
class GenerateSuratPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Inisialisasi Job dengan objek pengajuan surat.
     *
     * @param  \App\Models\PengajuanSurat  $pengajuan  Model pengajuan surat yang akan diproses
     */
    public function __construct(
        public PengajuanSurat $pengajuan
    ) {}

    /**
     * Mengeksekusi pembuatan tanda tangan digital dan notifikasi ke warga via Telegram.
     *
     * Proses yang dilakukan:
     * 1. Membuat QR hash jika belum ada
     * 2. Menghasilkan nomor surat dengan format Romawi
     * 3. Update status pengajuan ke 'Selesai'
     * 4. Mencatat tracking perubahan status
     * 5. Mengirim notifikasi ke warga via Telegram
     *
     * @param  \App\Services\PdfGeneratorService  $pdfService  Layanan pembuatan PDF
     * @param  \App\Services\TelegramService  $telegram  Layanan Telegram untuk notifikasi
     * @return void
     */
    public function handle(
        PdfGeneratorService $pdfService,
        TelegramService $telegram
    ): void {
        try {
            if (empty($this->pengajuan->qr_hash)) {
                $qrHash = hash('sha256', implode('|', [
                    $this->pengajuan->nomor_registrasi,
                    $this->pengajuan->nik_pemohon,
                    $this->pengajuan->kategori->kode_surat ?? 'SRT',
                    $this->pengajuan->created_at?->timestamp ?? time(),
                ]));
                $this->pengajuan->update(['qr_hash' => $qrHash]);
            }

            $printUrl = "/warga/pengajuan/{$this->pengajuan->id}/print";

            if (empty($this->pengajuan->nomor_surat)) {
                $counter = PengajuanSurat::where('kategori_surat_id', $this->pengajuan->kategori_surat_id)
                    ->whereNotNull('nomor_surat')
                    ->whereYear('created_at', date('Y'))
                    ->count() + 1;
                
                $romawiBulan = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
                $bulan = $romawiBulan[date('n')];

                $nomorSurat = sprintf(
                    '%s/%03d/%s/%s',
                    $this->pengajuan->kategori->kode_surat ?? 'SRT',
                    $counter,
                    $bulan,
                    date('Y')
                );
            } else {
                $nomorSurat = $this->pengajuan->nomor_surat;
            }

            $this->pengajuan->update([
                'status' => 'Selesai',
                'file_pdf_url' => $printUrl,
                'nomor_surat' => $nomorSurat
            ]);
            
            TrackingPengajuanSurat::create([
                'pengajuan_surat_id' => $this->pengajuan->id,
                'status_sebelumnya' => 'Disetujui',
                'status_baru' => 'Selesai',
                'keterangan_update' => 'Surat selesai diproses dan siap dicetak',
            ]);

            $pemohon = $this->pengajuan->pemohon;
            
            if ($pemohon && $pemohon->telegram_chat_id) {
                $message = "<b>Surat Anda telah selesai!</b>\n\n";
                $message .= "Nomor: <code>{$this->pengajuan->nomor_registrasi}</code>\n";
                $message .= "Jenis: {$this->pengajuan->kategori->nama_surat}\n\n";
                $message .= "Anda dapat melihat dan mengunduh/mencetak surat di:\n";
                $message .= config('app.url') . "/warga/dashboard?tab=pengajuan";

                $telegram->sendMessage(
                    $pemohon->telegram_chat_id,
                    $message
                );
            }

            $telegram->notifyPengajuanStatus(
                $this->pengajuan->nik_pemohon,
                'Selesai',
                $this->pengajuan->nomor_registrasi
            );

            Log::info("Letter processed successfully for pengajuan #{$this->pengajuan->id}");

        } catch (\Exception $e) {
            Log::error("Failed to process letter for pengajuan #{$this->pengajuan->id}: " . $e->getMessage());

            throw $e;
        }
    }

    /**
     * Dipanggil saat job gagal diproses.
     *
     * Update status pengajuan ke 'Ditolak' dan mencatat pesan error sebagai catatan penolakan.
     *
     * @param  \Throwable  $e  Exception yang menyebabkan kegagalan
     * @return void
     */
    public function failed(\Throwable $e): void
    {
        $this->pengajuan->update([
            'status' => 'Ditolak',
            'catatan_penolakan' => 'Gagal memproses surat: ' . $e->getMessage(),
        ]);
    }
}

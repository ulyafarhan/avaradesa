<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Penduduk;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job untuk mengirim notifikasi perubahan status pengajuan surat ke warga via WhatsApp.
 *
 * Dikirim saat admin menyetujui, menolak, atau memperbarui status pengajuan surat.
 * Nomor HP warga diambil dari kolom no_hp tabel penduduk.
 */
class SendStatusWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah maksimum percobaan ulang jika pengiriman gagal.
     */
    public int $tries = 3;

    /**
     * Inisialisasi Job dengan data pengajuan surat.
     *
     * @param  string       $nik              NIK pemohon surat
     * @param  string       $jenis            Jenis/nama surat yang diajukan
     * @param  string       $status           Status baru pengajuan (Disetujui/Ditolak/Diproses/Selesai)
     * @param  string       $nomorRegistrasi  Nomor registrasi pengajuan surat
     * @param  string|null  $catatan          Catatan dari admin (contoh: alasan penolakan)
     */
    public function __construct(
        public string $nik,
        public string $jenis,
        public string $status,
        public string $nomorRegistrasi,
        public ?string $catatan = null
    ) {}

    /**
     * Mengeksekusi pengiriman notifikasi status ke nomor WhatsApp warga.
     *
     * @param  WhatsAppService  $whatsapp  Layanan WhatsApp gateway
     * @return void
     */
    public function handle(WhatsAppService $whatsapp): void
    {
        $penduduk = Penduduk::find($this->nik);

        if (!$penduduk || empty($penduduk->no_hp)) {
            Log::info("Notifikasi WA surat dilewati: warga NIK {$this->nik} tidak memiliki no_hp terdaftar.");
            return;
        }

        $statusLabels = [
            'Pending'   => '⏳ Menunggu Verifikasi',
            'Diproses'  => '🔄 Sedang Diproses',
            'Disetujui' => '✅ Disetujui',
            'Ditolak'   => '❌ Ditolak',
            'Selesai'   => '📄 Selesai — Siap Diunduh',
        ];

        $statusLabel = $statusLabels[$this->status] ?? $this->status;

        $message  = "*STATUS PENGAJUAN SURAT*\n\n";
        $message .= "Yth. *{$penduduk->nama_lengkap}*,\n\n";
        $message .= "Pengajuan surat Anda telah diperbarui:\n";
        $message .= "Jenis : {$this->jenis}\n";
        $message .= "Nomor : `{$this->nomorRegistrasi}`\n";
        $message .= "Status: *{$statusLabel}*\n";

        if ($this->catatan) {
            $message .= "\nCatatan:\n_{$this->catatan}_\n";
        }

        if ($this->status === 'Disetujui' || $this->status === 'Selesai') {
            $message .= "\nSilakan login ke aplikasi untuk mengunduh surat Anda.";
        }

        $message .= "\n\n" . config('app.url');

        $whatsapp->sendMessage($penduduk->no_hp, $message);
    }
}

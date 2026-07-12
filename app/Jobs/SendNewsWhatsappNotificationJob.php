<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\InformasiPublik;
use App\Models\PengaturanDesa;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Job untuk mengirim notifikasi berita/pengumuman baru ke nomor WhatsApp default desa.
 *
 * Dieksekusi secara asinkronus saat artikel informasi publik dipublikasikan.
 * Target pengiriman dikonfigurasi dari database pengaturan_desa (wa_default_target).
 */
class SendNewsWhatsappNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah maksimum percobaan ulang jika pengiriman gagal.
     */
    public int $tries = 3;

    /**
     * Inisialisasi Job dengan ID informasi publik.
     *
     * @param  string  $informasiId  ID dari berita/pengumuman (format ULID)
     */
    public function __construct(
        public string $informasiId
    ) {}

    /**
     * Mengeksekusi pengiriman pesan notifikasi berita ke nomor WhatsApp default.
     *
     * @param  WhatsAppService  $whatsapp  Layanan WhatsApp gateway
     * @return void
     */
    public function handle(WhatsAppService $whatsapp): void
    {
        $informasi = InformasiPublik::find($this->informasiId);
        if (!$informasi || !$informasi->is_published) {
            return;
        }

        // Target dikonfigurasi dari DB (fleksibel), fallback ke .env
        $target = PengaturanDesa::get('wa_default_target')
            ?? config('services.whatsapp.default_target');

        if (empty($target)) {
            Log::warning('WhatsApp default target tidak dikonfigurasi. Set wa_default_target di Pengaturan Sistem > WhatsApp.');
            return;
        }

        $namaDesa    = PengaturanDesa::get('nama_desa', 'Desa');
        $baseUrl     = config('app.url');
        $articleUrl  = rtrim($baseUrl, '/') . '/informasi/' . $informasi->slug;

        $rawContent = strip_tags($informasi->konten);
        $summary    = Str::limit(trim(preg_replace('/\s+/', ' ', $rawContent)), 200, '...');

        // Format WhatsApp: *bold*, _italic_
        $message  = "*BERITA & PENGUMUMAN DESA {$namaDesa}*\n\n";
        $message .= "*{$informasi->judul}*\n";
        $message .= "Kategori: #{$informasi->kategori}\n\n";
        $message .= "\"{$summary}\"\n\n";
        $message .= "Baca selengkapnya:\n{$articleUrl}";

        $whatsapp->sendMessage($target, $message);
    }
}

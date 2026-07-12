<?php

namespace App\Jobs;

use App\Models\InformasiPublik;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Job untuk mengirimkan notifikasi rilis berita ke grup Telegram secara asinkronus.
 */
class SendNewsTelegramNotificationJob implements ShouldQueue
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
     * Mengeksekusi pengiriman pesan siaran ke grup Telegram terkait.
     *
     * Jika berita memiliki cover image, akan dikirim sebagai foto dengan caption.
     * Jika foto gagal dikirim, fallback ke pesan teks biasa.
     *
     * @param  \App\Services\TelegramService  $telegram  Layanan bot API Telegram
     * @return void
     */
    public function handle(TelegramService $telegram): void
    {
        $informasi = InformasiPublik::find($this->informasiId);
        if (!$informasi || !$informasi->is_published) {
            return;
        }

        $groupId = config('services.telegram.group_chat_id');
        if (empty($groupId)) {
            Log::warning('Telegram Group Chat ID is not configured. Cannot broadcast news.');
            return;
        }

        $webhookUrl = config('services.telegram.webhook_url');
        $baseUrl = config('app.url');
        if (!empty($webhookUrl)) {
            $parsedWebhook = parse_url($webhookUrl);
            $baseUrl = ($parsedWebhook['scheme'] ?? 'https') . '://' . ($parsedWebhook['host'] ?? '');
        }
        $articleUrl = rtrim($baseUrl, '/') . '/informasi/' . $informasi->slug;

        $rawContent = strip_tags($informasi->konten);
        $summary = Str::limit(trim(preg_replace('/\s+/', ' ', $rawContent)), 250, '...');

        $message = "<b>BERITA & PENGUMUMAN DESA</b>\n\n";
        $message .= "<b>" . htmlspecialchars($informasi->judul, ENT_QUOTES, 'UTF-8') . "</b>\n";
        $message .= "Kategori: <code>#" . htmlspecialchars(str_replace(' ', '', $informasi->kategori), ENT_QUOTES, 'UTF-8') . "</code>\n\n";
        $message .= "<i>\"" . htmlspecialchars($summary, ENT_QUOTES, 'UTF-8') . "\"</i>\n\n";
        $message .= "<a href=\"{$articleUrl}\">Baca Selengkapnya di SIG-Udeung</a>";

        $coverUrl = $informasi->cover_image;
        if ($coverUrl) {
            if (str_contains($coverUrl, 'localhost') || str_contains($coverUrl, '127.0.0.1')) {
                if (!empty($webhookUrl)) {
                    $parsedWebhook = parse_url($webhookUrl);
                    $publicHost = ($parsedWebhook['scheme'] ?? 'https') . '://' . ($parsedWebhook['host'] ?? '');
                    
                    $parsedCover = parse_url($coverUrl);
                    $coverUrl = rtrim($publicHost, '/') . ($parsedCover['path'] ?? '') . (isset($parsedCover['query']) ? '?' . $parsedCover['query'] : '');
                }
            }

            $success = $telegram->sendPhoto($groupId, $coverUrl, $message);
            if ($success) {
                return;
            }
            
            Log::warning("Failed to send news photo. Falling back to text-only message.");
        }

        $telegram->sendMessage($groupId, $message);
    }
}

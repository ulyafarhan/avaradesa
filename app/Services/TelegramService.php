<?php

namespace App\Services;

use App\Models\Penduduk;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk integrasi dengan Bot API Telegram.
 *
 * Menyediakan fungsi kirim pesan, foto, dokumen, broadcast,
 * notifikasi status pengajuan/mutasi, dan pengaturan webhook.
 */
class TelegramService
{
    /**
     * Token bot Telegram dari konfigurasi services.
     */
    protected ?string $botToken = null;

    /**
     * URL endpoint API Telegram.
     */
    protected ?string $apiUrl = null;

    /**
     * Menginisialisasi token bot dan URL API dari konfigurasi.
     */
    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        if ($this->botToken) {
            $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
        }
    }

    /**
     * Mengirim pesan teks ke chat Telegram.
     *
     * Mendukung parse mode HTML dan inline keyboard.
     *
     * @param  string  $chatId  ID chat tujuan di Telegram (bisa berupa user ID atau group ID)
     * @param  string  $message  Isi pesan yang akan dikirim, mendukung format HTML
     * @param  array|null  $keyboard  Array inline keyboard opsional untuk disertakan dalam pesan
     * @return bool  true jika pengiriman berhasil, false jika gagal atau token tidak terkonfigurasi
     */
    public function sendMessage(string $chatId, string $message, ?array $keyboard = null): bool
    {
        try {
            if (! $this->botToken || ! $this->apiUrl) {
                Log::warning('Telegram Bot Token or API URL is not configured.');
                return false;
            }

            $message = str_replace('\n', "\n", $message);

            if ($message === strip_tags($message)) {
                $message = $this->convertMarkdownToHtml($message);
            }

            $payload = [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ];

            if ($keyboard) {
                $payload['reply_markup'] = json_encode($keyboard);
            }

            $response = Http::timeout(10)->connectTimeout(5)->post("{$this->apiUrl}/sendMessage", $payload);

            if (!$response->successful()) {
                Log::error('Telegram Send Message Failed: ' . $response->body() . ' | Payload: ' . json_encode($payload));
            }

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram Send Message Error: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Mengirim foto ke chat Telegram dengan caption.
     *
     * @param  string  $chatId  ID chat tujuan di Telegram
     * @param  string  $photoUrl  URL gambar yang akan dikirim sebagai foto
     * @param  string  $caption  Teks caption yang menyertai foto
     * @return bool  true jika pengiriman berhasil, false jika gagal
     */
    public function sendPhoto(string $chatId, string $photoUrl, string $caption): bool
    {
        try {
            if (! $this->botToken || ! $this->apiUrl) {
                Log::warning('Telegram Bot Token or API URL is not configured.');
                return false;
            }

            $payload = [
                'chat_id' => $chatId,
                'photo' => $photoUrl,
                'caption' => $caption,
                'parse_mode' => 'HTML',
            ];

            $response = Http::timeout(10)->connectTimeout(5)->post("{$this->apiUrl}/sendPhoto", $payload);

            if (!$response->successful()) {
                Log::error('Telegram Send Photo Failed: ' . $response->body() . ' | Photo: ' . $photoUrl);
            }

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram Send Photo Error: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Mengirim dokumen/file ke chat Telegram.
     *
     * Menggunakan multipart form-data untuk upload file.
     *
     * @param  string  $chatId  ID chat tujuan di Telegram
     * @param  string  $filePath  Path lokal file yang akan dikirim (harus dalam format yang didukung Telegram)
     * @param  string  $caption  Teks caption opsional yang menyertai dokumen (default: string kosong)
     * @return bool  true jika pengiriman berhasil, false jika gagal
     */
    public function sendDocument(string $chatId, string $filePath, string $caption = ''): bool
    {
        try {
            if (! $this->botToken || ! $this->apiUrl) {
                Log::warning('Telegram Bot Token or API URL is not configured.');
                return false;
            }

            $response = Http::timeout(15)->connectTimeout(5)->attach(
                'document',
                file_get_contents($filePath),
                basename($filePath)
            )->post("{$this->apiUrl}/sendDocument", [
                'chat_id' => $chatId,
                'caption' => $caption,
                'parse_mode' => 'HTML',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram Send Document Error: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Mengirim pesan ke banyak chat secara bertahap.
     *
     * Memberi jeda 50ms antar pengiriman untuk menghindari rate limit Telegram.
     *
     * @param  array  $chatIds  Array berisi ID-ID chat Telegram tujuan broadcast
     * @param  string  $message  Isi pesan yang akan dikirim ke semua chat
     * @return array  Assoc array dengan kunci 'success' (jumlah berhasil) dan 'failed' (jumlah gagal)
     */
    public function broadcast(array $chatIds, string $message): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
        ];

        foreach ($chatIds as $chatId) {
            if ($this->sendMessage($chatId, $message)) {
                $results['success']++;
            } else {
                $results['failed']++;
            }

            usleep(50000);
        }

        return $results;
    }

    /**
     * Mengirim notifikasi perubahan status pengajuan surat ke warga.
     *
     * Mencari data penduduk berdasarkan NIK dan mengirim pesan status ke chat Telegram warga.
     *
     * @param  string  $nik  NIK pemohon untuk mencari data penduduk
     * @param  string  $status  Status baru pengajuan (Pending, Diproses, Disetujui, Ditolak, Selesai)
     * @param  string  $nomorRegistrasi  Nomor registrasi pengajuan surat
     * @param  string|null  $catatan  Catatan tambahan opsional untuk disertakan dalam notifikasi
     * @return void
     */
    public function notifyPengajuanStatus(string $nik, string $status, string $nomorRegistrasi, ?string $catatan = null): void
    {
        $penduduk = Penduduk::find($nik);

        if (! $penduduk || ! $penduduk->telegram_chat_id) {
            return;
        }

        $statusMessages = [
            'Pending' => 'Pengajuan surat Anda sedang menunggu verifikasi',
            'Diproses' => 'Pengajuan surat Anda sedang diproses',
            'Disetujui' => 'Pengajuan surat Anda telah disetujui',
            'Ditolak' => 'Pengajuan surat Anda ditolak',
            'Selesai' => 'Surat Anda telah selesai dan siap diunduh',
        ];

        $message = "<b>Status Pengajuan Surat</b>\n\n";
        $message .= 'Nomor Registrasi: <code>'.$this->escapeHtml($nomorRegistrasi)."</code>\n";
        $message .= 'Status: '.$this->escapeHtml($statusMessages[$status] ?? $status)."\n";

        if ($catatan) {
            $message .= "\nCatatan:\n".$this->escapeHtml($catatan);
        }

        $this->sendMessage($penduduk->telegram_chat_id, $message);
    }

    /**
     * Mengirim notifikasi perubahan status mutasi kependudukan ke warga.
     *
     * @param  string  $nik  NIK penduduk yang mengajukan mutasi
     * @param  string  $jenisMutasi  Jenis mutasi yang dilakukan (Kelahiran, Kematian, Kedatangan, Kepindahan)
     * @param  string  $status  Status verifikasi mutasi (Disetujui, Ditolak)
     * @return void
     */
    public function notifyMutasiStatus(string $nik, string $jenisMutasi, string $status): void
    {
        $penduduk = Penduduk::find($nik);

        if (! $penduduk || ! $penduduk->telegram_chat_id) {
            return;
        }

        $statusPrefix = $status === 'Disetujui' ? '[Disetujui]' : '[Ditolak]';

        $message = "<b>Status Mutasi Kependudukan</b>\n\n";
        $message .= 'Jenis: '.$this->escapeHtml($jenisMutasi)."\n";
        $message .= 'Status: '.$this->escapeHtml("{$statusPrefix} {$status}")."\n";

        $this->sendMessage($penduduk->telegram_chat_id, $message);
    }

    /**
     * Meng-escape karakter HTML untuk keamanan output.
     *
     * Menggunakan htmlspecialchars dengan flag ENT_QUOTES dan ENT_SUBSTITUTE untuk mencegah XSS.
     *
     * @param  string  $value  Nilai string yang akan di-escape
     * @return string  String yang sudah aman dari karakter HTML berbahaya
     */
    protected function escapeHtml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Mengatur webhook Telegram ke URL yang ditentukan.
     *
     * @param  string  $url  URL endpoint yang akan didaftarkan sebagai webhook Telegram
     * @return bool  true jika webhook berhasil diatur, false jika gagal
     */
    public function setWebhook(string $url): bool
    {
        try {
            $response = Http::timeout(10)->connectTimeout(5)->post("{$this->apiUrl}/setWebhook", [
                'url' => $url,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram Set Webhook Error: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Mendapatkan informasi bot dari API Telegram.
     *
     * @return array|null  Array berisi informasi bot (first_name, username, id) atau null jika gagal
     */
    public function getMe(): ?array
    {
        try {
            $response = Http::timeout(10)->connectTimeout(5)->get("{$this->apiUrl}/getMe");

            if ($response->successful()) {
                return $response->json('result');
            }
        } catch (\Exception $e) {
            Log::error('Telegram Get Me Error: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Mengonversi sintaks Markdown sederhana ke HTML untuk Telegram.
     *
     * Menangani heading (#, ##, ###), bold (**), italic (*), dan list item (*, -).
     *
     * @param  string  $text  Teks dalam format Markdown yang akan dikonversi
     * @return string  Teks dalam format HTML yang kompatibel dengan Telegram
     */
    protected function convertMarkdownToHtml(string $text): string
    {
        $text = preg_replace('/^###\s+(.+)$/m', '<b>$1</b>', $text);
        $text = preg_replace('/^##\s+(.+)$/m', '<b>$1</b>', $text);
        $text = preg_replace('/^#\s+(.+)$/m', '<b>$1</b>', $text);

        $text = preg_replace('/\*\*(.*?)\*\*/s', '<b>$1</b>', $text);

        $text = preg_replace('/\*([^*]+)\*/', '<i>$1</i>', $text);
        
        $text = preg_replace('/^\s*[\*\-]\s+/m', '• ', $text);

        return $text;
    }
}

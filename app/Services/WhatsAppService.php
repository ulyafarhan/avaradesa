<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Layanan WhatsApp Gateway untuk mengirim notifikasi pesan ke warga.
 *
 * Menggunakan gateway self-hosted (wa-gateway/Baileys) yang berjalan
 * di port 2785. Konfigurasi dibaca dari database pengaturan_desa agar
 * admin dapat mengubah gateway URL, API key, dan session_id tanpa perlu
 * mengubah file konfigurasi.
 *
 * Metode:
 * - sendMessage()  : Kirim pesan teks ke satu nomor
 * - sendImage()    : Kirim gambar beserta caption
 * - broadcast()    : Kirim ke banyak nomor secara berurutan
 * - checkHealth()  : Cek status koneksi gateway
 */
class WhatsAppService
{
    /**
     * URL endpoint WhatsApp gateway (contoh: http://127.0.0.1:2785).
     */
    protected string $gatewayUrl;

    /**
     * API key untuk autentikasi ke gateway (header X-API-Key).
     */
    protected string $apiKey;

    /**
     * Session ID WhatsApp yang digunakan (contoh: desaku atau default).
     */
    protected string $sessionId;

    /**
     * Inisialisasi konfigurasi dari database pengaturan_desa (dinamis)
     * dengan fallback ke config/services.php (statis).
     */
    public function __construct()
    {
        // Prioritas: nilai dari database pengaturan_desa
        // Fallback: nilai dari environment (.env) via config/services.php
        $this->gatewayUrl = rtrim(
            \App\Models\PengaturanDesa::get('wa_gateway_url')
                ?? config('services.whatsapp.gateway_url', 'http://localhost:2785'),
            '/'
        );

        $this->apiKey = \App\Models\PengaturanDesa::get('wa_api_key')
            ?? config('services.whatsapp.api_key', '');

        $this->sessionId = \App\Models\PengaturanDesa::get('wa_session_id')
            ?? config('services.whatsapp.session_id', 'default');
    }

    /**
     * Normalisasi nomor telepon ke format JID WhatsApp.
     *
     * Menerima nomor dalam berbagai format:
     * - "62812xxxx" → "62812xxxx@s.whatsapp.net"
     * - "+62812xxxx" → "62812xxxx@s.whatsapp.net"
     * - "08xxxxxxx" → "628xxxxxxx@s.whatsapp.net"
     * - "62812xxxx@c.us" → dikembalikan apa adanya
     *
     * @param  string  $number  Nomor telepon dalam format apapun
     * @return string  JID format yang siap dikirim ke gateway
     */
    protected function normalizeNumber(string $number): string
    {
        if (str_contains($number, '@')) {
            return $number;
        }

        // Hapus semua karakter non-digit
        $digits = preg_replace('/[^0-9]/', '', $number);

        // Konversi 08xxx → 628xxx
        if (str_starts_with($digits, '08')) {
            $digits = '62' . substr($digits, 1);
        }

        return $digits . '@s.whatsapp.net';
    }

    /**
     * Apakah service dikonfigurasi dengan benar (ada API key & gateway URL).
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->gatewayUrl);
    }

    /**
     * Mengirim pesan teks WhatsApp ke satu nomor tujuan.
     *
     * @param  string  $target   Nomor telepon tujuan (berbagai format diterima)
     * @param  string  $message  Pesan teks (mendukung format WhatsApp: *bold*, _italic_)
     * @return bool  true jika berhasil terkirim
     */
    public function sendMessage(string $target, string $message): bool
    {
        try {
            if (!$this->isConfigured()) {
                Log::warning('WhatsApp gateway tidak dikonfigurasi. Periksa wa_gateway_url dan wa_api_key di Pengaturan Sistem.');
                return false;
            }

            $jid = $this->normalizeNumber($target);

            $response = Http::timeout(15)
                ->connectTimeout(10)
                ->withHeaders(['X-API-Key' => $this->apiKey])
                ->post("{$this->gatewayUrl}/api/sessions/{$this->sessionId}/messages/send-text", [
                    'chatId' => $jid,
                    'text'   => $message,
                ]);

            if (!$response->successful()) {
                Log::error('WhatsApp send failed', [
                    'target'  => $target,
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                ]);
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('WhatsApp send error: ' . $e->getMessage(), ['target' => $target]);
            return false;
        }
    }

    /**
     * Mengirim gambar beserta caption ke satu nomor tujuan.
     *
     * @param  string  $target    Nomor telepon tujuan
     * @param  string  $imageUrl  URL gambar yang akan dikirim (harus dapat diakses publik)
     * @param  string  $caption   Teks caption di bawah gambar
     * @return bool  true jika berhasil terkirim
     */
    public function sendImage(string $target, string $imageUrl, string $caption): bool
    {
        try {
            if (!$this->isConfigured()) {
                return false;
            }

            $jid = $this->normalizeNumber($target);

            $response = Http::timeout(30)
                ->connectTimeout(10)
                ->withHeaders(['X-API-Key' => $this->apiKey])
                ->post("{$this->gatewayUrl}/api/sessions/{$this->sessionId}/messages/send-image", [
                    'chatId'   => $jid,
                    'imageUrl' => $imageUrl,
                    'caption'  => $caption,
                ]);

            if (!$response->successful()) {
                Log::error('WhatsApp send image failed', [
                    'target' => $target,
                    'body'   => $response->body(),
                ]);
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('WhatsApp send image error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengirim pesan teks yang sama ke banyak nomor secara berurutan.
     *
     * Setiap pengiriman diberi jeda 100ms untuk menghindari rate limit.
     *
     * @param  array<string>  $targets  Array nomor telepon tujuan
     * @param  string         $message  Pesan teks yang akan dikirim
     * @return array{success: int, failed: int}  Hasil rekapitulasi pengiriman
     */
    public function broadcast(array $targets, string $message): array
    {
        $results = ['success' => 0, 'failed' => 0];

        foreach ($targets as $target) {
            if ($this->sendMessage($target, $message)) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
            usleep(100_000); // 100ms jeda antar kirim
        }

        return $results;
    }

    /**
     * Memeriksa status koneksi WhatsApp gateway.
     *
     * @return array{connected: bool, connection: string}
     */
    public function checkHealth(): array
    {
        try {
            if (!$this->isConfigured()) {
                return ['connected' => false, 'connection' => 'not_configured'];
            }

            $response = Http::timeout(5)
                ->connectTimeout(3)
                ->withHeaders(['X-API-Key' => $this->apiKey])
                ->get("{$this->gatewayUrl}/api/status");

            if ($response->successful()) {
                return $response->json() + ['connected' => $response->json('connected', false)];
            }

            return ['connected' => false, 'connection' => 'error'];

        } catch (\Exception) {
            return ['connected' => false, 'connection' => 'unreachable'];
        }
    }

    /**
     * Mengambil QR code dari gateway untuk menghubungkan akun WhatsApp.
     *
     * @return string|null  Data QR code jika tersedia, null jika sudah terhubung atau error
     */
    public function getQrCode(): ?string
    {
        try {
            if (!$this->isConfigured()) {
                return null;
            }

            $response = Http::timeout(10)
                ->withHeaders(['X-API-Key' => $this->apiKey])
                ->get("{$this->gatewayUrl}/api/qr");

            return $response->json('qr');

        } catch (\Exception) {
            return null;
        }
    }
}

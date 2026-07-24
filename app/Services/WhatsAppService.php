<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Penduduk;
use App\Services\SystemLogger;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Layanan WhatsApp multi-provider untuk mengirim notifikasi pesan ke warga.
 *
 * Mendukung dua provider:
 * - wa-gateway: Self-hosted (Baileys) di port 2785
 * - fonnte:     Layanan cloud Fonnte (tanpa VPS)
 *
 * Provider dipilih via env WHA_PROVIDER (fallback: wa-gateway).
 * Nilai aktual dibaca dari database pengaturan_desa, lalu fallback ke config/services.php.
 */
class WhatsAppService
{
    protected string $provider;
    protected string $gatewayUrl;
    protected string $apiKey;
    protected string $sessionId;
    protected string $fonnteToken;

    /**
     * Inisialisasi konfigurasi dari database pengaturan_desa (dinamis)
     * dengan fallback ke config/services.php (statis).
     */
    public function __construct()
    {
        $this->provider = \App\Models\PengaturanDesa::get('wa_provider')
            ?? config('services.whatsapp.provider', 'wa-gateway');

        $this->gatewayUrl = rtrim(
            \App\Models\PengaturanDesa::get('wa_gateway_url')
                ?? config('services.whatsapp.gateway_url', 'http://localhost:2785'),
            '/'
        );

        $this->apiKey = \App\Models\PengaturanDesa::get('wa_api_key')
            ?? config('services.whatsapp.api_key', '');

        $this->sessionId = \App\Models\PengaturanDesa::get('wa_session_id')
            ?? config('services.whatsapp.session_id', 'default');

        $this->fonnteToken = \App\Models\PengaturanDesa::get('wa_fonnte_token')
            ?? config('services.whatsapp.fonnte_token', '');
    }

    /**
     * Mengirim pesan teks WhatsApp — provider switching otomatis.
     */
    public function sendMessage(string $target, string $message): bool
    {
        return $this->provider === 'fonnte'
            ? $this->sendViaFonnte($target, $message)
            : $this->sendViaGateway($target, $message);
    }

    /**
     * Mengirim gambar beserta caption — provider switching otomatis.
     */
    public function sendImage(string $target, string $imageUrl, string $caption = ''): bool
    {
        return $this->provider === 'fonnte'
            ? $this->sendViaFonnte($target, $caption, $imageUrl)
            : $this->sendImageViaGateway($target, $imageUrl, $caption);
    }

    /**
     * Mengirim pesan ke banyak nomor secara berurutan.
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
            usleep(100_000);
        }
        return $results;
    }

    /**
     * Provider aktif saat ini.
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * Apakah service dikonfigurasi dengan benar.
     */
    public function isConfigured(): bool
    {
        if ($this->provider === 'fonnte') {
            return !empty($this->fonnteToken);
        }
        return !empty($this->apiKey) && !empty($this->gatewayUrl);
    }

    // ── Normalisasi Nomor ─────────────────────────────────────────────

    /**
     * Normalisasi nomor telepon ke format JID WhatsApp.
     */
    protected function normalizeNumber(string $number): string
    {
        if (str_contains($number, '@')) {
            return $number;
        }

        $digits = preg_replace('/[^0-9]/', '', $number);

        if (str_starts_with($digits, '08')) {
            $digits = '62' . substr($digits, 1);
        }

        return $digits . '@s.whatsapp.net';
    }

    // ── WA Gateway Provider ───────────────────────────────────────────

    protected function sendViaGateway(string $target, string $message): bool
    {
        try {
            if (!$this->isConfigured()) {
                Log::warning('WhatsApp gateway tidak dikonfigurasi.');
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
                    'target' => $target,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                SystemLogger::log('notification.sent', "WhatsApp gagal dikirim ke $target", null, [
                    'provider' => 'wa-gateway', 'target' => $target, 'status' => $response->status(),
                ]);
            } else {
                SystemLogger::log('notification.sent', "WhatsApp berhasil dikirim ke $target", null, [
                    'provider' => 'wa-gateway', 'target' => $target,
                ]);
            }

            return $response->successful();
        } catch (ConnectionException $e) {
            Log::warning('WhatsApp gateway unreachable: ' . $e->getMessage(), ['target' => $target]);
            SystemLogger::log('notification.sent', "WhatsApp gateway unreachable: $target", null, [
                'provider' => 'wa-gateway', 'target' => $target, 'error' => $e->getMessage(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp send error: ' . $e->getMessage(), ['target' => $target]);
            SystemLogger::log('notification.sent', "WhatsApp send error: $target", null, [
                'provider' => 'wa-gateway', 'target' => $target, 'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    protected function sendImageViaGateway(string $target, string $imageUrl, string $caption): bool
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
            }

            return $response->successful();
        } catch (ConnectionException $e) {
            Log::warning('WhatsApp gateway unreachable (image): ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp send image error: ' . $e->getMessage());
            return false;
        }
    }

    // ── Fonnte Provider ───────────────────────────────────────────────

    protected function sendViaFonnte(string $target, string $message, string $url = ''): bool
    {
        try {
            if (empty($this->fonnteToken)) {
                Log::warning('Fonnte token tidak dikonfigurasi.');
                return false;
            }

            $digits = preg_replace('/[^0-9]/', '', $target);
            $target = str_starts_with($digits, '0') ? '62' . substr($digits, 1) : $digits;

            $payload = ['target' => $target, 'message' => $message];
            if (!empty($url)) {
                $payload['url'] = $url;
            }

            $response = Http::timeout(15)
                ->withHeaders(['Authorization' => $this->fonnteToken])
                ->post('https://api.fonnte.com/send', $payload);

            $body = $response->json();
            if (!($body['status'] ?? false)) {
                Log::error('Fonnte send failed: ' . ($body['reason'] ?? $response->body()));
                SystemLogger::log('notification.sent', "Fonnte gagal ke $target", null, [
                    'provider' => 'fonnte', 'target' => $target, 'reason' => $body['reason'] ?? 'unknown',
                ]);
                return false;
            }

            SystemLogger::log('notification.sent', "Fonnte berhasil ke $target", null, [
                'provider' => 'fonnte', 'target' => $target,
            ]);
            return true;
        } catch (ConnectionException $e) {
            Log::warning('Fonnte unreachable: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Log::error('Fonnte send error: ' . $e->getMessage());
            return false;
        }
    }

    // ── Health Check ──────────────────────────────────────────────────

    /**
     * Memeriksa status koneksi WhatsApp gateway.
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
                SystemLogger::log('notification.sent', "WhatsApp image berhasil dikirim ke $target", null, [
                    'provider' => 'wa-gateway', 'target' => $target, 'type' => 'image',
                ]);
                return $response->json() + ['connected' => $response->json('connected', false)];
            }

            return ['connected' => false, 'connection' => 'error'];
        } catch (\Exception) {
            return ['connected' => false, 'connection' => 'unreachable'];
        }
    }

    /**
     * Mengambil QR code dari gateway untuk menghubungkan akun WhatsApp.
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

    /**
     * Kirim notifikasi status pengajuan surat ke warga via WhatsApp.
     */
    public function notifyPengajuanStatus(string $nik, string $status, string $nomorRegistrasi, ?string $catatan = null): void
    {
        $penduduk = Penduduk::find($nik);
        if (!$penduduk || !$penduduk->no_hp) return;

        $template = $this->getTemplate('wa_surat_' . strtolower($status), [
            '{nomor}' => $nomorRegistrasi,
            '{catatan}' => $catatan ?? '',
            '{link}' => config('app.url') . '/warga/dashboard?tab=pengajuan',
        ]);

        $this->sendMessage($penduduk->no_hp, $template);
    }

    public function notifyMutasiStatus(string $nik, string $jenisMutasi, string $status): void
    {
        $penduduk = Penduduk::find($nik);
        if (!$penduduk || !$penduduk->no_hp) return;

        $template = $this->getTemplate('wa_mutasi_' . strtolower($status), [
            '{jenis}' => $jenisMutasi,
            '{status}' => $status,
        ]);

        $this->sendMessage($penduduk->no_hp, $template);
    }

    /**
     * Cari template dari pengaturan_desa, fallback ke hardcoded.
     */
    protected function getTemplate(string $key, array $replace): string
    {
        $defaults = [
            'wa_surat_pending' => "Status Pengajuan Surat\n\nNomor Registrasi: {nomor}\nStatus: menunggu verifikasi",
            'wa_surat_disetujui' => "Status Pengajuan Surat\n\nNomor Registrasi: {nomor}\nStatus: telah disetujui",
            'wa_surat_ditolak' => "Status Pengajuan Surat\n\nNomor Registrasi: {nomor}\nStatus: ditolak\n\nCatatan: {catatan}",
            'wa_surat_selesai' => "Status Pengajuan Surat\n\nNomor Registrasi: {nomor}\nStatus: selesai, siap diunduh\n\n{link}",
            'wa_mutasi_disetujui' => "Status Mutasi Kependudukan\n\nJenis: {jenis}\nStatus: {status}",
            'wa_mutasi_ditolak' => "Status Mutasi Kependudukan\n\nJenis: {jenis}\nStatus: {status}",
        ];

        // ponytail: ambil dari pengaturan_desa, fallback ke hardcoded
        $raw = \App\Models\PengaturanDesa::get('notif_' . $key)
            ?? ($defaults[$key] ?? '');

        return str_replace(array_keys($replace), array_values($replace), $raw);
    }
}

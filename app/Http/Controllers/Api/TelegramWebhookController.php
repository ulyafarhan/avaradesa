<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessTelegramMessageJob;
use App\Services\SystemLogger;
use App\Services\TelegramService;
use App\Services\TelegramKnowledgeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Controller untuk menangani Webhook API Telegram (Bot virtual SIG-Udeung).
 *
 * @group Integrasi Telegram
 */
class TelegramWebhookController extends Controller
{
    /**
     * Inisialisasi controller dengan layanan Telegram dan Pangkalan Pengetahuan.
     */
    public function __construct(
        protected TelegramService $telegram,
        protected TelegramKnowledgeService $knowledge
    ) {}

    /**
     * Endpoint utama penerima payload webhook Telegram.
     *
     * @unauthenticated
     *
     * @bodyParameter message object Data pesan dari Telegram yang berisi chat id dan teks.
     * @bodyParameter message.chat object Informasi chat pengguna.
     * @bodyParameter message.chat.id int ID chat Telegram pengguna.
     * @bodyParameter message.text string Teks pesan dari pengguna.
     * @bodyParameter callback_query object Data callback query dari tombol inline (opsional).
     *
     * @responseField ok boolean Status penerimaan webhook.
     *
     * @response {
     *   "ok": true
     * }
     */
    public function handle(Request $request)
    {
        $secret = $request->header('X-Telegram-Bot-Api-Secret-Token');
        $expected = config('services.telegram.webhook_secret');
        
        if (!$expected || !hash_equals($expected, (string) $secret)) {
            Log::warning('Invalid Telegram webhook secret', [
                'ip' => $request->ip(),
                'provided' => $secret,
            ]);
            SystemLogger::log('webhook.received', 'Webhook Telegram ditolak (invalid secret)', null, [
                'ip' => $request->ip(), 'result' => 'rejected',
            ]);
            abort(401, 'Invalid webhook secret');
        }

        $request->validate([
            'message' => 'nullable|array',
            'callback_query' => 'nullable|array',
        ]);

        $update = $request->all();
        
        Log::info('Telegram Webhook received', [
            'chat_id' => $update['message']['chat']['id'] ?? $update['callback_query']['message']['chat']['id'] ?? 'unknown'
        ]);

        SystemLogger::log('webhook.received', 'Webhook Telegram diterima', null, [
            'chat_id' => $update['message']['chat']['id'] ?? $update['callback_query']['message']['chat']['id'] ?? 'unknown',
            'has_message' => isset($update['message']),
            'has_callback' => isset($update['callback_query']),
        ]);

        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        }

        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Memproses pesan teks dari pengguna Telegram.
     *
     * Menangani perintah /start untuk pesan selamat datang, perintah /bind untuk
     * menghubungkan akun Telegram dengan NIK warga, pencarian jawaban dari basis
     * pengetahuan statis, caching respons, dan pemrosesan AI dengan pembatasan
     * kuota harian maksimal 10 pertanyaan per pengguna per hari.
     *
     * @param  array  $message  Array data pesan dari Telegram yang berisi chat id dan teks pesan
     * @return void
     */
    protected function handleMessage(array $message)
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';

        if ($text === '/start') {
            $welcomeMessage = "Selamat datang di Bot AvaraDesa-Udeung!\n\n";
            $welcomeMessage .= "Saya adalah asisten virtual Desa Udeung.\n\n";
            $welcomeMessage .= "Anda dapat bertanya tentang:\n";
            $welcomeMessage .= "• Prosedur pembuatan surat\n";
            $welcomeMessage .= "• Persyaratan dokumen\n";
            $welcomeMessage .= "• Informasi umum desa\n\n";
            $welcomeMessage .= 'Silakan kirim pertanyaan Anda!';

            $this->telegram->sendMessage($chatId, $welcomeMessage);
            return;
        }

        if (str_starts_with($text, '/bind')) {
            $parts = explode(' ', $text);
            if (count($parts) === 2) {
                $nik = $parts[1];
                $this->telegram->sendMessage(
                    $chatId,
                    "Untuk menghubungkan akun, silakan buka PWA dan masukkan kode: {$chatId}"
                );
            } else {
                $this->telegram->sendMessage(
                    $chatId,
                    "Format: /bind [NIK]\nContoh: /bind 1234567890123456"
                );
            }
            return;
        }

        if (empty(trim($text))) {
            return;
        }

        $staticAnswer = $this->knowledge->findStaticAnswer($text);
        if ($staticAnswer !== null) {
            $this->telegram->sendMessage($chatId, $staticAnswer);
            return;
        }

        $cacheKey = 'telegram_reply_' . md5(trim(strtolower($text)));
        $cachedResponse = Cache::get($cacheKey);
        if ($cachedResponse !== null) {
            $this->telegram->sendMessage($chatId, $cachedResponse);
            return;
        }

        $rateLimitKey = 'telegram_ai_limit_' . $chatId . '_' . date('Y-m-d');
        $usageCount = (int) Cache::get($rateLimitKey, 0);
        $maxDailyQueries = 10;

        if ($usageCount >= $maxDailyQueries) {
            $this->telegram->sendMessage(
                $chatId,
                "<b>Batas Pertanyaan AI Habis</b>\n\nKuota harian Anda untuk bertanya pada asisten AI hari ini telah habis (Maks. {$maxDailyQueries} kali/hari).\n\nUntuk pertanyaan darurat, silakan langsung hubungi Kantor Kepala Desa Udeung."
            );
            return;
        }

        Cache::put($rateLimitKey, $usageCount + 1, now()->addDay());

        ProcessTelegramMessageJob::dispatch((string) $chatId, $text);
    }

    /**
     * Memproses callback query dari tombol inline Telegram.
     *
     * Metode ini menangani callback query yang dikirim oleh pengguna ketika
     * menekan tombol inline pada pesan bot. Saat ini hanya mengirimkan
     * konfirmasi callback yang diterima.
     *
     * @param  array  $callbackQuery  Array data callback query dari Telegram yang berisi chat id dan data callback
     * @return void
     */
    protected function handleCallbackQuery(array $callbackQuery)
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $data = $callbackQuery['data'];

        $this->telegram->sendMessage($chatId, "Callback received: {$data}");
    }
}

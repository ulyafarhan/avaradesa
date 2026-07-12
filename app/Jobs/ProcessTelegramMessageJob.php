<?php

namespace App\Jobs;

use App\Services\Contracts\AiProviderInterface;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job untuk memproses pesan masuk dari Telegram warga secara asinkronus menggunakan penyedia AI.
 */
class ProcessTelegramMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah maksimum percobaan jika pemrosesan gagal.
     */
    public int $tries = 2;

    /**
     * Inisialisasi Job dengan ID Chat Telegram pengirim dan isi teks pesan.
     *
     * @param  string  $chatId  ID chat Telegram pengirim pesan
     * @param  string  $text  Isi teks pesan yang diterima
     */
    public function __construct(
        public string $chatId,
        public string $text
    ) {}

    /**
     * Mengeksekusi penanganan pesan masuk menggunakan kecerdasan buatan (AI) dan membalas pengirim.
     *
     * Alur proses:
     * 1. Mengambil konteks dari knowledge base berdasarkan pesan
     * 2. Menghasilkan respons AI dengan konteks RAG
     * 3. Mengcache respons untuk pertanyaan serupa
     * 4. Mengirim balasan ke pengirim via Telegram
     *
     * @param  \App\Services\Contracts\AiProviderInterface  $ai  Provider AI untuk menghasilkan respons
     * @param  \App\Services\TelegramService  $telegram  Layanan Telegram untuk mengirim balasan
     * @param  \App\Services\TelegramKnowledgeService  $knowledge  Layanan knowledge base untuk RAG
     * @return void
     */
    public function handle(
        AiProviderInterface $ai, 
        TelegramService $telegram,
        \App\Services\TelegramKnowledgeService $knowledge
    ): void {
        $context = $knowledge->retrieveContext($this->text);

        $aiResponse = $ai->generateResponse($this->text, $this->chatId, $context);

        if ($aiResponse) {
            $cacheKey = 'telegram_reply_' . md5(trim(strtolower($this->text)));
            \Illuminate\Support\Facades\Cache::put($cacheKey, $aiResponse, now()->addHours(2));

            $telegram->sendMessage($this->chatId, $aiResponse);

            return;
        }

        $telegram->sendMessage(
            $this->chatId,
            'Maaf, saya sedang mengalami gangguan. Silakan coba lagi nanti atau hubungi kantor desa.'
        );
    }
}

<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

/**
 * Perintah Artisan untuk mendaftarkan dan mengonfigurasi URL Webhook Bot Telegram ke server Telegram API.
 *
 * @package App\Console\Commands
 */
class SetupTelegramWebhook extends Command
{
    /**
     * Nama dan tanda tangan perintah Artisan.
     */
    protected $signature = 'telegram:setup-webhook';

    /**
     * Deskripsi singkat perintah Artisan.
     */
    protected $description = 'Setup Telegram webhook untuk bot';

    /**
     * Eksekusi utama perintah untuk mendaftarkan URL Webhook Telegram.
     *
     * Membaca URL webhook dari config services.telegram.webhook_url,
     * kemudian mendaftarkannya ke Telegram API. Jika berhasil,
     * menampilkan informasi bot (nama dan username).
     *
     * @param  \App\Services\TelegramService  $telegram  Layanan bot API Telegram
     * @return int  Command::SUCCESS atau Command::FAILURE
     */
    public function handle(TelegramService $telegram): int
    {
        $this->info('Setting up Telegram webhook...');

        $webhookUrl = config('services.telegram.webhook_url');
        
        if (empty($webhookUrl)) {
            $this->error('TELEGRAM_WEBHOOK_URL tidak diset di .env');
            return self::FAILURE;
        }

        $this->info("Webhook URL: {$webhookUrl}");

        if ($telegram->setWebhook($webhookUrl)) {
            $this->info('✅ Webhook berhasil disetup!');
            
            $botInfo = $telegram->getMe();
            if ($botInfo) {
                $this->info("Bot Name: {$botInfo['first_name']}");
                $this->info("Bot Username: @{$botInfo['username']}");
            }
            
            return self::SUCCESS;
        }

        $this->error('❌ Gagal setup webhook');
        return self::FAILURE;
    }
}

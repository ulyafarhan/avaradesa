<?php

namespace App\Console\Commands;

use App\Models\TelegramBroadcastQueue;
use App\Jobs\ProcessTelegramBroadcastJob;
use Illuminate\Console\Command;

/**
 * Perintah Artisan untuk memproses antrean pesan siaran (broadcast) Telegram warga yang terjadwal.
 *
 * @package App\Console\Commands
 */
class ProcessBroadcastQueue extends Command
{
    /**
     * Nama dan tanda tangan perintah Artisan.
     */
    protected $signature = 'telegram:process-broadcast';

    /**
     * Deskripsi singkat perintah Artisan.
     */
    protected $description = 'Process pending Telegram broadcast queue';

    /**
     * Eksekusi utama perintah untuk memicu pengiriman pesan siaran terjadwal.
     *
     * Mengambil semua broadcast yang statusnya 'Ready' dari database,
     * lalu mendispatch ke queue untuk diproses secara asinkronus.
     *
     * @return int  Command::SUCCESS
     */
    public function handle(): int
    {
        $this->info('Processing broadcast queue...');

        $broadcasts = TelegramBroadcastQueue::ready()->get();

        if ($broadcasts->isEmpty()) {
            $this->info('No broadcasts to process');
            return self::SUCCESS;
        }

        $this->info("Found {$broadcasts->count()} broadcasts");

        foreach ($broadcasts as $broadcast) {
            ProcessTelegramBroadcastJob::dispatch($broadcast);
            $this->info("Dispatched broadcast #{$broadcast->id}");
        }

        $this->info('All broadcasts dispatched to queue');
        return self::SUCCESS;
    }
}

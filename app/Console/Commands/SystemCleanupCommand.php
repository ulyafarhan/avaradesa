<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Perintah Artisan untuk membersihkan data lama secara berkala (chatbot logs, audit logs, token expired, failed jobs).
 *
 * @package App\Console\Commands
 */
class SystemCleanupCommand extends Command
{
    /**
     * Nama dan tanda tangan perintah Artisan.
     */
    protected $signature = 'system:cleanup';

    /**
     * Deskripsi singkat perintah Artisan.
     */
    protected $description = 'Perform periodic system maintenance and data cleanup';

    /**
     * Eksekusi utama perintah untuk pembersihan dan pemeliharaan data sistem.
     *
     * Pembersihan yang dilakukan:
     * 1. Hapus chatbot_logs lebih dari 30 hari
     * 2. Hapus audit_logs lebih dari 90 hari
     * 3. Prune expired personal access tokens (Sanctum)
     * 4. Prune failed queue jobs lebih dari 72 jam
     *
     * @return int  Command::SUCCESS atau Command::FAILURE
     */
    public function handle(): int
    {
        $this->info('Starting system cleanup...');
        Log::info('System cleanup started.');

        try {
            $deletedChatbotLogs = DB::table('chatbot_logs')
                ->where('created_at', '<', now()->subDays(30))
                ->delete();
            $this->info("Deleted $deletedChatbotLogs chatbot logs older than 30 days.");
            Log::info("System cleanup: Deleted $deletedChatbotLogs chatbot logs.");

            $deletedAuditLogs = DB::table('audit_logs')
                ->where('created_at', '<', now()->subDays(90))
                ->delete();
            $this->info("Deleted $deletedAuditLogs audit logs older than 90 days.");
            Log::info("System cleanup: Deleted $deletedAuditLogs audit logs.");

            $this->info('Pruning expired personal access tokens...');
            Artisan::call('sanctum:prune-expired');
            $this->info('Sanctum tokens pruned.');

            $this->info('Pruning failed queue jobs...');
            Artisan::call('queue:prune-failed', ['--hours' => 72]);
            $this->info('Failed queue jobs pruned.');

            $this->info('System cleanup completed successfully.');
            Log::info('System cleanup completed successfully.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error during system cleanup: ' . $e->getMessage());
            Log::error('System cleanup failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

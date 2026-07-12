<?php

namespace App\Jobs;

use App\Models\TelegramBroadcastQueue;
use App\Models\Penduduk;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job untuk memproses pengiriman pesan siaran (broadcast) massal ke warga via bot Telegram.
 */
class ProcessTelegramBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Inisialisasi Job dengan objek antrean broadcast.
     *
     * @param  \App\Models\TelegramBroadcastQueue  $broadcast  Model antrean broadcast yang akan diproses
     */
    public function __construct(
        public TelegramBroadcastQueue $broadcast
    ) {}

    /**
     * Mengeksekusi pengiriman pesan broadcast berdasarkan target kategori warga.
     *
     * Proses:
     * 1. Update status broadcast ke 'Processing'
     * 2. Mendapatkan daftar chat ID berdasarkan kategori target
     * 3. Mengirim pesan broadcast ke semua chat ID
     * 4. Update status broadcast ke 'Completed' atau 'Failed'
     *
     * @param  \App\Services\TelegramService  $telegram  Layanan bot API Telegram
     * @return void
     */
    public function handle(TelegramService $telegram): void
    {
        try {
            $this->broadcast->update(['status' => 'Processing']);

            $chatIds = $this->getTargetChatIds($this->broadcast->kategori_target);

            if (empty($chatIds)) {
                $this->broadcast->update([
                    'status' => 'Failed',
                    'waktu_selesai' => now(),
                ]);
                return;
            }

            $results = $telegram->broadcast($chatIds, $this->broadcast->pesan);

            $this->broadcast->update([
                'status' => 'Completed',
                'waktu_selesai' => now(),
            ]);

            Log::info("Broadcast completed: {$results['success']} success, {$results['failed']} failed");

        } catch (\Exception $e) {
            Log::error("Broadcast failed: " . $e->getMessage());
            
            $this->broadcast->update([
                'status' => 'Failed',
                'waktu_selesai' => now(),
            ]);

            throw $e;
        }
    }

    /**
     * Mendapatkan daftar chat ID Telegram berdasarkan kategori target.
     *
     * Kategori yang didukung:
     * - 'semua': Semua warga yang memiliki chat ID
     * - 'aktif': Warga dengan status mutasi 'Tetap'
     * - 'laki-laki': Warga berjenis kelamin Laki-laki
     * - 'perempuan': Warga berjenis kelamin Perempuan
     * - 'dusun:{nama}': Warga dari dusun tertentu
     *
     * @param  string  $kategori  Kategori target broadcast
     * @return array  Array berisi chat ID Telegram yang sesuai kategori
     */
    protected function getTargetChatIds(string $kategori): array
    {
        $query = Penduduk::whereNotNull('telegram_chat_id');

        switch ($kategori) {
            case 'semua':
                break;
            
            case 'aktif':
                $query->where('status_mutasi', 'Tetap');
                break;
            
            case 'laki-laki':
                $query->where('jenis_kelamin', 'L');
                break;
            
            case 'perempuan':
                $query->where('jenis_kelamin', 'P');
                break;
            
            default:
                if (str_starts_with($kategori, 'dusun:')) {
                    $dusun = str_replace('dusun:', '', $kategori);
                    $query->whereHas('keluarga', function ($q) use ($dusun) {
                        $q->where('dusun', $dusun);
                    });
                }
        }

        return $query->pluck('telegram_chat_id')->toArray();
    }
}

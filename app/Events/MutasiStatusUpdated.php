<?php

namespace App\Events;

use App\Models\MutasiPenduduk;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang dipicu ketika status pengajuan mutasi penduduk diperbarui.
 * Digunakan untuk menyiarkan perubahan status secara real-time.
 */
class MutasiStatusUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * Membuat instance event baru.
     *
     * @param MutasiPenduduk $mutasi Objek mutasi penduduk yang statusnya berubah.
     * @param string $oldStatus Status lama sebelum diperbarui.
     * @param string $newStatus Status baru setelah diperbarui.
     */
    public function __construct(
        public MutasiPenduduk $mutasi,
        public string $oldStatus,
        public string $newStatus,
    ) {}

    /**
     * Mendapatkan saluran (channel) tempat event ini harus disiarkan.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('mutasi'),
        ];
    }

    /**
     * Mendapatkan data yang akan dikirimkan bersama dengan siaran event.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->mutasi->id,
            'nik' => $this->mutasi->nik,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'jenis_mutasi' => $this->mutasi->jenis_mutasi,
        ];
    }
}

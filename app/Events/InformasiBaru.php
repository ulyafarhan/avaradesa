<?php

namespace App\Events;

use App\Models\InformasiPublik;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang dipicu ketika ada pengumuman/berita baru yang dipublikasikan (sebagai pemicu siaran langsung).
 */
class InformasiBaru
{
    use Dispatchable, SerializesModels;

    /**
     * Inisialisasi Event dengan objek informasi publik yang baru dibuat.
     */
    public function __construct(
        public InformasiPublik $informasi,
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('informasi')];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->informasi->id,
            'judul' => $this->informasi->judul,
            'slug' => $this->informasi->slug,
            'kategori' => $this->informasi->kategori,
        ];
    }
}

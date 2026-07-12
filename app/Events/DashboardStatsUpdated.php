<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang dipicu ketika data statistik dasbor admin diperbarui untuk memicu broadcast.
 */
class DashboardStatsUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * Inisialisasi Event dengan array rangkuman statistik.
     */
    public function __construct(
        public array $stats,
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('dashboard')];
    }
}

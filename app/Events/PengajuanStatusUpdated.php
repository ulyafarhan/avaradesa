<?php

namespace App\Events;

use App\Models\PengajuanSurat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event yang dipicu ketika status pengajuan surat warga diperbarui.
 * Berfungsi untuk mengirimkan siaran (broadcast) perubahan status ke panel warga maupun admin.
 */
class PengajuanStatusUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * Membuat instance event baru.
     *
     * @param PengajuanSurat $pengajuan Objek pengajuan surat yang statusnya diperbarui.
     * @param string $oldStatus Status lama sebelum diperbarui.
     * @param string $newStatus Status baru setelah diperbarui.
     */
    public function __construct(
        public PengajuanSurat $pengajuan,
        public string $oldStatus,
        public string $newStatus,
    ) {}

    /**
     * Mendapatkan saluran (channel) tempat event ini harus disiarkan.
     * Siaran dikirimkan ke channel umum 'pengajuan' dan channel privat/spesifik milik warga pemohon.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('pengajuan'),
            new Channel('warga.' . $this->pengajuan->nik_pemohon),
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
            'id' => $this->pengajuan->id,
            'nomor_registrasi' => $this->pengajuan->nomor_registrasi,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'nama_surat' => $this->pengajuan->kategori?->nama_surat,
            'nama_pemohon' => $this->pengajuan->pemohon?->nama_lengkap,
        ];
    }
}

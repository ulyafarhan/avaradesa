<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\InformasiPublik;
use App\Models\Penduduk;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendNewsWhatsappNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public string $informasiId
    ) {}

    public function handle(WhatsAppService $whatsapp): void
    {
        $informasi = InformasiPublik::find($this->informasiId);
        if (!$informasi || !$informasi->is_published) {
            return;
        }

        $targets = Penduduk::where('status_mutasi', 'Tetap')
            ->whereNotNull('no_hp')
            ->where('no_hp', '!=', '')
            ->pluck('no_hp')
            ->all();

        if (!$targets) {
            Log::warning('Tidak ada no_hp penduduk untuk broadcast WhatsApp.');
            return;
        }

        $namaDesa   = \App\Models\PengaturanDesa::get('nama_desa', 'Desa');
        $baseUrl    = config('app.url');
        $articleUrl = rtrim($baseUrl, '/') . '/informasi/' . $informasi->slug;

        $rawContent = strip_tags($informasi->konten);
        $summary    = Str::limit(trim(preg_replace('/\s+/', ' ', $rawContent)), 200, '...');

        $message  = "*BERITA & PENGUMUMAN DESA {$namaDesa}*\n\n";
        $message .= "*{$informasi->judul}*\n";
        $message .= "Kategori: #{$informasi->kategori}\n\n";
        $message .= "\"{$summary}\"\n\n";
        $message .= "Baca selengkapnya:\n{$articleUrl}";

        $whatsapp->broadcast($targets, $message);
    }
}

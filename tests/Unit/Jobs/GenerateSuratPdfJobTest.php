<?php

namespace Tests\Unit\Jobs;

use App\Jobs\GenerateSuratPdfJob;
use App\Models\KategoriSurat;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\TrackingPengajuanSurat;
use App\Services\PdfGeneratorService;
use App\Services\TelegramService;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Mockery\MockInterface;

class GenerateSuratPdfJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_processes_pdf_updates_status_and_notifies()
    {
        $penduduk = Penduduk::factory()->create([
            'telegram_chat_id' => '12345'
        ]);
        
        $kategori = KategoriSurat::factory()->create([
            'kode_surat' => 'SKD'
        ]);
        
        $pengajuan = PengajuanSurat::factory()->create([
            'nik_pemohon' => $penduduk->nik,
            'kategori_surat_id' => $kategori->id,
            'status' => 'Disetujui',
            'nomor_surat' => 'SKD/001/2026',
            'nomor_registrasi' => 'REG-001'
        ]);

        $pdfService = $this->mock(PdfGeneratorService::class);
        // We aren't calling pdfService in the job handle directly, it's injected but unused directly! Wait, let me check the code.
        // Actually, PdfGeneratorService is injected but only TelegramService is used in the try block? Let me check.
        // Oh, the job doesn't actually call PdfService directly in the code provided? Ah, looking at GenerateSuratPdfJob.php, pdfService is indeed injected but not used. It only generates qr_hash manually.
        
        $telegramService = $this->mock(TelegramService::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendMessage')
                 ->once()
                 ->with('12345', \Mockery::type('string'));
                 
            $mock->shouldReceive('notifyPengajuanStatus')
                 ->once()
                 ->with(\Mockery::type('string'), 'Selesai', 'REG-001');
        });

        $whatsappService = $this->mock(WhatsAppService::class, function (MockInterface $mock) {
            $mock->shouldReceive('notifyPengajuanStatus')
                 ->once()
                 ->with(\Mockery::type('string'), 'Selesai', \Mockery::type('string'));
        });

        $job = new GenerateSuratPdfJob($pengajuan);
        $job->handle($pdfService, $telegramService, $whatsappService);

        $pengajuan->refresh();
        $this->assertEquals('Selesai', $pengajuan->status);
        $this->assertNotNull($pengajuan->qr_hash);
        $this->assertEquals("/warga/pengajuan/{$pengajuan->id}/print", $pengajuan->file_pdf_url);

        $this->assertDatabaseHas('tracking_pengajuan_surat', [
            'pengajuan_surat_id' => $pengajuan->id,
            'status_baru' => 'Selesai'
        ]);
    }
}

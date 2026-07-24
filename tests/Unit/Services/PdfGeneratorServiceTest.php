<?php

namespace Tests\Unit\Services;

use App\Models\KategoriSurat;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Services\PdfGeneratorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PdfGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->penduduk = Penduduk::factory()->create();
        $this->kategori = KategoriSurat::factory()->create([
            'kode_surat' => 'SKD',
            'template_view' => 'skd'
        ]);
        $this->pengajuan = PengajuanSurat::factory()->create([
            'nik_pemohon' => $this->penduduk->nik,
            'kategori_surat_id' => $this->kategori->id,
            'data_isian' => ['keperluan' => 'Test'],
            'created_at' => now()->setYear(2026)->setMonth(7)->setDay(10),
        ]);
    }

    public function test_generate_qr_hash_returns_valid_sha256()
    {
        $service = new class extends PdfGeneratorService {
            public function testGenerateQrHash($pengajuan) {
                return $this->generateQrHash($pengajuan);
            }
        };

        $hash = $service->testGenerateQrHash($this->pengajuan);

        $this->assertEquals(64, strlen($hash));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $hash);
    }

    public function test_generate_nomor_surat_formats_correctly()
    {
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::create(2026, 7, 10));
        
        $service = new class extends PdfGeneratorService {
            public function testGenerateNomorSurat($pengajuan) {
                return $this->generateNomorSurat($pengajuan);
            }
        };

        $nomorSurat = $service->testGenerateNomorSurat($this->pengajuan);

        // Expecting 1 because it's the first in the year
        $this->assertEquals('SKD/001/DESA-SUKAMAKMUR/JULI/2026', $nomorSurat);
        
        \Carbon\Carbon::setTestNow();
    }

    public function test_generate_surat_pdf_creates_file_and_updates_model()
    {
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::create(2026, 7, 10));
        Storage::fake('public');
        
        $mockPdf = $this->mock(\Barryvdh\DomPDF\PDF::class);
        $mockPdf->shouldReceive('output')->andReturn('PDF CONTENT');
        Pdf::shouldReceive('loadHTML')->andReturn($mockPdf);
        
        Http::fake([
            'upload.wikimedia.org/*' => Http::response('LOGO CONTENT', 200)
        ]);
        
        view()->addLocation(__DIR__ . '/../../views');
        
        if (!is_dir(public_path('images'))) {
            @mkdir(public_path('images'), 0777, true);
        }
        
        // We mock the view render because we just want to test the service logic
        $mockView = $this->mock(\Illuminate\View\View::class);
        $mockView->shouldReceive('render')->andReturn('<html>Test</html>');
        
        \Illuminate\Support\Facades\View::shouldReceive('exists')
            ->andReturn(true);
        \Illuminate\Support\Facades\View::shouldReceive('make')
            ->andReturn($mockView);
            
        $service = new PdfGeneratorService();
        $url = $service->generateSuratPdf($this->pengajuan);

        $this->assertNotNull($this->pengajuan->fresh()->qr_hash);
        $this->assertNotNull($this->pengajuan->fresh()->nomor_surat);
        $this->assertNotNull($this->pengajuan->fresh()->file_pdf_url);
        
        $filename = 'surat_' . $this->pengajuan->nomor_registrasi;
        
        $files = Storage::disk('public')->files('surat');
        $this->assertCount(1, $files);
        $this->assertStringContainsString($filename, $files[0]);
        $this->assertEquals(Storage::disk('public')->url($files[0]), $url);
        
        \Carbon\Carbon::setTestNow();
    }
}

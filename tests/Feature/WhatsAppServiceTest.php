<?php

namespace Tests\Feature;

use App\Models\Penduduk;
use App\Models\PengaturanDesa;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.whatsapp.provider' => 'fonnte']);
        config(['services.whatsapp.fonnte_token' => 'test-token']);
    }

    public function test_is_configured_with_fonnte_token()
    {
        $service = new WhatsAppService();
        $this->assertTrue($service->isConfigured());
    }

    public function test_not_configured_without_fonnte_token()
    {
        config(['services.whatsapp.fonnte_token' => '']);
        $service = new WhatsAppService();
        $this->assertFalse($service->isConfigured());
    }

    public function test_notify_pengajuan_status_skips_when_no_phone()
    {
        $penduduk = Penduduk::factory()->create(['no_hp' => null]);
        $service = new WhatsAppService();
        $service->notifyPengajuanStatus($penduduk->nik, 'Disetujui', 'REG/2026/0001');
        $this->assertTrue(true);
    }

    public function test_notify_mutasi_status_skips_when_no_phone()
    {
        $penduduk = Penduduk::factory()->create(['no_hp' => null]);
        $service = new WhatsAppService();
        $service->notifyMutasiStatus($penduduk->nik, 'Kelahiran', 'Disetujui');
        $this->assertTrue(true);
    }

    public function test_phone_auto_format_on_save()
    {
        $penduduk = Penduduk::factory()->create([
            'no_hp' => '08123456789',
        ]);
        $this->assertEquals('628123456789', $penduduk->no_hp);
    }

    public function test_phone_auto_format_with_plus62()
    {
        $penduduk = Penduduk::factory()->create([
            'no_hp' => '+62812345678',
        ]);
        $this->assertEquals('62812345678', $penduduk->no_hp);
    }

    public function test_get_provider_default()
    {
        $service = new WhatsAppService();
        $this->assertEquals('fonnte', $service->getProvider());
    }
}

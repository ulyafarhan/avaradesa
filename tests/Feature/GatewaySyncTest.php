<?php

namespace Tests\Feature;

use App\Models\BotKnowledge;
use App\Models\PengaturanDesa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GatewaySyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.whatsapp.api_key' => 'test-sync-key']);
    }

    public function test_sync_returns_401_without_api_key()
    {
        $response = $this->getJson('/api/v1/gateway/sync');
        $response->assertStatus(401);
    }

    public function test_sync_returns_401_with_wrong_api_key()
    {
        $response = $this->getJson('/api/v1/gateway/sync', [
            'X-API-Key' => 'wrong-key',
        ]);
        $response->assertStatus(401);
    }

    public function test_sync_returns_faqs_and_templates()
    {
        BotKnowledge::create([
            'kunci' => 'sktm',
            'tipe' => 'faq',
            'is_aktif' => true,
            'kata_kunci' => json_encode(['sktm', 'tidak mampu']),
            'pertanyaan_atau_topik' => 'Cara buat SKTM',
            'jawaban_atau_konten' => 'Siapkan KTP dan KK',
        ]);

        PengaturanDesa::set('notif_wa_surat_disetujui', 'Surat {nomor} telah disetujui');

        $response = $this->getJson('/api/v1/gateway/sync', [
            'X-API-Key' => 'test-sync-key',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'faqs',
                    'kategori_surat',
                    'notif_templates',
                    'synced_at',
                ],
            ]);

        $this->assertCount(1, $response->json('data.faqs'));
        $this->assertArrayHasKey('notif_wa_surat_disetujui', $response->json('data.notif_templates'));
    }

    public function test_sync_returns_inactive_faqs_filtered()
    {
        BotKnowledge::create([
            'kunci' => 'inactive-faq',
            'tipe' => 'faq',
            'is_aktif' => false,
            'kata_kunci' => json_encode(['test']),
            'pertanyaan_atau_topik' => 'Inactive FAQ',
            'jawaban_atau_konten' => 'Should not appear',
        ]);

        $response = $this->getJson('/api/v1/gateway/sync', [
            'X-API-Key' => 'test-sync-key',
        ]);

        $response->assertOk();
        $this->assertCount(0, $response->json('data.faqs'));
    }
}

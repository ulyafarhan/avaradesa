<?php

namespace Tests\Feature;

use App\Models\Penduduk;
use App\Models\KategoriSurat;
use App\Models\PengajuanSurat;
use Database\Seeders\KategoriSuratSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncTest extends TestCase
{
    use RefreshDatabase;

    protected $warga;
    protected $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(KategoriSuratSeeder::class);
        $this->kategori = KategoriSurat::first();

        $this->warga = Penduduk::factory()->create();

        PengajuanSurat::factory()->count(3)->create([
            'nik_pemohon' => $this->warga->nik,
            'kategori_surat_id' => $this->kategori->id,
        ]);
    }

    public function test_sync_pull_requires_since_parameter()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/v1/sync/pull');

        $response->assertStatus(422);
    }

    public function test_sync_pull_returns_warga_data()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/v1/sync/pull?since=2020-01-01T00:00:00Z');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'pengajuan_surat' => ['updated', 'deleted'],
                'mutasi' => ['updated', 'deleted'],
                'penduduk',
            ],
            'meta' => ['sync_token'],
        ]);
    }

    public function test_sync_pull_filtered_by_nik()
    {
        $warga2 = Penduduk::factory()->create();
        PengajuanSurat::factory()->count(2)->create([
            'nik_pemohon' => $warga2->nik,
            'kategori_surat_id' => $this->kategori->id,
        ]);

        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/v1/sync/pull?since=2020-01-01T00:00:00Z');

        $response->assertOk();
        $data = $response->json('data.pengajuan_surat.updated');
        $this->assertCount(3, $data);
    }

    public function test_sync_push_requires_auth()
    {
        $response = $this->postJson('/api/v1/sync/push', [
            'operations' => [],
        ]);

        $response->assertStatus(401);
    }

    public function test_sync_push_creates_pengajuan()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/sync/push', [
                'operations' => [
                    [
                        'client_id' => '550e8400-e29b-41d4-a716-446655440000',
                        'type' => 'pengajuan_surat',
                        'action' => 'create',
                        'data' => [
                            'kategori_surat_id' => $this->kategori->id,
                            'data_isian' => ['keperluan' => 'Test sync'],
                        ],
                        'created_at' => '2026-07-14T10:00:00Z',
                    ],
                ],
            ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'processed',
        ]);
        $this->assertDatabaseHas('pengajuan_surat', [
            'nik_pemohon' => $this->warga->nik,
        ]);
    }

    public function test_sync_push_rejects_unauthorized()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/sync/push', [
                'operations' => [
                    [
                        'client_id' => '550e8400-e29b-41d4-a716-446655440001',
                        'type' => 'pengajuan_surat',
                        'action' => 'delete',
                        'data' => [],
                        'created_at' => '2026-07-14T10:00:00Z',
                    ],
                ],
            ]);

        $response->assertStatus(422);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\InformasiPublik;
use Tests\TestCase;

class InformasiPublikTest extends TestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Administrator::factory()->operator()->create();
    }

    public function test_can_get_published_informasi()
    {
        InformasiPublik::factory()->create(['is_published' => true]);

        $response = $this->getJson('/api/v1/informasi');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_draft_not_shown_in_public()
    {
        InformasiPublik::factory()->draft()->create();

        $response = $this->getJson('/api/v1/informasi');

        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));
    }

    public function test_can_get_informasi_by_slug()
    {
        $info = InformasiPublik::factory()->create(['is_published' => true]);

        $response = $this->getJson("/api/v1/informasi/{$info->slug}");

        $response->assertStatus(200)
            ->assertJsonPath('data.judul', $info->judul);
    }

    public function test_returns_404_for_non_existent_slug()
    {
        $response = $this->getJson('/api/v1/informasi/non-existent-slug');

        $response->assertStatus(404);
    }

    public function test_returns_404_for_draft_slug()
    {
        $info = InformasiPublik::factory()->draft()->create();

        $response = $this->getJson("/api/v1/informasi/{$info->slug}");

        $response->assertStatus(404);
    }

    public function test_admin_can_create_informasi()
    {
        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/admin/informasi', [
                'judul' => 'Pengumuman Penting',
                'konten' => 'Ini adalah pengumuman penting untuk warga.',
                'kategori' => 'Pengumuman',
                'is_published' => true,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'data']);
    }

    public function test_admin_cannot_create_without_judul()
    {
        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/admin/informasi', [
                'konten' => 'Test konten',
                'kategori' => 'Berita',
            ]);

        $response->assertStatus(422);
    }

    public function test_admin_can_update_informasi()
    {
        $info = InformasiPublik::factory()->create(['author_id' => $this->admin->id]);

        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $response = $this->withToken($token)
            ->putJson("/api/v1/admin/informasi/{$info->id}", [
                'judul' => 'Judul Diperbarui',
            ]);

        $response->assertStatus(200);
    }

    public function test_admin_can_delete_informasi()
    {
        $adminKepalaDesa = Administrator::factory()->kepalaDesa()->create();
        $info = InformasiPublik::factory()->create(['author_id' => $adminKepalaDesa->id]);

        $token = $adminKepalaDesa->createToken('test', ['admin'])->plainTextToken;

        $response = $this->withToken($token)
            ->deleteJson("/api/v1/admin/informasi/{$info->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('informasi_publik', ['id' => $info->id]);
    }

    public function test_can_filter_informasi_by_kategori()
    {
        InformasiPublik::factory()->create([
            'is_published' => true,
            'kategori' => 'Berita',
        ]);
        InformasiPublik::factory()->create([
            'is_published' => true,
            'kategori' => 'Pengumuman',
        ]);

        $response = $this->getJson('/api/v1/informasi?kategori=Berita');

        $response->assertStatus(200);
    }

    public function test_guest_cannot_create_informasi()
    {
        $response = $this->postJson('/api/v1/admin/informasi', [
            'judul' => 'Test',
            'konten' => 'Test',
            'kategori' => 'Berita',
        ]);

        $response->assertStatus(401);
    }

    public function test_warga_cannot_create_informasi()
    {
        $keluarga = \App\Models\Keluarga::factory()->create();
        $warga = \App\Models\Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);
        $token = $warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/admin/informasi', [
                'judul' => 'Test',
                'konten' => 'Test',
                'kategori' => 'Berita',
            ]);

        $response->assertStatus(403);
    }

    public function test_auto_slug_generation()
    {
        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/admin/informasi', [
                'judul' => 'Pengumuman Baru 2026',
                'konten' => 'Konten pengumuman',
                'kategori' => 'Pengumuman',
            ]);

        $response->assertStatus(201);
        $this->assertNotNull($response->json('data.slug'));
    }
}

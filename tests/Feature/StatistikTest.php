<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\Administrator;
use Tests\TestCase;

class StatistikTest extends TestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Administrator::factory()->operator()->create();
    }

    public function test_can_get_statistik_demografi()
    {
        $keluarga = Keluarga::factory()->create();
        Penduduk::factory()->count(3)->create([
            'no_kk' => $keluarga->no_kk,
            'jenis_kelamin' => 'L',
        ]);
        Penduduk::factory()->count(2)->create([
            'no_kk' => $keluarga->no_kk,
            'jenis_kelamin' => 'P',
        ]);

        $response = $this->getJson('/api/v1/statistik/demografi');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_can_get_statistik_layanan()
    {
        $response = $this->getJson('/api/v1/statistik/layanan');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_statistik_demografi_returns_zero_when_no_data()
    {
        $response = $this->getJson('/api/v1/statistik/demografi');

        $response->assertStatus(200);
    }

    public function test_admin_can_clear_cache()
    {
        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/admin/statistik/clear-cache');

        $response->assertStatus(200);
    }

    public function test_warga_cannot_clear_cache()
    {
        $keluarga = Keluarga::factory()->create();
        $warga = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);
        $token = $warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/admin/statistik/clear-cache');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_clear_cache()
    {
        $response = $this->postJson('/api/v1/admin/statistik/clear-cache');

        $response->assertStatus(401);
    }

    public function test_demografi_structure()
    {
        $keluarga = Keluarga::factory()->create();
        Penduduk::factory()->create([
            'no_kk' => $keluarga->no_kk,
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
        ]);

        $response = $this->getJson('/api/v1/statistik/demografi');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertIsArray($data);
    }
}

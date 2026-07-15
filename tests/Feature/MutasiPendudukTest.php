<?php

namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\MutasiPenduduk;
use Tests\TestCase;

class MutasiPendudukTest extends TestCase
{
    protected $warga;
    protected $warga2;
    protected $admin;
    protected $admin2;

    protected function setUp(): void
    {
        parent::setUp();

        $keluarga = Keluarga::factory()->create();
        $keluarga2 = Keluarga::factory()->create();
        $keluarga3 = Keluarga::factory()->create();

        $this->warga = Penduduk::factory()->create([
            'no_kk' => $keluarga->no_kk,
            'status_keluarga' => 'Kepala Keluarga',
        ]);
        $this->warga2 = Penduduk::factory()->create(['no_kk' => $keluarga2->no_kk]);
        $this->admin = Administrator::factory()->operator()->create();
        $this->admin2 = Administrator::factory()->sekdes()->create();
    }

    public function test_warga_can_submit_mutasi()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/mutasi', [
                'nik' => $this->warga->nik,
                'jenis_mutasi' => 'Kepindahan',
                'tanggal_mutasi' => '2026-07-01',
                'keterangan' => 'Pindah ke luar kota untuk bekerja',
                'dokumen_bukti' => 'uploads/bukti/pindah.pdf',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'data']);

        $this->assertDatabaseHas('mutasi_penduduk', [
            'nik' => $this->warga->nik,
            'jenis_mutasi' => 'Kepindahan',
            'status_verifikasi' => 'Pending',
        ]);
    }

    public function test_warga_cannot_submit_mutasi_for_other_nik()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/mutasi', [
                'nik' => $this->warga2->nik,
                'jenis_mutasi' => 'Kepindahan',
                'tanggal_mutasi' => '2026-07-01',
                'keterangan' => 'Pindah',
                'dokumen_bukti' => 'uploads/bukti.pdf',
            ]);

        $response->assertStatus(403);
    }

    public function test_warga_cannot_submit_with_invalid_jenis()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/mutasi', [
                'nik' => $this->warga->nik,
                'jenis_mutasi' => 'InvalidJenis',
                'tanggal_mutasi' => '2026-07-01',
                'keterangan' => 'Test',
                'dokumen_bukti' => 'uploads/bukti.pdf',
            ]);

        $response->assertStatus(422);
    }

    public function test_warga_cannot_submit_future_date()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/mutasi', [
                'nik' => $this->warga->nik,
                'jenis_mutasi' => 'Kelahiran',
                'tanggal_mutasi' => '2099-01-01',
                'keterangan' => 'Test',
                'dokumen_bukti' => 'uploads/bukti.pdf',
            ]);

        $response->assertStatus(422);
    }

    public function test_warga_cannot_submit_without_keterangan()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/mutasi', [
                'nik' => $this->warga->nik,
                'jenis_mutasi' => 'Kepindahan',
                'tanggal_mutasi' => '2026-07-01',
                'keterangan' => '',
                'dokumen_bukti' => 'uploads/bukti.pdf',
            ]);

        $response->assertStatus(422);
    }

    public function test_warga_can_get_their_mutasi()
    {
        MutasiPenduduk::factory()->create(['nik' => $this->warga->nik]);

        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/v1/mutasi');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_warga_only_sees_own_mutasi()
    {
        MutasiPenduduk::factory()->create(['nik' => $this->warga->nik]);
        MutasiPenduduk::factory()->create(['nik' => $this->warga2->nik]);

        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/v1/mutasi');

        $response->assertStatus(200);

        foreach ($response->json('data') as $item) {
            $this->assertEquals($this->warga->nik, $item['nik']);
        }
    }

    public function test_admin_can_get_all_mutasi()
    {
        MutasiPenduduk::factory()->create(['nik' => $this->warga->nik]);

        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/v1/admin/mutasi');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_admin_can_filter_mutasi_by_status()
    {
        MutasiPenduduk::factory()->create(['nik' => $this->warga->nik, 'status_verifikasi' => 'Pending']);
        MutasiPenduduk::factory()->disetujui()->create(['nik' => $this->warga->nik]);

        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/v1/admin/mutasi?status_verifikasi=Disetujui');

        $response->assertStatus(200);
    }

    public function test_admin_can_filter_mutasi_by_jenis()
    {
        MutasiPenduduk::factory()->create([
            'nik' => $this->warga->nik,
            'jenis_mutasi' => 'Kelahiran',
        ]);
        MutasiPenduduk::factory()->create([
            'nik' => $this->warga->nik,
            'jenis_mutasi' => 'Kematian',
        ]);

        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/v1/admin/mutasi?jenis_mutasi=Kelahiran');

        $response->assertStatus(200);
    }

    public function test_admin_can_approve_mutasi()
    {
        $mutasi = MutasiPenduduk::factory()->create([
            'nik' => $this->warga->nik,
            'jenis_mutasi' => 'Kedatangan',
        ]);

        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson("/api/v1/admin/mutasi/{$mutasi->id}/approve");

        $response->assertStatus(200);

        $this->assertDatabaseHas('mutasi_penduduk', [
            'id' => $mutasi->id,
            'status_verifikasi' => 'Disetujui',
            'diverifikasi_oleh' => $this->admin->id,
        ]);
    }

    public function test_admin_can_reject_mutasi()
    {
        $mutasi = MutasiPenduduk::factory()->create([
            'nik' => $this->warga->nik,
            'jenis_mutasi' => 'Kedatangan',
        ]);

        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson("/api/v1/admin/mutasi/{$mutasi->id}/reject");

        $response->assertStatus(200);

        $this->assertDatabaseHas('mutasi_penduduk', [
            'id' => $mutasi->id,
            'status_verifikasi' => 'Ditolak',
        ]);
    }

    public function test_approve_kematian_updates_penduduk_status()
    {
        $mutasi = MutasiPenduduk::factory()->create([
            'nik' => $this->warga->nik,
            'jenis_mutasi' => 'Kematian',
        ]);

        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $this->withToken($token)
            ->postJson("/api/v1/admin/mutasi/{$mutasi->id}/approve");

        $this->assertDatabaseHas('penduduk', [
            'nik' => $this->warga->nik,
            'status_mutasi' => 'Meninggal',
        ]);
    }

    public function test_approve_kepindahan_updates_penduduk_status()
    {
        $mutasi = MutasiPenduduk::factory()->create([
            'nik' => $this->warga->nik,
            'jenis_mutasi' => 'Kepindahan',
        ]);

        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $this->withToken($token)
            ->postJson("/api/v1/admin/mutasi/{$mutasi->id}/approve");

        $this->assertDatabaseHas('penduduk', [
            'nik' => $this->warga->nik,
            'status_mutasi' => 'Pindah',
        ]);
    }

    public function test_approve_kelahiran_does_not_change_status()
    {
        $mutasi = MutasiPenduduk::factory()->create([
            'nik' => $this->warga->nik,
            'jenis_mutasi' => 'Kelahiran',
        ]);

        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $this->withToken($token)
            ->postJson("/api/v1/admin/mutasi/{$mutasi->id}/approve");

        $this->assertDatabaseHas('penduduk', [
            'nik' => $this->warga->nik,
            'status_mutasi' => 'Tetap',
        ]);
    }

    public function test_warga_cannot_approve_mutasi()
    {
        $mutasi = MutasiPenduduk::factory()->create(['nik' => $this->warga->nik]);

        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson("/api/v1/admin/mutasi/{$mutasi->id}/approve");

        $response->assertStatus(403);
    }

    public function test_warga_cannot_reject_mutasi()
    {
        $mutasi = MutasiPenduduk::factory()->create(['nik' => $this->warga->nik]);

        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson("/api/v1/admin/mutasi/{$mutasi->id}/reject");

        $response->assertStatus(403);
    }
}

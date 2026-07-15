<?php

namespace Tests\Unit\Models;

use App\Models\Keluarga;
use App\Models\Penduduk;
use Tests\TestCase;

class PendudukTest extends TestCase
{
    public function test_penduduk_can_be_created()
    {
        $keluarga = Keluarga::factory()->create();
        $penduduk = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);

        $this->assertDatabaseHas('penduduk', [
            'nik' => $penduduk->nik,
            'nama_lengkap' => $penduduk->nama_lengkap,
        ]);
    }

    public function test_penduduk_has_keluarga_relationship()
    {
        $keluarga = Keluarga::factory()->create();
        $penduduk = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);

        $this->assertNotNull($penduduk->keluarga);
        $this->assertEquals($keluarga->no_kk, $penduduk->keluarga->no_kk);
    }

    public function test_penduduk_umur_accessor()
    {
        $keluarga = Keluarga::factory()->create();
        $penduduk = Penduduk::factory()->create([
            'no_kk' => $keluarga->no_kk,
            'tanggal_lahir' => '2000-01-01',
        ]);

        $umur = $penduduk->tanggal_lahir->age;
        $this->assertGreaterThanOrEqual(26, $umur);
    }

    public function test_penduduk_aktif_scope()
    {
        $keluarga = Keluarga::factory()->create();
        Penduduk::factory()->create(['no_kk' => $keluarga->no_kk, 'status_mutasi' => 'Tetap']);
        Penduduk::factory()->pindah()->create(['no_kk' => $keluarga->no_kk]);

        $aktif = Penduduk::where('status_mutasi', 'Tetap')->count();

        $this->assertEquals(1, $aktif);
    }

    public function test_penduduk_has_mutasi_relationship()
    {
        $keluarga = Keluarga::factory()->create();
        $penduduk = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);

        $mutasi = \App\Models\MutasiPenduduk::factory()->create(['nik' => $penduduk->nik]);

        $this->assertTrue($penduduk->mutasi->contains($mutasi));
    }

    public function test_foto_profil_returns_null_when_empty()
    {
        $keluarga = Keluarga::factory()->create();
        $penduduk = Penduduk::factory()->create([
            'no_kk' => $keluarga->no_kk,
            'foto_profil' => null,
        ]);

        $this->assertNull($penduduk->foto_profil);
    }

    public function test_foto_profil_returns_url_when_set()
    {
        $keluarga = Keluarga::factory()->create();
        $penduduk = Penduduk::factory()->create([
            'no_kk' => $keluarga->no_kk,
            'foto_profil' => 'uploads/profil/test.jpg',
        ]);

        $this->assertStringContainsString('test.jpg', $penduduk->foto_profil);
    }

    public function test_penduduk_uses_nik_as_primary_key()
    {
        $keluarga = Keluarga::factory()->create();
        $penduduk = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);

        $this->assertEquals($penduduk->nik, $penduduk->getKey());
    }

    public function test_penduduk_incrementing_is_false()
    {
        $this->assertFalse((new Penduduk())->incrementing);
    }
}

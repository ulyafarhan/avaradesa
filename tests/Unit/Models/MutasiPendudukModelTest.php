<?php

namespace Tests\Unit\Models;

use App\Models\Keluarga;
use App\Models\MutasiPenduduk;
use App\Models\Penduduk;
use Tests\TestCase;

class MutasiPendudukModelTest extends TestCase
{
    public function test_mutasi_can_be_created()
    {
        $keluarga = Keluarga::factory()->create();
        $warga = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);
        $mutasi = MutasiPenduduk::factory()->create(['nik' => $warga->nik]);

        $this->assertDatabaseHas('mutasi_penduduk', [
            'id' => $mutasi->id,
            'nik' => $warga->nik,
        ]);
    }

    public function test_mutasi_has_penduduk_relationship()
    {
        $keluarga = Keluarga::factory()->create();
        $warga = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);
        $mutasi = MutasiPenduduk::factory()->create(['nik' => $warga->nik]);

        $this->assertNotNull($mutasi->penduduk);
        $this->assertEquals($warga->nik, $mutasi->penduduk->nik);
    }

    public function test_mutasi_pending_scope()
    {
        $keluarga = Keluarga::factory()->create();
        $warga = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);

        MutasiPenduduk::factory()->create(['nik' => $warga->nik, 'status_verifikasi' => 'Pending']);
        MutasiPenduduk::factory()->disetujui()->create(['nik' => $warga->nik]);

        $this->assertEquals(1, MutasiPenduduk::pending()->count());
    }

    public function test_mutasi_jenis_is_enum()
    {
        $keluarga = Keluarga::factory()->create();
        $warga = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);
        $mutasi = MutasiPenduduk::factory()->create([
            'nik' => $warga->nik,
            'jenis_mutasi' => 'Kelahiran',
        ]);

        $this->assertEquals('Kelahiran', $mutasi->jenis_mutasi);
    }

    public function test_mutasi_status_default_is_pending()
    {
        $keluarga = Keluarga::factory()->create();
        $warga = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);
        $mutasi = MutasiPenduduk::factory()->create(['nik' => $warga->nik]);

        $this->assertEquals('Pending', $mutasi->status_verifikasi);
    }

    public function test_mutasi_tanggal_is_date()
    {
        $keluarga = Keluarga::factory()->create();
        $warga = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);
        $mutasi = MutasiPenduduk::factory()->create(['nik' => $warga->nik]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $mutasi->tanggal_mutasi);
    }
}

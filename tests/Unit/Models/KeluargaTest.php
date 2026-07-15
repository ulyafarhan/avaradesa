<?php

namespace Tests\Unit\Models;

use App\Models\Keluarga;
use App\Models\Penduduk;
use Tests\TestCase;

class KeluargaTest extends TestCase
{
    public function test_keluarga_can_be_created()
    {
        $keluarga = Keluarga::factory()->create();

        $this->assertDatabaseHas('keluarga', [
            'no_kk' => $keluarga->no_kk,
        ]);
    }

    public function test_keluarga_has_anggota_relationship()
    {
        $keluarga = Keluarga::factory()->create();
        $penduduk = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);

        $this->assertTrue($keluarga->anggota->contains($penduduk));
    }

    public function test_keluarga_can_have_multiple_anggota()
    {
        $keluarga = Keluarga::factory()->create();
        Penduduk::factory()->count(3)->create(['no_kk' => $keluarga->no_kk]);

        $this->assertEquals(3, $keluarga->anggota->count());
    }

    public function test_keluarga_can_filter_by_dusun()
    {
        Keluarga::factory()->create(['dusun' => 'Dusun A']);
        Keluarga::factory()->create(['dusun' => 'Dusun A']);
        Keluarga::factory()->create(['dusun' => 'Dusun B']);

        $count = Keluarga::byDusun('Dusun A')->count();

        $this->assertEquals(2, $count);
    }

    public function test_keluarga_uses_no_kk_as_primary_key()
    {
        $keluarga = Keluarga::factory()->create();

        $this->assertEquals($keluarga->no_kk, $keluarga->getKey());
    }

    public function test_keluarga_timestamps_are_enabled()
    {
        $this->assertTrue((new Keluarga())->usesTimestamps());
    }
}

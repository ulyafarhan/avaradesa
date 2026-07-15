<?php

namespace Tests\Unit\Models;

use App\Models\KategoriSurat;
use Tests\TestCase;

class KategoriSuratTest extends TestCase
{
    public function test_kategori_can_be_created()
    {
        $kategori = KategoriSurat::factory()->create();

        $this->assertDatabaseHas('kategori_surat', [
            'id' => $kategori->id,
            'kode_surat' => $kategori->kode_surat,
        ]);
    }

    public function test_kategori_active_scope()
    {
        KategoriSurat::factory()->create(['is_active' => true]);
        KategoriSurat::factory()->nonaktif()->create(['is_active' => false]);

        $this->assertEquals(1, KategoriSurat::active()->count());
    }

    public function test_kategori_schema_isian_is_array()
    {
        $kategori = KategoriSurat::factory()->create();

        $this->assertIsArray($kategori->schema_isian);
    }

    public function test_kategori_syarat_dokumen_is_array()
    {
        $kategori = KategoriSurat::factory()->create();

        $this->assertIsArray($kategori->syarat_dokumen);
    }

    public function test_kode_surat_is_unique()
    {
        KategoriSurat::factory()->create(['kode_surat' => 'XYZ']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        KategoriSurat::factory()->create(['kode_surat' => 'XYZ']);
    }

    public function test_kategori_is_active_by_default()
    {
        $kategori = KategoriSurat::factory()->create();

        $this->assertTrue($kategori->is_active);
    }

    public function test_kategori_has_pengajuan_relationship()
    {
        $kategori = KategoriSurat::factory()->create();

        $this->assertNotNull($kategori->pengajuan);
        $this->assertCount(0, $kategori->pengajuan);
    }
}

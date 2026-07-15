<?php

namespace Tests\Unit\Models;

use App\Models\KategoriSurat;
use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use Database\Seeders\KategoriSuratSeeder;
use Tests\TestCase;

class PengajuanSuratModelTest extends TestCase
{
    protected $warga;
    protected $kategori;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(KategoriSuratSeeder::class);
        $this->kategori = KategoriSurat::first();
        $keluarga = Keluarga::factory()->create();
        $this->warga = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);
    }

    public function test_pengajuan_surat_auto_generates_nomor_registrasi()
    {
        $pengajuan = PengajuanSurat::factory()->create([
            'nik_pemohon' => $this->warga->nik,
            'kategori_surat_id' => $this->kategori->id,
        ]);

        $this->assertNotNull($pengajuan->nomor_registrasi);
        $this->assertStringStartsWith(date('Ymd'), $pengajuan->nomor_registrasi);
    }

    public function test_pengajuan_surat_has_relationships()
    {
        $pengajuan = PengajuanSurat::factory()->create([
            'nik_pemohon' => $this->warga->nik,
            'kategori_surat_id' => $this->kategori->id,
        ]);

        $this->assertNotNull($pengajuan->pemohon);
        $this->assertNotNull($pengajuan->kategori);
        $this->assertEquals($this->warga->nik, $pengajuan->pemohon->nik);
    }

    public function test_pengajuan_surat_pending_scope()
    {
        PengajuanSurat::factory()->create([
            'nik_pemohon' => $this->warga->nik,
            'kategori_surat_id' => $this->kategori->id,
            'status' => 'Pending',
        ]);
        PengajuanSurat::factory()->disetujui()->create([
            'nik_pemohon' => $this->warga->nik,
            'kategori_surat_id' => $this->kategori->id,
        ]);

        $count = PengajuanSurat::pending()->count();

        $this->assertEquals(1, $count);
    }

    public function test_pengajuan_surat_diproses_scope()
    {
        $pengajuan = PengajuanSurat::factory()->create([
            'nik_pemohon' => $this->warga->nik,
            'kategori_surat_id' => $this->kategori->id,
        ]);
        $pengajuan->update(['status' => 'Diproses']);

        $this->assertEquals(1, PengajuanSurat::diproses()->count());
    }

    public function test_pengajuan_has_tracking()
    {
        $pengajuan = PengajuanSurat::factory()->create([
            'nik_pemohon' => $this->warga->nik,
            'kategori_surat_id' => $this->kategori->id,
        ]);

        \App\Models\TrackingPengajuanSurat::create([
            'pengajuan_surat_id' => $pengajuan->id,
            'status_baru' => 'Pending',
            'keterangan_update' => 'Dibuat',
        ]);

        $this->assertEquals(1, $pengajuan->tracking->count());
    }

    public function test_pengajuan_status_default_is_pending()
    {
        $pengajuan = PengajuanSurat::factory()->create([
            'nik_pemohon' => $this->warga->nik,
            'kategori_surat_id' => $this->kategori->id,
        ]);

        $this->assertEquals('Pending', $pengajuan->status);
    }

    public function test_data_isian_is_array()
    {
        $data = ['keperluan' => 'Test', 'lama_tinggal' => 5];
        $pengajuan = PengajuanSurat::factory()->create([
            'nik_pemohon' => $this->warga->nik,
            'kategori_surat_id' => $this->kategori->id,
            'data_isian' => $data,
        ]);

        $this->assertIsArray($pengajuan->data_isian);
        $this->assertEquals('Test', $pengajuan->data_isian['keperluan']);
    }
}

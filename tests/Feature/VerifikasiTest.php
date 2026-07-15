<?php

namespace Tests\Feature;

use App\Models\KategoriSurat;
use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use Database\Seeders\KategoriSuratSeeder;
use Tests\TestCase;

class VerifikasiTest extends TestCase
{
    protected $pengajuan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(KategoriSuratSeeder::class);
        $kategori = KategoriSurat::first();
        $keluarga = Keluarga::factory()->create();
        $warga = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);

        $this->pengajuan = PengajuanSurat::factory()->create([
            'nik_pemohon' => $warga->nik,
            'kategori_surat_id' => $kategori->id,
            'status' => 'Selesai',
            'qr_hash' => 'test_hash_12345',
        ]);
    }

    public function test_can_verify_valid_qr_hash()
    {
        $response = $this->getJson("/api/v1/verifikasi/{$this->pengajuan->qr_hash}");

        $response->assertStatus(200)
            ->assertJson([
                'valid' => true,
            ]);
    }

    public function test_returns_invalid_for_non_existent_hash()
    {
        $response = $this->getJson('/api/v1/verifikasi/non_existent_hash');

        $response->assertStatus(404)
            ->assertJson(['valid' => false]);
    }

    public function test_returns_invalid_for_unfinished_document()
    {
        $keluarga = Keluarga::factory()->create();
        $warga = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);
        $kategori = KategoriSurat::first();

        $pengajuan = PengajuanSurat::factory()->create([
            'nik_pemohon' => $warga->nik,
            'kategori_surat_id' => $kategori->id,
            'status' => 'Pending',
            'qr_hash' => 'pending_hash',
        ]);

        $response = $this->getJson("/api/v1/verifikasi/{$pengajuan->qr_hash}");

        $response->assertStatus(200)
            ->assertJson(['valid' => false]);
    }

    public function test_returns_document_data_on_valid_hash()
    {
        $response = $this->getJson("/api/v1/verifikasi/{$this->pengajuan->qr_hash}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'valid',
                'message',
                'data' => [
                    'nomor_registrasi',
                    'jenis_surat',
                    'nama_pemohon',
                    'nik_pemohon',
                    'tanggal_terbit',
                ],
            ]);
    }

    public function test_nik_masked_or_shown_correctly()
    {
        $response = $this->getJson("/api/v1/verifikasi/{$this->pengajuan->qr_hash}");

        $response->assertStatus(200);
        $nik = $response->json('data.nik_pemohon');
        $this->assertEquals(16, strlen($nik));
    }
}

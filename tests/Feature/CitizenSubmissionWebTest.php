<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\KategoriSurat;
use App\Models\PengajuanSurat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CitizenSubmissionWebTest extends TestCase
{
    use RefreshDatabase;

    protected $warga;
    protected $keluarga;
    protected $kategori;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');

        $this->keluarga = Keluarga::factory()->create(['dusun' => 'Dusun Sejahtera']);
        $this->warga = Penduduk::factory()->create([
            'no_kk' => $this->keluarga->no_kk,
            'status_mutasi' => 'Tetap'
        ]);

        $this->kategori = KategoriSurat::factory()->create([
            'nama_surat' => 'Surat Keterangan Domisili',
            'kode_surat' => 'SKD',
            'schema_isian' => [
                ['field' => 'keperluan', 'label' => 'Keperluan', 'type' => 'text', 'required' => true]
            ],
            'syarat_dokumen' => ['KTP']
        ]);
    }

    public function test_warga_can_access_submission_form()
    {
        $response = $this->actingAs($this->warga, 'penduduk')
            ->get("/warga/surat/ajukan/{$this->kategori->id}");

        $response->assertStatus(200);
    }

    public function test_warga_can_submit_web_application_successfully()
    {
        $file = UploadedFile::fake()->image('ktp_doc.jpg');

        $response = $this->actingAs($this->warga, 'penduduk')
            ->post('/warga/surat/pengajuan', [
                'kategori_surat_id' => $this->kategori->id,
                'nik_pemohon' => $this->warga->nik,
                'data_isian' => [
                    'keperluan' => 'Membuka Rekening Bank'
                ],
                'file_syarat' => [
                    'ktp' => $file
                ]
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('pengajuan_surat', [
            'nik_pemohon' => $this->warga->nik,
            'kategori_surat_id' => $this->kategori->id,
            'status' => 'Pending'
        ]);
    }

    public function test_warga_validation_errors_when_missing_required_isian()
    {
        $response = $this->actingAs($this->warga, 'penduduk')
            ->from("/warga/surat/ajukan/{$this->kategori->id}")
            ->post('/warga/surat/pengajuan', [
                'kategori_surat_id' => $this->kategori->id,
                'nik_pemohon' => $this->warga->nik,
                'file_syarat' => [
                    'ktp' => UploadedFile::fake()->image('ktp.jpg')
                ],
                'data_isian' => [
                    'keperluan' => ''
                ]
            ]);

        $response->assertRedirect("/warga/surat/ajukan/{$this->kategori->id}");
        $response->assertSessionHasErrors('data_isian.keperluan');
    }

    public function test_warga_cannot_print_unfinished_letter()
    {
        $pengajuan = PengajuanSurat::create([
            'nik_pemohon' => $this->warga->nik,
            'kategori_surat_id' => $this->kategori->id,
            'data_isian' => ['keperluan' => 'Test'],
            'file_syarat' => ['ktp' => 'path.jpg'],
            'status' => 'Pending'
        ]);

        $response = $this->actingAs($this->warga, 'penduduk')
            ->get("/warga/pengajuan/{$pengajuan->id}/print");

        $response->assertStatus(404);
    }

    public function test_warga_can_print_completed_letter()
    {
        $pengajuan = PengajuanSurat::create([
            'nik_pemohon' => $this->warga->nik,
            'kategori_surat_id' => $this->kategori->id,
            'data_isian' => ['keperluan' => 'Test'],
            'file_syarat' => ['ktp' => 'path.jpg'],
            'status' => 'Selesai',
            'qr_hash' => 'dummy_qr_hash_code',
            'nomor_surat' => 'SKD/001/2026'
        ]);

        $response = $this->actingAs($this->warga, 'penduduk')
            ->get("/warga/pengajuan/{$pengajuan->id}/print");

        $response->assertStatus(200);
    }
}

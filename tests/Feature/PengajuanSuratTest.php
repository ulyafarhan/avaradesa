<?php

namespace Tests\Feature;

use App\Jobs\GenerateSuratPdfJob;
use App\Models\Administrator;
use App\Models\KategoriSurat;
use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use Database\Seeders\KategoriSuratSeeder;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

use Illuminate\Http\UploadedFile;

class PengajuanSuratTest extends TestCase
{
    protected $warga;
    protected $warga2;
    protected $admin;
    protected $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(KategoriSuratSeeder::class);
        $this->kategori = KategoriSurat::first();

        $keluarga = Keluarga::factory()->create();
        $keluarga2 = Keluarga::factory()->create();

        $this->warga = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);
        $this->warga2 = Penduduk::factory()->create(['no_kk' => $keluarga2->no_kk]);
        $this->admin = Administrator::factory()->operator()->create();
    }

    public function test_warga_can_get_kategori_surat()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/v1/surat/kategori');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_warga_can_get_kategori_detail()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson("/api/v1/surat/kategori/{$this->kategori->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_inactive_kategori_not_in_list()
    {
        $nonaktif = KategoriSurat::factory()->nonaktif()->create();
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/v1/surat/kategori');

        $response->assertStatus(200)
            ->assertJsonMissing(['id' => $nonaktif->id]);
    }

    public function test_warga_can_submit_pengajuan_surat()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/surat/pengajuan', [
                'kategori_surat_id' => $this->kategori->id,
                'data_isian' => ['keperluan' => 'Melamar pekerjaan', 'lama_tinggal' => '10'],
                'file_syarat' => ['ktp' => UploadedFile::fake()->image('ktp.jpg'), 'kk' => UploadedFile::fake()->image('kk.jpg')],
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'data' => ['id', 'nomor_registrasi', 'status']]);

        $this->assertDatabaseHas('pengajuan_surat', [
            'nik_pemohon' => $this->warga->nik,
            'status' => 'Pending',
        ]);
    }

    public function test_warga_cannot_submit_without_kategori()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/surat/pengajuan', [
                'data_isian' => ['keperluan' => 'Test'],
                'file_syarat' => ['ktp' => UploadedFile::fake()->image('ktp.jpg')],
            ]);

        $response->assertStatus(422);
    }

    public function test_warga_cannot_submit_with_invalid_kategori()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/surat/pengajuan', [
                'kategori_surat_id' => 'non-existent-id',
                'data_isian' => ['keperluan' => 'Test'],
                'file_syarat' => ['ktp' => UploadedFile::fake()->image('ktp.jpg')],
            ]);

        $response->assertStatus(422);
    }

    public function test_warga_can_get_their_pengajuan()
    {
        PengajuanSurat::factory()->create(['nik_pemohon' => $this->warga->nik]);

        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/v1/surat/pengajuan');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_warga_empty_list_when_no_pengajuan()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/v1/surat/pengajuan');

        $response->assertStatus(200);
    }

    public function test_warga_can_view_own_pengajuan_detail()
    {
        $pengajuan = PengajuanSurat::factory()->create(['nik_pemohon' => $this->warga->nik]);

        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson("/api/v1/surat/pengajuan/{$pengajuan->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_admin_can_get_all_pengajuan()
    {
        PengajuanSurat::factory()->create(['nik_pemohon' => $this->warga->nik]);

        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/v1/admin/surat/pengajuan');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_admin_can_filter_pengajuan_by_status()
    {
        PengajuanSurat::factory()->create(['nik_pemohon' => $this->warga->nik, 'status' => 'Pending']);
        PengajuanSurat::factory()->disetujui()->create(['nik_pemohon' => $this->warga->nik]);

        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/v1/admin/surat/pengajuan?status=Disetujui');

        $response->assertStatus(200);
    }

    public function test_admin_can_approve_pengajuan()
    {
        Queue::fake();

        $pengajuan = PengajuanSurat::factory()->create([
            'nik_pemohon' => $this->warga->nik,
            'kategori_surat_id' => $this->kategori->id,
        ]);

        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson("/api/v1/admin/surat/pengajuan/{$pengajuan->id}/approve");

        $response->assertStatus(200);

        $this->assertDatabaseHas('pengajuan_surat', [
            'id' => $pengajuan->id,
            'status' => 'Disetujui',
            'diverifikasi_oleh' => $this->admin->id,
        ]);

        Queue::assertPushed(GenerateSuratPdfJob::class);
    }

    public function test_admin_can_reject_pengajuan()
    {
        $pengajuan = PengajuanSurat::factory()->create([
            'nik_pemohon' => $this->warga->nik,
            'kategori_surat_id' => $this->kategori->id,
        ]);

        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson("/api/v1/admin/surat/pengajuan/{$pengajuan->id}/reject", [
                'catatan_penolakan' => 'Dokumen tidak lengkap',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('pengajuan_surat', [
            'id' => $pengajuan->id,
            'status' => 'Ditolak',
            'catatan_penolakan' => 'Dokumen tidak lengkap',
        ]);
    }

    public function test_admin_cannot_reject_without_catatan()
    {
        $pengajuan = PengajuanSurat::factory()->create([
            'nik_pemohon' => $this->warga->nik,
            'kategori_surat_id' => $this->kategori->id,
        ]);

        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson("/api/v1/admin/surat/pengajuan/{$pengajuan->id}/reject");

        $response->assertStatus(422);
    }

    public function test_warga_cannot_approve_pengajuan()
    {
        $pengajuan = PengajuanSurat::factory()->create(['nik_pemohon' => $this->warga->nik]);

        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson("/api/v1/admin/surat/pengajuan/{$pengajuan->id}/approve");

        $response->assertStatus(403);
    }

    public function test_warga_cannot_reject_pengajuan()
    {
        $pengajuan = PengajuanSurat::factory()->create(['nik_pemohon' => $this->warga->nik]);

        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson("/api/v1/admin/surat/pengajuan/{$pengajuan->id}/reject", [
                'catatan_penolakan' => 'Alasan',
            ]);

        $response->assertStatus(403);
    }

    public function test_nomor_registrasi_auto_generated()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/surat/pengajuan', [
                'kategori_surat_id' => $this->kategori->id,
                'data_isian' => ['keperluan' => 'Test'],
                'file_syarat' => ['ktp' => UploadedFile::fake()->image('ktp.jpg')],
            ]);

        $response->assertStatus(201);
        $this->assertNotNull($response->json('data.nomor_registrasi'));
    }

    public function test_tracking_created_on_submission()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/surat/pengajuan', [
                'kategori_surat_id' => $this->kategori->id,
                'data_isian' => ['keperluan' => 'Test'],
                'file_syarat' => ['ktp' => UploadedFile::fake()->image('ktp.jpg')],
            ]);

        $response->assertStatus(201);
        $pengajuanId = $response->json('data.id');

        $this->assertDatabaseHas('tracking_pengajuan_surat', [
            'pengajuan_surat_id' => $pengajuanId,
            'status_baru' => 'Pending',
        ]);
    }
}

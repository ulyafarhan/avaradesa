<?php

namespace Tests\Unit\Services;

use App\Models\Penduduk;
use App\Models\Keluarga;
use App\Models\PengajuanSurat;
use App\Models\KategoriSurat;
use App\Services\StatistikService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class StatistikServiceTest extends TestCase
{
    protected StatistikService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new StatistikService();
        $this->seed(\Database\Seeders\KategoriSuratSeeder::class);
    }

    public function test_get_demografi_returns_correct_structure()
    {
        $keluarga = Keluarga::create([
            'no_kk' => '1234567890123456',
            'alamat' => 'Jl. Test',
            'dusun' => 'Dusun A',
            'rt_rw' => '001/002',
        ]);

        Penduduk::create([
            'nik' => '1111111111111111',
            'no_kk' => $keluarga->no_kk,
            'nama_lengkap' => 'John Doe',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Programmer',
            'status_perkawinan' => 'Belum Kawin',
            'status_keluarga' => 'Anak',
            'status_mutasi' => 'Tetap',
        ]);

        $demografi = $this->service->getDemografi();

        $this->assertIsArray($demografi);
        $this->assertArrayHasKey('total_penduduk', $demografi);
        $this->assertArrayHasKey('total_keluarga', $demografi);
        $this->assertArrayHasKey('laki_laki', $demografi);
        $this->assertArrayHasKey('perempuan', $demografi);
        $this->assertArrayHasKey('per_dusun', $demografi);
        $this->assertArrayHasKey('per_agama', $demografi);
        $this->assertArrayHasKey('per_pendidikan', $demografi);
        $this->assertArrayHasKey('per_pekerjaan', $demografi);
        $this->assertArrayHasKey('per_usia', $demografi);
    }

    public function test_get_demografi_counts_correctly()
    {
        $keluarga = Keluarga::create([
            'no_kk' => '1234567890123456',
            'alamat' => 'Jl. Test',
            'dusun' => 'Dusun A',
            'rt_rw' => '001/002',
        ]);

        Penduduk::create([
            'nik' => '1111111111111111',
            'no_kk' => $keluarga->no_kk,
            'nama_lengkap' => 'John Doe',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Programmer',
            'status_perkawinan' => 'Belum Kawin',
            'status_keluarga' => 'Anak',
            'status_mutasi' => 'Tetap',
        ]);

        Penduduk::create([
            'nik' => '2222222222222222',
            'no_kk' => $keluarga->no_kk,
            'nama_lengkap' => 'Jane Doe',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1995-01-01',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Designer',
            'status_perkawinan' => 'Belum Kawin',
            'status_keluarga' => 'Anak',
            'status_mutasi' => 'Tetap',
        ]);

        $demografi = $this->service->getDemografi();

        $this->assertEquals(2, $demografi['total_penduduk']);
        $this->assertEquals(1, $demografi['total_keluarga']);
        $this->assertEquals(1, $demografi['laki_laki']);
        $this->assertEquals(1, $demografi['perempuan']);
    }

    public function test_get_layanan_returns_correct_structure()
    {
        $layanan = $this->service->getLayanan();

        $this->assertIsArray($layanan);
        $this->assertArrayHasKey('pengajuan_surat', $layanan);
        $this->assertArrayHasKey('mutasi_penduduk', $layanan);
        $this->assertArrayHasKey('per_jenis_surat', $layanan);
    }

    public function test_clear_cache_removes_cached_data()
    {
        // Set cache
        Cache::put('statistik_demografi', ['test' => 'data'], 3600);

        $this->assertTrue(Cache::has('statistik_demografi'));

        // Clear cache
        $this->service->clearCache();

        $this->assertFalse(Cache::has('statistik_demografi'));
    }

    public function test_demografi_uses_cache()
    {
        $keluarga = Keluarga::create([
            'no_kk' => '1234567890123456',
            'alamat' => 'Jl. Test',
            'dusun' => 'Dusun A',
            'rt_rw' => '001/002',
        ]);

        Penduduk::create([
            'nik' => '1111111111111111',
            'no_kk' => $keluarga->no_kk,
            'nama_lengkap' => 'John Doe',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Programmer',
            'status_perkawinan' => 'Belum Kawin',
            'status_keluarga' => 'Anak',
            'status_mutasi' => 'Tetap',
        ]);

        // First call
        $demografi1 = $this->service->getDemografi();

        // Add new penduduk
        Penduduk::withoutEvents(function () use ($keluarga) {
            Penduduk::create([
                'nik' => '2222222222222222',
                'no_kk' => $keluarga->no_kk,
                'nama_lengkap' => 'Jane Doe',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1995-01-01',
                'jenis_kelamin' => 'P',
                'agama' => 'Islam',
                'pendidikan' => 'S1',
                'pekerjaan' => 'Designer',
                'status_perkawinan' => 'Belum Kawin',
                'status_keluarga' => 'Anak',
                'status_mutasi' => 'Tetap',
            ]);
        });

        // Second call should return cached data
        $demografi2 = $this->service->getDemografi();

        // Should be same because of cache
        $this->assertEquals($demografi1['total_penduduk'], $demografi2['total_penduduk']);
    }
}

<?php

namespace Tests\Unit\Models;

use App\Models\PengajuanSurat;
use App\Models\KategoriSurat;
use App\Models\Penduduk;
use App\Models\Keluarga;
use Tests\TestCase;

class PengajuanSuratTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\KategoriSuratSeeder::class);
    }

    public function test_pengajuan_surat_auto_generates_nomor_registrasi()
    {
        $keluarga = Keluarga::create([
            'no_kk' => '1234567890123456',
            'alamat' => 'Jl. Test',
            'dusun' => 'Test',
            'rt_rw' => '001/002',
        ]);

        $penduduk = Penduduk::create([
            'nik' => '1234567890123456',
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
        ]);

        $kategori = KategoriSurat::first();

        $pengajuan = PengajuanSurat::create([
            'nik_pemohon' => $penduduk->nik,
            'kategori_surat_id' => $kategori->id,
            'data_isian' => ['test' => 'data'],
            'file_syarat' => ['ktp' => 'path/to/ktp.jpg'],
        ]);

        $this->assertNotNull($pengajuan->nomor_registrasi);
        $this->assertStringStartsWith(date('Ymd'), $pengajuan->nomor_registrasi);
    }

    public function test_pengajuan_surat_has_relationships()
    {
        $keluarga = Keluarga::create([
            'no_kk' => '1234567890123456',
            'alamat' => 'Jl. Test',
            'dusun' => 'Test',
            'rt_rw' => '001/002',
        ]);

        $penduduk = Penduduk::create([
            'nik' => '1234567890123456',
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
        ]);

        $kategori = KategoriSurat::first();

        $pengajuan = PengajuanSurat::create([
            'nik_pemohon' => $penduduk->nik,
            'kategori_surat_id' => $kategori->id,
            'data_isian' => ['test' => 'data'],
            'file_syarat' => ['ktp' => 'path/to/ktp.jpg'],
        ]);

        $this->assertInstanceOf(Penduduk::class, $pengajuan->pemohon);
        $this->assertInstanceOf(KategoriSurat::class, $pengajuan->kategori);
    }

    public function test_pengajuan_surat_pending_scope()
    {
        $keluarga = Keluarga::create([
            'no_kk' => '1234567890123456',
            'alamat' => 'Jl. Test',
            'dusun' => 'Test',
            'rt_rw' => '001/002',
        ]);

        $penduduk = Penduduk::create([
            'nik' => '1234567890123456',
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
        ]);

        $kategori = KategoriSurat::first();

        PengajuanSurat::create([
            'nik_pemohon' => $penduduk->nik,
            'kategori_surat_id' => $kategori->id,
            'data_isian' => ['test' => 'data'],
            'file_syarat' => ['ktp' => 'path/to/ktp.jpg'],
            'status' => 'Pending',
        ]);

        PengajuanSurat::create([
            'nik_pemohon' => $penduduk->nik,
            'kategori_surat_id' => $kategori->id,
            'data_isian' => ['test' => 'data'],
            'file_syarat' => ['ktp' => 'path/to/ktp.jpg'],
            'status' => 'Selesai',
        ]);

        $pending = PengajuanSurat::pending()->get();

        $this->assertCount(1, $pending);
        $this->assertEquals('Pending', $pending->first()->status);
    }
}

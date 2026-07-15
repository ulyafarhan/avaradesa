<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\Api\StoreMutasiRequest;
use App\Models\Penduduk;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreMutasiRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_mutasi_rules_validation()
    {
        $penduduk = Penduduk::factory()->create(['nik' => '1234567890123456']);

        $request = new StoreMutasiRequest();

        // Pass valid data
        $validator = Validator::make([
            'nik' => '1234567890123456',
            'jenis_mutasi' => 'Kematian',
            'tanggal_mutasi' => now()->subDay()->format('Y-m-d'),
            'keterangan' => 'Sakit',
            'dokumen_bukti' => 'http://example.com/doc.pdf',
        ], $request->rules());
        
        $this->assertTrue($validator->passes());

        // Fail validation - invalid NIK
        $validator = Validator::make([
            'nik' => '12345', // Not 16 chars
            'jenis_mutasi' => 'Kematian',
            'tanggal_mutasi' => now()->subDay()->format('Y-m-d'),
            'keterangan' => 'Sakit',
            'dokumen_bukti' => 'http://example.com/doc.pdf',
        ], $request->rules());
        
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('nik', $validator->errors()->toArray());

        // Fail validation - future date
        $validator = Validator::make([
            'nik' => '1234567890123456',
            'jenis_mutasi' => 'Kematian',
            'tanggal_mutasi' => now()->addDay()->format('Y-m-d'), // Future
            'keterangan' => 'Sakit',
            'dokumen_bukti' => 'http://example.com/doc.pdf',
        ], $request->rules());
        
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('tanggal_mutasi', $validator->errors()->toArray());

        // Fail validation - invalid jenis mutasi
        $validator = Validator::make([
            'nik' => '1234567890123456',
            'jenis_mutasi' => 'InvalidType',
            'tanggal_mutasi' => now()->subDay()->format('Y-m-d'),
            'keterangan' => 'Sakit',
            'dokumen_bukti' => 'http://example.com/doc.pdf',
        ], $request->rules());
        
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('jenis_mutasi', $validator->errors()->toArray());
    }
}

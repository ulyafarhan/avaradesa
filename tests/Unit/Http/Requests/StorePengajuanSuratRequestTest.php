<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\Api\StorePengajuanSuratRequest;
use App\Models\KategoriSurat;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorePengajuanSuratRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_pengajuan_surat_rules_validation()
    {
        $kategori = KategoriSurat::factory()->create();

        $request = new StorePengajuanSuratRequest();

        // Pass valid data
        $validator = Validator::make([
            'kategori_surat_id' => $kategori->id,
            'data_isian' => ['keperluan' => 'Pernikahan'],
            'file_syarat' => ['ktp' => 'http://example.com/ktp.jpg'],
        ], $request->rules());

        $this->assertTrue($validator->passes());

        // Fail validation - non-existent kategori
        $validator = Validator::make([
            'kategori_surat_id' => 99999,
            'data_isian' => ['keperluan' => 'Pernikahan'],
            'file_syarat' => ['ktp' => 'http://example.com/ktp.jpg'],
        ], $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('kategori_surat_id', $validator->errors()->toArray());

        // Fail validation - data_isian is not an array
        $validator = Validator::make([
            'kategori_surat_id' => $kategori->id,
            'data_isian' => 'not-an-array',
            'file_syarat' => ['ktp' => 'http://example.com/ktp.jpg'],
        ], $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('data_isian', $validator->errors()->toArray());
    }
}

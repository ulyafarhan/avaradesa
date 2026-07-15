<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\Penduduk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CitizenFamilyWebTest extends TestCase
{
    use RefreshDatabase;

    protected $keluarga;
    protected $kepalaKeluarga;
    protected $anggota;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->keluarga = Keluarga::factory()->create();
        
        $this->kepalaKeluarga = Penduduk::factory()->create([
            'no_kk' => $this->keluarga->no_kk,
            'is_kepala_keluarga' => true
        ]);

        $this->keluarga->update(['kepala_keluarga_nik' => $this->kepalaKeluarga->nik]);

        $this->anggota = Penduduk::factory()->create([
            'no_kk' => $this->keluarga->no_kk,
            'is_kepala_keluarga' => false
        ]);
    }

    public function test_warga_can_view_family_members()
    {
        $response = $this->actingAs($this->kepalaKeluarga, 'penduduk')
            ->get('/warga/keluarga');

        $response->assertStatus(200);
    }

    public function test_kepala_keluarga_can_update_family_member_data()
    {
        $response = $this->actingAs($this->kepalaKeluarga, 'penduduk')
            ->from('/warga/keluarga')
            ->put("/warga/keluarga/{$this->anggota->nik}", [
                'pendidikan' => 'S1',
                'pekerjaan' => 'PNS',
                'status_perkawinan' => 'Kawin'
            ]);

        $response->assertRedirect('/warga/keluarga');
        
        $this->anggota->refresh();
        $this->assertEquals('S1', $this->anggota->pendidikan);
        $this->assertEquals('PNS', $this->anggota->pekerjaan);
    }

    public function test_non_kepala_keluarga_is_forbidden_to_update_member_data()
    {
        $response = $this->actingAs($this->anggota, 'penduduk')
            ->put("/warga/keluarga/{$this->kepalaKeluarga->nik}", [
                'agama_id' => 2,
            ]);

        $response->assertStatus(403);
    }
}

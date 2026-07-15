<?php

namespace Tests\Unit\Policies;

use App\Models\Administrator;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Policies\PengajuanSuratPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengajuanSuratPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_policy()
    {
        $policy = new PengajuanSuratPolicy();
        $pendudukA = Penduduk::factory()->create(['nik' => '1111111111111111']);
        $pendudukB = Penduduk::factory()->create(['nik' => '2222222222222222']);
        
        $pengajuan = PengajuanSurat::factory()->create(['nik_pemohon' => '1111111111111111']);

        $this->assertTrue($policy->view($pendudukA, $pengajuan));
        $this->assertFalse($policy->view($pendudukB, $pengajuan));
    }

    public function test_create_policy()
    {
        $policy = new PengajuanSuratPolicy();
        $penduduk = Penduduk::factory()->create();

        $this->assertTrue($policy->create($penduduk));
    }

    public function test_approve_reject_policy()
    {
        $policy = new PengajuanSuratPolicy();

        $kades = Administrator::factory()->create(['role' => 'kepala_desa']);
        $sekdes = Administrator::factory()->create(['role' => 'sekdes']);
        $operator = Administrator::factory()->create(['role' => 'operator']);
        $other = new Administrator(['role' => 'invalid_role']);

        $this->assertTrue($policy->approve($kades));
        $this->assertTrue($policy->approve($sekdes));
        $this->assertTrue($policy->approve($operator));
        $this->assertFalse($policy->approve($other));

        $this->assertTrue($policy->reject($kades));
        $this->assertTrue($policy->reject($sekdes));
        $this->assertTrue($policy->reject($operator));
        $this->assertFalse($policy->reject($other));
    }
}

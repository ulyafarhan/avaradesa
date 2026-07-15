<?php

namespace Tests\Unit\Policies;

use App\Models\Administrator;
use App\Models\Penduduk;
use App\Models\MutasiPenduduk;
use App\Policies\MutasiPendudukPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MutasiPendudukPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_policy()
    {
        $policy = new MutasiPendudukPolicy();
        $pendudukA = Penduduk::factory()->create(['nik' => '1111111111111111']);
        $pendudukB = Penduduk::factory()->create(['nik' => '2222222222222222']);
        
        $mutasi = MutasiPenduduk::factory()->create(['nik' => '1111111111111111']);

        $this->assertTrue($policy->view($pendudukA, $mutasi));
        $this->assertFalse($policy->view($pendudukB, $mutasi));
    }

    public function test_create_policy()
    {
        $policy = new MutasiPendudukPolicy();
        $penduduk = Penduduk::factory()->create();

        $this->assertTrue($policy->create($penduduk));
    }

    public function test_approve_reject_policy()
    {
        $policy = new MutasiPendudukPolicy();

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

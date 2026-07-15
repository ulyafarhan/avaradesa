<?php

namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Keluarga;
use App\Models\Penduduk;
use Tests\TestCase;

class WebFrontendTest extends TestCase
{
    protected $warga;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $keluarga = Keluarga::factory()->create();
        $this->warga = Penduduk::factory()->create([
            'no_kk' => $keluarga->no_kk,
            'status_keluarga' => 'Kepala Keluarga',
        ]);
        $this->admin = Administrator::factory()->operator()->create();
    }

    public function test_public_home_renders()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_profile_page_renders()
    {
        $response = $this->get('/profil');

        $response->assertStatus(200);
    }

    public function test_informasi_page_renders()
    {
        $response = $this->get('/informasi');

        $response->assertStatus(200);
    }

    public function test_verifikasi_page_renders()
    {
        $response = $this->get('/verifikasi');

        $response->assertStatus(200);
    }

    public function test_fasilitas_page_renders()
    {
        $response = $this->get('/fasilitas');

        $response->assertStatus(200);
    }

    public function test_statistik_page_renders()
    {
        $response = $this->get('/statistik');

        $response->assertStatus(200);
    }

    public function test_guest_redirected_from_warga_dashboard()
    {
        $response = $this->get('/warga/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_warga_can_access_dashboard_after_login()
    {
        $response = $this->actingAs($this->warga, 'penduduk')
            ->get('/warga/dashboard');

        $response->assertStatus(200);
    }

    public function test_guest_sees_login_page()
    {
        $response = $this->get('/login');

        $response->assertStatus(200)
            ->assertSee('login', false);
    }

    public function test_warga_redirected_from_login()
    {
        $response = $this->actingAs($this->warga, 'penduduk')
            ->get('/login');

        $response->assertStatus(302);
    }

    public function test_warga_can_logout()
    {
        $response = $this->actingAs($this->warga, 'penduduk')
            ->post('/logout');

        $response->assertStatus(302);
        $this->assertGuest('penduduk');
    }

    public function test_warga_profile_page()
    {
        $response = $this->actingAs($this->warga, 'penduduk')
            ->get('/warga/profil');

        $response->assertStatus(200);
    }

    public function test_warga_keluarga_page()
    {
        $response = $this->actingAs($this->warga, 'penduduk')
            ->get('/warga/keluarga');

        $response->assertStatus(200);
    }

    public function test_warga_can_access_pengajuan_history()
    {
        $response = $this->actingAs($this->warga, 'penduduk')
            ->get('/warga/pengajuan/1/print');

        $response->assertStatus(404);
    }
}

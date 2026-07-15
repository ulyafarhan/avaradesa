<?php

namespace Tests\Feature;

use App\Models\InformasiPublik;
use App\Models\InventarisFasilitas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPortalWebTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        InformasiPublik::factory()->create([
            'judul' => 'Agenda Rapat Desa',
            'konten' => 'Pembahasan anggaran tahun baru',
            'kategori' => 'Agenda',
            'is_published' => true
        ]);

        InformasiPublik::factory()->create([
            'judul' => 'Pengumuman Penerima BLT',
            'konten' => 'Daftar nama penerima manfaat',
            'kategori' => 'Pengumuman',
            'is_published' => true
        ]);

        InventarisFasilitas::factory()->create([
            'nama_fasilitas' => 'Polindes Desa',
            'is_publik' => true
        ]);
    }

    public function test_guest_can_search_information()
    {
        $response = $this->get('/informasi?search=Rapat');
        $response->assertStatus(200);
        $response->assertSee('Rapat');
    }

    public function test_guest_can_filter_information_by_category()
    {
        $response = $this->get('/informasi?kategori=Pengumuman');
        $response->assertStatus(200);
        $response->assertSee('BLT');
    }

    public function test_guest_can_view_public_facilities()
    {
        $response = $this->get('/fasilitas');
        $response->assertStatus(200);
        $response->assertSee('Polindes');
    }
}

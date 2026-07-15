<?php

namespace Tests\Unit\Models;

use App\Models\Administrator;
use App\Models\InformasiPublik;
use Tests\TestCase;

class InformasiPublikModelTest extends TestCase
{
    public function test_informasi_can_be_created()
    {
        $admin = Administrator::factory()->operator()->create();
        $info = InformasiPublik::factory()->create(['author_id' => $admin->id]);

        $this->assertDatabaseHas('informasi_publik', [
            'id' => $info->id,
            'judul' => $info->judul,
        ]);
    }

    public function test_informasi_has_author_relationship()
    {
        $admin = Administrator::factory()->operator()->create();
        $info = InformasiPublik::factory()->create(['author_id' => $admin->id]);

        $this->assertNotNull($info->author);
        $this->assertEquals($admin->id, $info->author->id);
    }

    public function test_informasi_published_scope()
    {
        $admin = Administrator::factory()->operator()->create();
        InformasiPublik::factory()->create(['author_id' => $admin->id, 'is_published' => true]);
        InformasiPublik::factory()->draft()->create(['author_id' => $admin->id]);

        $this->assertEquals(1, InformasiPublik::published()->count());
    }

    public function test_cover_image_returns_null_when_empty()
    {
        $admin = Administrator::factory()->operator()->create();
        $info = InformasiPublik::factory()->create([
            'author_id' => $admin->id,
            'cover_image' => null,
        ]);

        $this->assertNull($info->cover_image);
    }

    public function test_cover_image_returns_url_when_set()
    {
        $admin = Administrator::factory()->operator()->create();
        $info = InformasiPublik::factory()->create([
            'author_id' => $admin->id,
            'cover_image' => 'informasi/test-cover.jpg',
        ]);

        $this->assertStringContainsString('test-cover.jpg', $info->cover_image);
    }

    public function test_informasi_status_default_is_draft()
    {
        $admin = Administrator::factory()->operator()->create();
        $info = InformasiPublik::factory()->draft()->create(['author_id' => $admin->id]);

        $this->assertFalse($info->is_published);
    }

    public function test_slug_is_unique()
    {
        $admin = Administrator::factory()->operator()->create();

        InformasiPublik::factory()->create([
            'author_id' => $admin->id,
            'slug' => 'unique-slug',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        InformasiPublik::factory()->create([
            'author_id' => $admin->id,
            'slug' => 'unique-slug',
        ]);
    }
}

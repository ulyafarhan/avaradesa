<?php

namespace Tests\Unit\Models;

use App\Models\PengaturanDesa;
use Tests\TestCase;

class PengaturanDesaTest extends TestCase
{
    public function test_can_set_and_get_string()
    {
        PengaturanDesa::set('nama_desa', 'Desa Avara');

        $this->assertEquals('Desa Avara', PengaturanDesa::get('nama_desa'));
    }

    public function test_returns_default_if_key_not_found()
    {
        $this->assertEquals('default_value', PengaturanDesa::get('non_existent_key', 'default_value'));
    }

    public function test_can_set_integer()
    {
        PengaturanDesa::set('tahun', 2026, 'integer');

        $this->assertSame(2026, PengaturanDesa::get('tahun'));
    }

    public function test_can_set_boolean()
    {
        PengaturanDesa::set('is_active', true, 'boolean');

        $this->assertTrue(PengaturanDesa::get('is_active'));
    }

    public function test_can_set_json()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        PengaturanDesa::set('json_data', $data, 'json');

        $this->assertEquals($data, PengaturanDesa::get('json_data'));
    }

    public function test_update_existing_key()
    {
        PengaturanDesa::set('nama_desa', 'Desa Avara');
        PengaturanDesa::set('nama_desa', 'Desa Avara Baru');

        $this->assertEquals('Desa Avara Baru', PengaturanDesa::get('nama_desa'));
    }

    public function test_handles_empty_string()
    {
        PengaturanDesa::set('key', '');

        $this->assertEquals('', PengaturanDesa::get('key'));
    }

    public function test_handles_integer_type()
    {
        PengaturanDesa::set('number', '42', 'integer');

        $this->assertSame(42, PengaturanDesa::get('number'));
    }

    public function test_handles_boolean_type()
    {
        PengaturanDesa::set('flag', '1', 'boolean');

        $this->assertTrue(PengaturanDesa::get('flag'));
    }

    public function test_handles_json_type()
    {
        PengaturanDesa::set('json_item', '{"a":1}', 'json');

        $this->assertEquals(['a' => 1], PengaturanDesa::get('json_item'));
    }
}

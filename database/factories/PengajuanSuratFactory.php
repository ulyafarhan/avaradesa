<?php

namespace Database\Factories;

use App\Models\KategoriSurat;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use Illuminate\Database\Eloquent\Factories\Factory;

class PengajuanSuratFactory extends Factory
{
    protected $model = PengajuanSurat::class;

    public function definition(): array
    {
        return [
            'nik_pemohon' => Penduduk::factory(),
            'kategori_surat_id' => KategoriSurat::factory(),
            'data_isian' => ['keperluan' => fake()->sentence()],
            'file_syarat' => ['ktp' => 'uploads/ktp.jpg', 'kk' => 'uploads/kk.jpg'],
            'status' => 'Pending',
        ];
    }

    public function disetujui(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'Disetujui']);
    }

    public function ditolak(): static
    {
        return $this->state(fn (array $attrs) => [
            'status' => 'Ditolak',
            'catatan_penolakan' => fake()->sentence(),
        ]);
    }

    public function selesai(): static
    {
        return $this->state(fn (array $attrs) => ['status' => 'Selesai']);
    }
}

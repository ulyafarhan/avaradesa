<?php

namespace Database\Factories;

use App\Models\MutasiPenduduk;
use App\Models\Penduduk;
use Illuminate\Database\Eloquent\Factories\Factory;

class MutasiPendudukFactory extends Factory
{
    protected $model = MutasiPenduduk::class;

    public function definition(): array
    {
        return [
            'nik' => Penduduk::factory(),
            'jenis_mutasi' => fake()->randomElement(['Kelahiran', 'Kematian', 'Kedatangan', 'Kepindahan']),
            'tanggal_mutasi' => fake()->date(max: 'now'),
            'keterangan' => fake()->paragraph(),
            'dokumen_bukti' => 'uploads/bukti/' . fake()->uuid() . '.pdf',
            'status_verifikasi' => 'Pending',
        ];
    }

    public function disetujui(): static
    {
        return $this->state(fn (array $attrs) => ['status_verifikasi' => 'Disetujui']);
    }

    public function ditolak(): static
    {
        return $this->state(fn (array $attrs) => ['status_verifikasi' => 'Ditolak']);
    }
}

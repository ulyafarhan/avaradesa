<?php

namespace Database\Factories;

use App\Models\Keluarga;
use App\Models\Penduduk;
use Illuminate\Database\Eloquent\Factories\Factory;

class PendudukFactory extends Factory
{
    protected $model = Penduduk::class;

    private static $nikCounter = 1234567890123000;

    public function definition(): array
    {
        $nik = (string) (++self::$nikCounter);

        $agama = fake()->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha']);
        $pendidikan = fake()->randomElement(['SD', 'SMP', 'SMA', 'S1', 'S2', 'S3']);
        $pekerjaan = fake()->randomElement(['Petani', 'PNS', 'Swasta', 'IRT', 'Pelajar', 'Wiraswasta']);
        $statusPerkawinan = fake()->randomElement(['Belum Kawin', 'Kawin', 'Cerai']);
        $statusKeluarga = fake()->randomElement(['Suami', 'Istri', 'Anak', 'Kepala Keluarga']);

        return [
            'nik' => $nik,
            'no_kk' => fn () => Keluarga::factory()->create()->no_kk,
            'nama_lengkap' => fake()->name(),
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->date(max: '2005-01-01'),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'agama' => $agama,
            'agama_id' => \App\Models\RefAgama::where('nama', $agama)->first()?->id,
            'pendidikan' => $pendidikan,
            'pendidikan_id' => \App\Models\RefPendidikan::where('nama', 'like', "$pendidikan%")->first()?->id,
            'pekerjaan' => $pekerjaan,
            'pekerjaan_id' => \App\Models\RefPekerjaan::where('nama', $pekerjaan)->first()?->id,
            'status_perkawinan' => $statusPerkawinan,
            'status_perkawinan_id' => \App\Models\RefStatusPerkawinan::where('nama', $statusPerkawinan)->first()?->id,
            'status_keluarga' => $statusKeluarga,
            'status_keluarga_id' => \App\Models\RefStatusKeluarga::where('nama', $statusKeluarga)->first()?->id,
            'status_mutasi' => 'Tetap',
        ];
    }

    public function pindah(): static
    {
        return $this->state(fn (array $attrs) => ['status_mutasi' => 'Pindah']);
    }

    public function meninggal(): static
    {
        return $this->state(fn (array $attrs) => ['status_mutasi' => 'Meninggal']);
    }
}

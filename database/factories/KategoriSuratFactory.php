<?php

namespace Database\Factories;

use App\Models\KategoriSurat;
use Illuminate\Database\Eloquent\Factories\Factory;

class KategoriSuratFactory extends Factory
{
    protected $model = KategoriSurat::class;

    public function definition(): array
    {
        $kode = fake()->unique()->regexify('[A-Z]{3}');

        return [
            'kode_surat' => $kode,
            'nama_surat' => 'Surat Keterangan ' . fake()->word(),
            'template_view' => fake()->randomElement(['domisili', 'sktm', 'usaha', 'pengantar_ktp', 'kelahiran']),
            'schema_isian' => [
                ['field' => 'keperluan', 'label' => 'Keperluan', 'type' => 'text', 'required' => true],
            ],
            'syarat_dokumen' => ['KTP', 'Kartu Keluarga'],
            'is_active' => true,
        ];
    }

    public function nonaktif(): static
    {
        return $this->state(fn (array $attrs) => ['is_active' => false]);
    }
}

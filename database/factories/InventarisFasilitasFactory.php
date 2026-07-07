<?php

namespace Database\Factories;

use App\Models\InventarisFasilitas;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventarisFasilitasFactory extends Factory
{
    protected $model = InventarisFasilitas::class;

    public function definition(): array
    {
        return [
            'nama_fasilitas' => fake()->word() . ' Desa',
            'jenis_fasilitas' => fake()->randomElement(['Kesehatan', 'Pendidikan', 'Agama']),
            'kondisi' => 'Baik',
            'status_penggunaan' => 'Aktif',
            'is_publik' => true
        ];
    }
}

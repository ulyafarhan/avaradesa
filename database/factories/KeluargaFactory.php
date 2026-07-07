<?php

namespace Database\Factories;

use App\Models\Keluarga;
use Illuminate\Database\Eloquent\Factories\Factory;

class KeluargaFactory extends Factory
{
    protected $model = Keluarga::class;

    private static $kkCounter = 1234567890123000;

    public function definition(): array
    {
        return [
            'no_kk' => (string) (++self::$kkCounter),
            'alamat' => fake()->address(),
            'dusun' => fake()->randomElement(['Dusun A', 'Dusun B', 'Dusun C']),
            'rt_rw' => fake()->randomElement(['001/001', '001/002', '002/001']),
        ];
    }
}

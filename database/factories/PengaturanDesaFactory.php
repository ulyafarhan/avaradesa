<?php

namespace Database\Factories;

use App\Models\PengaturanDesa;
use Illuminate\Database\Eloquent\Factories\Factory;

class PengaturanDesaFactory extends Factory
{
    protected $model = PengaturanDesa::class;

    public function definition(): array
    {
        return [
            'kunci' => fake()->unique()->word(),
            'nilai' => fake()->sentence(),
            'tipe_data' => 'string',
        ];
    }

    public function bertipe(string $type): static
    {
        return $this->state(fn (array $attrs) => ['tipe_data' => $type]);
    }
}

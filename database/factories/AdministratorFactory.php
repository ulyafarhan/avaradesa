<?php

namespace Database\Factories;

use App\Models\Administrator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class AdministratorFactory extends Factory
{
    protected $model = Administrator::class;

    private static $password;

    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'password' => self::$password ??= Hash::make('password123'),
            'role' => fake()->randomElement(['operator', 'sekdes', 'kepala_desa']),
        ];
    }

    public function kepalaDesa(): static
    {
        static $counter = 0;
        $counter++;
        return $this->state(fn (array $attrs) => ['role' => 'kepala_desa', 'username' => 'kepala_desa' . ($counter > 1 ? $counter : '')]);
    }

    public function sekdes(): static
    {
        static $counter = 0;
        $counter++;
        return $this->state(fn (array $attrs) => ['role' => 'sekdes', 'username' => 'sekdes' . ($counter > 1 ? $counter : '')]);
    }

    public function operator(): static
    {
        static $counter = 0;
        $counter++;
        return $this->state(fn (array $attrs) => ['role' => 'operator', 'username' => 'operator' . ($counter > 1 ? $counter : '')]);
    }
}

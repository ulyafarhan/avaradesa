<?php

namespace Database\Factories;

use App\Models\Administrator;
use App\Models\InformasiPublik;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InformasiPublikFactory extends Factory
{
    protected $model = InformasiPublik::class;

    public function definition(): array
    {
        $judul = fake()->sentence();

        return [
            'judul' => $judul,
            'slug' => Str::slug($judul) . '-' . fake()->unique()->randomNumber(4),
            'konten' => fake()->paragraphs(3, true),
            'kategori' => fake()->randomElement(['Berita', 'Pengumuman', 'Kegiatan']),
            'is_published' => true,
            'author_id' => Administrator::factory(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attrs) => ['is_published' => false]);
    }
}

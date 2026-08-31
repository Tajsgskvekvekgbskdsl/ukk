<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Buku>
 */
class BukuFactory extends Factory
{
    public function definition(): array
    {
        return [
            'judul_buku' => $this->faker->sentence(3),
            'pengarang' => $this->faker->name(),
            'penerbit' => $this->faker->company(),
            'tahun_terbit' => $this->faker->numberBetween(2000, 2025),
            'kategori' => $this->faker->randomElement(['Teknologi', 'Fiksi', 'Pelajaran', 'Sejarah']),
            'stok' => $this->faker->numberBetween(1, 20),
        ];
    }
}

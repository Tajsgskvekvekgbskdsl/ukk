<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Anggota>
 */
class AnggotaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_user' => User::factory(),
            'nis' => (string) $this->faker->unique()->numberBetween(10000, 99999),
            'nama' => $this->faker->name(),
            'kelas' => $this->faker->randomElement(['X IPA 1', 'X IPS 2', 'XI IPA 3', 'XII IPS 1']),
            'alamat' => $this->faker->address(),
        ];
    }
}

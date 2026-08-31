<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Faker userName() bisa mengandung titik; pakai format aman alpha_dash.
            'username' => strtolower($this->faker->unique()->firstName())
                . $this->faker->unique()->numberBetween(10, 999),
            'nama_lengkap' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password', // otomatis di-hash oleh cast 'hashed'
            'role' => 'user',
            'status' => 'aktif',
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => 'admin']);
    }
}

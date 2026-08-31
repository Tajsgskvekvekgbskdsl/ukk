<?php

namespace Database\Factories;

use App\Models\Anggota;
use App\Models\Buku;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaksi>
 */
class TransaksiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_anggota' => Anggota::factory(),
            'id_buku' => Buku::factory(),
            'tgl_pinjam' => now()->toDateString(),
            'tgl_kembali' => null,
            'status' => 'dipinjam',
        ];
    }

    public function dikembalikan(): static
    {
        return $this->state(fn () => [
            'status' => 'dikembalikan',
            'tgl_kembali' => now()->toDateString(),
        ]);
    }
}

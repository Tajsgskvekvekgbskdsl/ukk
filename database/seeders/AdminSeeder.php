<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder idempotent: aman dijalankan berulang.
 * Tidak menghapus / menimpa data yang sudah ada.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ================= AKUN ADMIN =================
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama_lengkap' => 'Administrator',
                'email' => 'admin@perpus.test',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'aktif',
            ]
        );
        // ================= AKUN ADMIN 2 =================
User::updateOrCreate(
    ['username' => 'admin2'],
    [
        'nama_lengkap' => 'Julia Wanda',
        'email' => 'juliawanda@gmail.com',
        'password' => Hash::make('admin123'),
        'role' => 'admin',
        'status' => 'aktif',
    ]
);

        // ================= CONTOH BUKU =================
        if (Buku::count() === 0) {
            $bukus = [
                [
                    'judul_buku' => 'Pemrograman PHP & MySQL',
                    'pengarang' => 'Andi Wijaya',
                    'penerbit' => 'Informatika',
                    'tahun_terbit' => 2020,
                    'kategori' => 'Teknologi',
                    'stok' => 10,
                ],
                [
                    'judul_buku' => 'Belajar Laravel untuk Pemula',
                    'pengarang' => 'Rina Kartika',
                    'penerbit' => 'Elex Media',
                    'tahun_terbit' => 2022,
                    'kategori' => 'Teknologi',
                    'stok' => 8,
                ],
                [
                    'judul_buku' => 'Matematika Dasar',
                    'pengarang' => 'Dwi Prasetyo',
                    'penerbit' => 'Erlangga',
                    'tahun_terbit' => 2019,
                    'kategori' => 'Pelajaran',
                    'stok' => 15,
                ],
                [
                    'judul_buku' => 'Laskar Pelangi',
                    'pengarang' => 'Andrea Hirata',
                    'penerbit' => 'Bentang Pustaka',
                    'tahun_terbit' => 2005,
                    'kategori' => 'Fiksi',
                    'stok' => 6,
                ],
                [
                    'judul_buku' => 'Sejarah Indonesia Modern',
                    'pengarang' => 'Mestika Zed',
                    'penerbit' => 'Kompas',
                    'tahun_terbit' => 2017,
                    'kategori' => 'Sejarah',
                    'stok' => 4,
                ],
            ];

            foreach ($bukus as $b) {
                Buku::create($b);
            }
        }
    }
}

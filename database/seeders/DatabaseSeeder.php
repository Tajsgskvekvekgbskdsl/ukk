<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed idempotent: aman dijalankan ulang tanpa duplikasi data.
     */
    public function run(): void
    {
        // Admin + contoh buku
        $this->call(AdminSeeder::class);

        // ================= AKUN CONTOH ANGGOTA =================
        $budi = User::updateOrCreate(
            ['username' => 'budi'],
            [
                'nama_lengkap' => 'Budi Santoso',
                'email' => 'budi@perpus.test',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'status' => 'aktif',
            ]
        );

        Anggota::updateOrCreate(
            ['id_user' => $budi->id_user],
            [
                'nis' => '2024001',
                'nama' => 'Budi Santoso',
                'kelas' => 'XI IPA 2',
                'alamat' => 'Jl. Merdeka No. 1',
            ]
        );

        $siti = User::updateOrCreate(
            ['username' => 'siti'],
            [
                'nama_lengkap' => 'Siti Aminah',
                'email' => 'siti@perpus.test',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'status' => 'aktif',
            ]
        );

        Anggota::updateOrCreate(
            ['id_user' => $siti->id_user],
            [
                'nis' => '2024002',
                'nama' => 'Siti Aminah',
                'kelas' => 'X IPS 1',
                'alamat' => 'Jl. Sudirman No. 45',
            ]
        );
    }
}

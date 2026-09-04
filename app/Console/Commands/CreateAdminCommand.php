<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Buat akun admin/user baru dari CLI.
 * Password otomatis di-hash Bcrypt — tidak perlu bcrypt generator.
 *
 * Usage:
 *   php artisan admin:create
 *   php artisan admin:create --username=john --email=john@test.com --role=user --status=aktif
 *   php artisan admin:create --username=jane --email=jane@test.com --role=admin (akan diminta password via prompt)
 */
class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create
        {--username= : Username (harus unik)}
        {--nama= : Nama lengkap}
        {--email= : Email (harus unik)}
        {--password= : Password (akan di-hash otomatis)}
        {--role= : admin atau user}
        {--status= : aktif atau nonaktif}';

    protected $description = 'Buat akun admin/user baru (password otomatis di-hash Bcrypt)';

    public function handle(): int
    {
        $username = $this->option('username') ?? $this->ask('Username');
        $nama = $this->option('nama') ?? $this->ask('Nama Lengkap');
        $email = $this->option('email') ?? $this->ask('Email');
        $password = $this->option('password') ?? $this->secret('Password (min 6 karakter)');
        $role = $this->option('role') ?? $this->choice('Role', ['admin', 'user'], 0);
        $status = $this->option('status') ?? $this->choice('Status', ['aktif', 'nonaktif'], 0);

        // Validasi dasar
        if (strlen($password) < 6) {
            $this->error('Password minimal 6 karakter.');
            return 1;
        }

        if (!in_array($role, ['admin', 'user'])) {
            $this->error("Role harus 'admin' atau 'user'.");
            return 1;
        }

        if (!in_array($status, ['aktif', 'nonaktif'])) {
            $this->error("Status harus 'aktif' atau 'nonaktif'.");
            return 1;
        }

        // Cek duplikasi
        if (User::where('username', $username)->exists()) {
            $this->error("Username '{$username}' sudah digunakan.");
            return 1;
        }

        if (User::where('email', $email)->exists()) {
            $this->error("Email '{$email}' sudah digunakan.");
            return 1;
        }

        // Buat user
        $user = User::create([
            'username' => strtolower($username),
            'nama_lengkap' => $nama,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
            'status' => $status,
        ]);

        $this->info("✅ Akun {$role} '{$username}' berhasil dibuat!");
        $this->table(
            ['Field', 'Value'],
            [
                ['Username', $user->username],
                ['Nama', $user->nama_lengkap],
                ['Email', $user->email],
                ['Role', $user->role],
                ['Status', $user->status],
                ['Password', '*** (sudah di-hash Bcrypt)'],
            ]
        );

        return 0;
    }
}

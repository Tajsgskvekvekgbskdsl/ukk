<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request (username ATAU email).
     * Mendukung dua skenario:
     * 1. Password sudah Bcrypt → flow normal Laravel.
     * 2. Password masih plaintext (dibuat via phpMyAdmin/SQL) → auto-migrasi ke Bcrypt
     *    saat login berhasil, ala WordPress. Plaintext TIDAK pernah tersimpan permanen.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $inputPassword = $credentials['password'];

        // ========== FLOW 1: Password sudah Bcrypt (normal) ==========
        try {
            if (Auth::attempt([$field => $credentials['login'], 'password' => $inputPassword], $request->boolean('remember'))) {
                return $this->afterLogin(Auth::user(), $request);
            }
        } catch (\RuntimeException $e) {
            // Password BUKAN hash Bcrypt — kemungkinan plaintext.
            // Lanjut ke Flow 2 di bawah.
        }

        // ========== FLOW 2: Password plaintext → auto-migrasi ==========
        // Hanya jalan kalau ada user dengan field tersebut DAN passwordnya
        // TIDAK diawali '$2y$' (bukan Bcrypt).
        $user = User::where($field, $credentials['login'])->first();

        if ($user && !str_starts_with((string) $user->password, '$2y$')) {
            // Cek apakah plaintext cocok
            if (hash_equals((string) $user->password, $inputPassword)) {
                // Auto-migrasi: plaintext → Bcrypt
                $user->password = Hash::make($inputPassword);
                $user->save();

                // Login dengan user yang sudah di-upgrade
                Auth::login($user, $request->boolean('remember'));
                return $this->afterLogin($user, $request);
            }
        }

        // ========== GAGAL: password salah atau format tidak dikenali ==========
        return back()->withErrors([
            'login' => 'Username/email atau password salah.',
        ])->onlyInput('login');
    }

    /**
     * Logika setelah login berhasil (cek status + redirect berdasarkan role).
     */
    private function afterLogin(User $user, Request $request)
    {
        // Status akun: kolom status di tabel users (default 'aktif').
        // Akun non-aktif tidak boleh tetap berada dalam session.
        if (trim((string) $user->status) !== 'aktif') {
            Auth::logout();
            $request->session()->invalidate();

            return back()->withErrors([
                'login' => 'Akun Anda tidak aktif. Hubungi administrator perpustakaan.',
            ])->onlyInput('login');
        }

        $request->session()->regenerate();

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang, Admin!');
        }

        return redirect()->intended(route('katalog'))
            ->with('success', 'Selamat datang di Perpustakaan Sekolah Digital!');
    }

    /**
     * Show register form.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle register request:
     * 1. Buat record users (role otomatis user).
     * 2. Buat record anggota.
     * 3. Hubungkan anggota -> users melalui id_user.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'nis' => 'nullable|string|max:30',
            'kelas' => 'nullable|string|max:30',
            'alamat' => 'nullable|string|max:500',
        ]);

        // User + Anggota dibuat atomik agar tidak ada data yang terpisah.
        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'username' => strtolower($data['username']),
                'nama_lengkap' => $data['nama_lengkap'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'user', // Register TIDAK PERNAH membuat admin.
                'status' => 'aktif',
            ]);

            // ERD: anggota.id_user -> users.id_user
            $user->anggota()->create([
                'nis' => $data['nis'] ?? null,
                'nama' => $data['nama_lengkap'],
                'kelas' => $data['kelas'] ?? null,
                'alamat' => $data['alamat'] ?? null,
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('katalog')
            ->with('success', 'Registrasi berhasil! Selamat menjadi anggota perpustakaan.');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda')->with('success', 'Anda telah logout.');
    }
}

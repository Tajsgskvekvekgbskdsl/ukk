<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * ADMIN - CRUD ANGGOTA.
 * Setiap anggota dibuatkan akun user sehingga selalu terhubung:
 * anggota.id_user -> users.id_user (sesuai ERD).
 */
class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $anggotas = Anggota::query()
            ->with(['user:id_user,username,nama_lengkap,email'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('nama', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhere('kelas', 'like', "%{$search}%");
                })->orWhereHas('user', function ($u) use ($search) {
                    $u->where('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('anggotas.index', compact('anggotas', 'search'));
    }

    public function create()
    {
        return view('anggotas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => 'required|string|min:6',
            'nis' => 'nullable|string|max:30',
            'kelas' => 'nullable|string|max:30',
            'alamat' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'username' => strtolower($data['username']),
                'nama_lengkap' => $data['nama'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'user',
                'status' => 'aktif',
            ]);

            // ERD: anggota.id_user -> users.id_user
            Anggota::create([
                'id_user' => $user->id_user,
                'nis' => $data['nis'] ?? null,
                'nama' => $data['nama'],
                'kelas' => $data['kelas'] ?? null,
                'alamat' => $data['alamat'] ?? null,
            ]);
        });

        return redirect()->route('admin.anggota.index')
            ->with('success', 'Anggota baru beserta akunnya berhasil ditambahkan.');
    }

    public function show(Anggota $anggota)
    {
        $anggota->load(['user', 'transaksi.buku']);

        return view('anggotas.show', compact('anggota'));
    }

    public function edit(Anggota $anggota)
    {
        $anggota->load('user');

        return view('anggotas.edit', compact('anggota'));
    }

    public function update(Request $request, Anggota $anggota)
    {
        $anggota->load('user');

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => [
                'required', 'string', 'max:50', 'alpha_dash',
                Rule::unique('users', 'username')->ignore($anggota->user?->id_user, 'id_user'),
            ],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($anggota->user?->id_user, 'id_user'),
            ],
            'password' => 'nullable|string|min:6',
            'nis' => 'nullable|string|max:30',
            'kelas' => 'nullable|string|max:30',
            'alamat' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($data, $anggota) {
            $anggota->update([
                'nis' => $data['nis'] ?? null,
                'nama' => $data['nama'],
                'kelas' => $data['kelas'] ?? null,
                'alamat' => $data['alamat'] ?? null,
            ]);

            if ($anggota->user) {
                $payload = [
                    'username' => strtolower($data['username']),
                    'nama_lengkap' => $data['nama'],
                    'email' => $data['email'],
                ];

                if (! empty($data['password'])) {
                    $payload['password'] = Hash::make($data['password']);
                }

                $anggota->user->update($payload);
            }
        });

        return redirect()->route('admin.anggota.index')
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(Anggota $anggota)
    {
        // Anggota yang punya riwayat transaksi tidak dihapus
        // agar history/report tetap utuh.
        if ($anggota->transaksi()->exists()) {
            return redirect()->route('admin.anggota.index')
                ->with('error', 'Anggota tidak dapat dihapus karena masih memiliki data transaksi.');
        }

        DB::transaction(function () use ($anggota) {
            if ($anggota->user) {
                $anggota->user->delete(); // anggota ikut terhapus via FK cascade.
            } else {
                $anggota->delete();
            }
        });

        return redirect()->route('admin.anggota.index')
            ->with('success', 'Anggota beserta akunnya berhasil dihapus.');
    }
}

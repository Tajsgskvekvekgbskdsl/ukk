<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Peminjaman oleh USER yang sedang login.
 *
 * Alur: pilih buku di katalog/detail -> POST peminjaman
 *       -> cek stok (>0) -> transaksi dibuat dengan id_anggota milik user
 *       -> stok -1 -> muncul di Peminjaman Saya.
 */
class PeminjamanController extends Controller
{
    /**
     * Peminjaman Saya: daftar buku yang sedang dipinjam.
     */
    public function index()
    {
        $anggota = $this->authUser()->anggota;

        // Paginator KOSONG (bukan collect()) — view memanggil firstItem()/links()
        // yang hanya ada pada Paginator, bukan Collection.
        $peminjamans = Transaksi::whereRaw('1 = 0')->paginate(10);

        if ($anggota) {
            $peminjamans = Transaksi::with(['buku', 'anggota'])
                ->where('id_anggota', $anggota->id_anggota)
                ->whereIn('status', ['dipinjam'])
                ->latest('tgl_pinjam')
                ->paginate(10);
        }

        return view('site.peminjaman-saya', compact('peminjamans'));
    }

    /**
     * Proses pinjam buku (dari halaman detail / katalog).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_buku' => 'required|integer|exists:buku,id_buku',
        ]);

        $user = $this->authUser();

        // Transaksi WAJIB memakai anggota milik user yang sedang login.
        // Input id_anggota dari luar DIABAIKAN total.
        $anggota = Anggota::where('id_user', $user->id_user)->first();

        if (! $anggota) {
            return back()->with('error', 'Akun Anda belum terhubung dengan data anggota. Silakan hubungi admin.');
        }

        try {
            $judul = '';

            DB::transaction(function () use ($data, $anggota, &$judul) {
                $buku = Buku::whereKey($data['id_buku'])->lockForUpdate()->first();

                if (! $buku) {
                    throw ValidationException::withMessages([
                        'id_buku' => 'Buku tidak ditemukan.',
                    ]);
                }

                // Stok harus > 0 dan tidak boleh menjadi negatif.
                if ($buku->stok < 1) {
                    throw ValidationException::withMessages([
                        'id_buku' => 'Stok buku sedang habis, tidak dapat dipinjam.',
                    ]);
                }

                // Satu anggota tidak boleh meminjam judul yang sama dua kali bersamaan.
                $sudahPinjam = Transaksi::where('id_anggota', $anggota->id_anggota)
                    ->where('id_buku', $buku->id_buku)
                    ->where('status', 'dipinjam')
                    ->lockForUpdate()
                    ->exists();

                if ($sudahPinjam) {
                    throw ValidationException::withMessages([
                        'id_buku' => 'Anda sedang meminjam buku ini. Kembalikan dulu sebelum meminjam lagi.',
                    ]);
                }

                Transaksi::create([
                    'id_anggota' => $anggota->id_anggota,
                    'id_buku' => $buku->id_buku,
                    'tgl_pinjam' => now()->toDateString(),
                    'tgl_kembali' => null,
                    'status' => 'dipinjam',
                ]);

                // Stok -1 secara atomik (tidak akan pernah negatif).
                $berkurang = Buku::whereKey($buku->id_buku)
                    ->where('stok', '>=', 1)
                    ->decrement('stok', 1);

                if ($berkurang === 0) {
                    throw ValidationException::withMessages([
                        'id_buku' => 'Stok buku sedang habis, tidak dapat dipinjam.',
                    ]);
                }

                $judul = $buku->judul_buku;
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()->route('user.peminjaman.index')
            ->with('success', "Buku \"{$judul}\" berhasil dipinjam! Silakan kembalikan maksimal 7 hari.");
    }
}

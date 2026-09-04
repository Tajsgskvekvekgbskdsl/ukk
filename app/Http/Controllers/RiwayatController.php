<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;

/**
 * HISTORY MILIK USER SENDIRI.
 */
class RiwayatController extends Controller
{
    /**
     * Riwayat seluruh transaksi milik user yang sedang login.
     */
    public function index(Request $request)
    {
        $anggota = $this->authUser()->anggota;

        $search = trim((string) $request->input('search'));
        $status = trim((string) $request->input('status'));

        $dasar = Transaksi::query()
            // KEAMANAN: user tanpa anggota TIDAK BOLEH melihat transaksi
            // milik orang lain — paksa hasil kosong.
            ->when($anggota, fn ($q) => $q->where('id_anggota', $anggota->id_anggota))
            ->when(! $anggota, fn ($q) => $q->whereRaw('1 = 0'))
            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('buku', function ($w) use ($search) {
                    $w->where('judul_buku', 'like', "%{$search}%")
                        ->orWhere('pengarang', 'like', "%{$search}%");
                });
            });

        $totalData = (clone $dasar)->count();
        $totalDipinjam = (clone $dasar)->where('status', 'dipinjam')->count();
        $totalSelesai = (clone $dasar)->where('status', 'dikembalikan')->count();

        $riwayat = (clone $dasar)
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->with(['buku'])
            ->latest('tgl_pinjam')
            ->paginate(10)
            ->withQueryString();

        return view('site.history', compact(
            'riwayat',
            'search',
            'status',
            'totalData',
            'totalDipinjam',
            'totalSelesai'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Transaksi;
use Illuminate\Http\Request;

/**
 * Dashboard ADMIN - statistik perpustakaan.
 */
class DashboardController extends Controller
{
    public function admin(Request $request)
    {
        $totalJudulBuku = Buku::count();
        $totalStokBuku = (int) Buku::sum('stok');
        $totalAnggota = Anggota::count();
        $totalTransaksi = Transaksi::count();
        $sedangDipinjam = Transaksi::where('status', 'dipinjam')->count();
        $sudahKembali = Transaksi::where('status', 'dikembalikan')->count();

        $peminjamanHariIni = Transaksi::whereDate('tgl_pinjam', today())->count();
        $pengembalianHariIni = Transaksi::whereDate('tgl_kembali', today())->count();

        $recentTransaksi = Transaksi::with(['anggota.user', 'buku'])
            ->orderByDesc('tgl_pinjam')
            ->take(5)
            ->get();

        return view('dashboard.admin', compact(
            'totalJudulBuku',
            'totalStokBuku',
            'totalAnggota',
            'totalTransaksi',
            'sedangDipinjam',
            'sudahKembali',
            'peminjamanHariIni',
            'pengembalianHariIni',
            'recentTransaksi'
        ));
    }
}

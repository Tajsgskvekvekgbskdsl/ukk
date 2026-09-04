<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Transaksi;
use Illuminate\Http\Request;

/**
 * ADMIN - REPORT perpustakaan.
 */
class ReportController extends Controller
{
    /**
     * Ringkasan statistik (Total Buku, Anggota, Transaksi,
     * Peminjaman, Pengembalian, anggota/peminjaman/pengembalian hari ini).
     */
    public function dashboard()
    {
        $totalBuku = Buku::count();
        $totalStok = (int) Buku::sum('stok');
        $totalAnggota = Anggota::count();
        $totalPeminjaman = Transaksi::where('status', 'dipinjam')->count();
        $totalPengembalian = Transaksi::where('status', 'dikembalikan')->count();
        $semuaTransaksi = Transaksi::count();

        $anggotaBaruHariIni = Anggota::whereDate('created_at', today())->count();

        $peminjamanHariIni = Transaksi::whereDate('tgl_pinjam', today())->count();
        $pengembalianHariIni = Transaksi::whereDate('tgl_kembali', today())->count();

        $recentPeminjaman = Transaksi::with(['anggota.user', 'buku'])
            ->orderByDesc('tgl_pinjam')
            ->take(5)
            ->get();

        $recentPengembalian = Transaksi::with(['anggota.user', 'buku'])
            ->whereNotNull('tgl_kembali')
            ->orderByDesc('tgl_kembali')
            ->take(5)
            ->get();

        return view('reports.dashboard', compact(
            'totalBuku',
            'totalStok',
            'totalAnggota',
            'totalPeminjaman',
            'totalPengembalian',
            'semuaTransaksi',
            'anggotaBaruHariIni',
            'peminjamanHariIni',
            'pengembalianHariIni',
            'recentPeminjaman',
            'recentPengembalian'
        ));
    }

    /**
     * Report per buku + jumlah transaksinya.
     */
    public function buku(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $bukus = Buku::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('judul_buku', 'like', "%{$search}%")
                        ->orWhere('pengarang', 'like', "%{$search}%")
                        ->orWhere('kategori', 'like', "%{$search}%");
                });
            })
            ->withCount(['transaksi as dipinjam_count' => fn ($q) => $q->where('status', 'dipinjam')])
            ->withCount('transaksi')
            ->orderBy('judul_buku')
            ->paginate(10)
            ->withQueryString();

        return view('reports.buku', compact('bukus', 'search'));
    }

    /**
     * Report per anggota + jumlah transaksinya.
     * (Sebelumnya halaman ini error karena relasi tidak sesuai ERD.)
     */
    public function anggota(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $anggotas = Anggota::query()
            ->with(['user:id_user,username,email'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('nama', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhere('kelas', 'like', "%{$search}%");
                });
            })
            ->withCount(['transaksi as sedang_pinjam' => fn ($q) => $q->where('status', 'dipinjam')])
            ->withCount('transaksi')
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('reports.anggota', compact('anggotas', 'search'));
    }

    /**
     * Report daftar peminjaman (+ filter tanggal & search).
     */
    public function peminjaman(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');

        $transaksis = Transaksi::query()
            ->with(['anggota.user', 'buku'])
            ->when($search !== '', function ($q) use ($search) {
                // PENTING: kondisi OR harus dibungkus closure agar tidak
                // merusak presedensi filter tanggal (WHERE ... AND ...).
                $q->where(function ($w) use ($search) {
                    $w->whereHas('anggota', function ($a) use ($search) {
                        $a->where('nama', 'like', "%{$search}%");
                    })->orWhereHas('buku', function ($b) use ($search) {
                        $b->where('judul_buku', 'like', "%{$search}%");
                    });
                });
            })
            ->when($dari, fn ($q) => $q->whereDate('tgl_pinjam', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('tgl_pinjam', '<=', $sampai))
            ->orderByDesc('tgl_pinjam')
            ->paginate(10)
            ->withQueryString();

        return view('reports.peminjaman', compact('transaksis', 'search', 'dari', 'sampai'));
    }

    /**
     * Report daftar pengembalian (+ filter tanggal & search).
     */
    public function pengembalian(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $dari = $request->input('dari');
        $sampai = $request->input('sampai');

        $transaksis = Transaksi::query()
            ->with(['anggota.user', 'buku'])
            ->whereNotNull('tgl_kembali')
            ->when($search !== '', function ($q) use ($search) {
                // PENTING: kondisi OR harus dibungkus closure agar tidak
                // merusak presedensi filter tanggal (WHERE ... AND ...).
                $q->where(function ($w) use ($search) {
                    $w->whereHas('anggota', function ($a) use ($search) {
                        $a->where('nama', 'like', "%{$search}%");
                    })->orWhereHas('buku', function ($b) use ($search) {
                        $b->where('judul_buku', 'like', "%{$search}%");
                    });
                });
            })
            ->when($dari, fn ($q) => $q->whereDate('tgl_kembali', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('tgl_kembali', '<=', $sampai))
            ->orderByDesc('tgl_kembali')
            ->paginate(10)
            ->withQueryString();

        return view('reports.pengembalian', compact('transaksis', 'search', 'dari', 'sampai'));
    }
}

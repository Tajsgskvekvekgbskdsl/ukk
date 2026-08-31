<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Website perpustakaan untuk USER & pengunjung umum:
 * Beranda, Katalog Buku (card), Detail Buku.
 */
class KatalogController extends Controller
{
    /**
     * Beranda publik: hero, buku terbaru, kategori.
     */
    public function beranda()
    {
        $bukuTerbaru = Buku::latest()->take(8)->get();
        $kategori = Buku::select('kategori')
            ->whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        $totalBuku = Buku::sum('stok');
        $totalJudul = Buku::count();
        $totalAnggota = Anggota::count();

        return view('site.beranda', compact(
            'bukuTerbaru',
            'kategori',
            'totalBuku',
            'totalJudul',
            'totalAnggota'
        ));
    }

    /**
     * Katalog buku: search + filter kategori + filter tersedia.
     */
    public function katalog(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $kategori = trim((string) $request->input('kategori'));
        $tersedia = $request->boolean('tersedia');

        $bukus = Buku::query()
            ->when($search !== '', function (Builder $q) use ($search) {
                $q->where(function (Builder $w) use ($search) {
                    $w->where('judul_buku', 'like', "%{$search}%")
                        ->orWhere('pengarang', 'like', "%{$search}%")
                        ->orWhere('penerbit', 'like', "%{$search}%");
                });
            })
            ->when($kategori !== '', fn (Builder $q) => $q->where('kategori', $kategori))
            ->when($tersedia, fn (Builder $q) => $q->where('stok', '>', 0))
            ->orderBy('judul_buku')
            ->paginate(12)
            ->withQueryString();

        $daftarKategori = Buku::select('kategori')
            ->whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        return view('site.katalog', compact(
            'bukus',
            'daftarKategori',
            'search',
            'kategori',
            'tersedia'
        ));
    }

    /**
     * Detail satu buku.
     */
    public function show(Buku $buku)
    {
        $serupa = Buku::where('kategori', $buku->kategori)
            ->where('id_buku', '!=', $buku->id_buku)
            ->take(4)
            ->get();

        // Jika user login, cek apakah sedang meminjam buku ini.
        $sedangDipinjam = false;
        $user = auth()->user();

        if ($user && $user->anggota && ! $user->isAdmin()) {
            $sedangDipinjam = Transaksi::where('id_anggota', $user->anggota->id_anggota)
                ->where('id_buku', $buku->id_buku)
                ->where('status', 'dipinjam')
                ->exists();
        }

        return view('site.detail', compact('buku', 'serupa', 'sedangDipinjam'));
    }
}

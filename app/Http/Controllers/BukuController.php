<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * ADMIN - CRUD BUKU (tabel buku, skema ERD).
 */
class BukuController extends Controller
{
    /**
     * Daftar buku + search.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $bukus = Buku::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('judul_buku', 'like', "%{$search}%")
                        ->orWhere('pengarang', 'like', "%{$search}%")
                        ->orWhere('penerbit', 'like', "%{$search}%")
                        ->orWhere('kategori', 'like', "%{$search}%");
                });
            })
            ->orderBy('judul_buku')
            ->paginate(10)
            ->withQueryString();

        return view('bukus.index', compact('bukus', 'search'));
    }

    public function create()
    {
        return view('bukus.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateBuku($request);

        // Upload cover jika ada.
        $data['gambar'] = $this->uploadCover($request);

        Buku::create($data);

        return redirect()->route('admin.buku.index')
            ->with('success', 'Data buku berhasil ditambahkan.');
    }

    public function show(Buku $buku)
    {
        // Muat statistik pinjam untuk halaman detail.
        $totalDipinjam = $buku->transaksi()->count();
        $sedangDipinjam = $buku->transaksi()->where('status', 'dipinjam')->count();

        return view('bukus.show', compact('buku', 'totalDipinjam', 'sedangDipinjam'));
    }

    public function edit(Buku $buku)
    {
        return view('bukus.edit', compact('buku'));
    }

    public function update(Request $request, Buku $buku)
    {
        $data = $this->validateBuku($request);

        // Jika cover baru diunggah, hapus cover lama lalu simpan yang baru.
        if ($request->hasFile('gambar')) {
            $this->hapusCoverLama($buku->gambar);
            $data['gambar'] = $this->uploadCover($request);
        }

        $buku->update($data);

        return redirect()->route('admin.buku.index')
            ->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Buku $buku)
    {
        // Buku yang pernah/sedang dipinjam tidak boleh dihapus
        // agar riwayat transaksi tetap utuh.
        if ($buku->transaksi()->exists()) {
            return redirect()->route('admin.buku.index')
                ->with('error', 'Buku tidak dapat dihapus karena sudah memiliki data transaksi peminjaman.');
        }

        // Hapus file cover dari storage jika ada.
        $this->hapusCoverLama($buku->gambar);

        $buku->delete();

        return redirect()->route('admin.buku.index')
            ->with('success', 'Data buku berhasil dihapus.');
    }

    /**
     * Validasi data buku. Stok tidak boleh negatif.
     */
    private function validateBuku(Request $request): array
    {
        return $request->validate([
            'judul_buku' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun_terbit' => 'nullable|integer|min:1900|max:' . date('Y'),
            'kategori' => 'nullable|string|max:100',
            'stok' => 'required|integer|min:0',
            'isbn' => 'nullable|string|max:255',
            'sinopsis' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
    }

    /**
     * Unggah file cover ke disk "public" di bawah folder "buku/".
     * Nama file diacak agar tidak bertabrakan.
     * Mengembalikan path relatif (misal: buku/abc123.jpg) untuk disimpan ke DB,
     * atau null jika tidak ada file.
     */
    private function uploadCover(Request $request): ?string
    {
        if (! $request->hasFile('gambar')) {
            return null;
        }

        // Store sebagai nama acak di folder "buku" pada disk "public".
        $path = $request->file('gambar')->store('buku', 'public');

        // $path sudah berupa path relatif (buku/xyz.jpg) — cocok untuk asset('storage/'.$path).
        return $path;
    }

    /**
     * Hapus file cover lama dari storage.
     * Aman dipanggil berulang kali karena cek eksistensi.
     */
    private function hapusCoverLama(?string $gambar): void
    {
        if ($gambar && Storage::disk('public')->exists($gambar)) {
            Storage::disk('public')->delete($gambar);
        }
    }
}

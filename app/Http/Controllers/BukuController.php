<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * ADMIN - CRUD BUKU
 */
class BukuController extends Controller
{
    /**
     * Daftar buku + pencarian.
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

    /**
     * Form tambah buku.
     */
    public function create()
    {
        return view('bukus.create');
    }

    /**
     * Simpan buku baru.
     */
    public function store(Request $request)
    {
        $data = $this->validateBuku($request);

        $data['gambar'] = $this->uploadCover($request);

        Buku::create($data);

        return redirect()
            ->route('admin.buku.index')
            ->with('success', 'Data buku berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail buku.
     */
    public function show(Buku $buku)
    {
        $totalDipinjam = $buku->transaksi()->count();

        $sedangDipinjam = $buku->transaksi()
            ->where('status', 'dipinjam')
            ->count();

        return view(
            'bukus.show',
            compact('buku', 'totalDipinjam', 'sedangDipinjam')
        );
    }

    /**
     * Form edit buku.
     */
    public function edit(Buku $buku)
    {
        return view('bukus.edit', compact('buku'));
    }

    /**
     * Update data buku.
     */
    public function update(Request $request, Buku $buku)
    {
        $data = $this->validateBuku($request);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $this->uploadCover($request);

            $this->hapusCoverLama($buku->gambar);
        }

        $buku->update($data);

        return redirect()
            ->route('admin.buku.index')
            ->with('success', 'Data buku berhasil diperbarui.');
    }

    /**
     * Hapus buku.
     */
    public function destroy(Buku $buku)
    {
        if ($buku->transaksi()->exists()) {
            return redirect()
                ->route('admin.buku.index')
                ->with(
                    'error',
                    'Buku tidak dapat dihapus karena sudah memiliki data transaksi peminjaman.'
                );
        }

        $this->hapusCoverLama($buku->gambar);

        $buku->delete();

        return redirect()
            ->route('admin.buku.index')
            ->with('success', 'Data buku berhasil dihapus.');
    }

    /**
     * Validasi data buku.
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
     * Upload cover buku ke Cloudinary.
     */
    private function uploadCover(Request $request): ?string
    {
        if (! $request->hasFile('gambar')) {
            return null;
        }

        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key' => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
        ]);

        $result = $cloudinary->uploadApi()->upload(
            $request->file('gambar')->getRealPath(),
            [
                'folder' => 'perpustakaan/buku',
                'resource_type' => 'image',
            ]
        );

        return $result['secure_url'] ?? null;
    }

    /**
     * Hapus cover lama.
     *
     * Cover Cloudinary tidak dihapus.
     * Cover lokal tetap dihapus dari storage.
     */
    private function hapusCoverLama(?string $gambar): void
    {
        if (! $gambar) {
            return;
        }

        // Kalau URL Cloudinary, jangan hapus dari storage lokal.
        if (
            str_starts_with($gambar, 'http://') ||
            str_starts_with($gambar, 'https://')
        ) {
            return;
        }

        if (Storage::disk('public')->exists($gambar)) {
            Storage::disk('public')->delete($gambar);
        }
    }
}
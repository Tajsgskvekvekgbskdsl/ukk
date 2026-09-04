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

        // Upload cover ke Cloudinary jika ada.
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

        // Jika ada cover baru, upload ke Cloudinary.
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $this->uploadCover($request);

            // Hapus cover lama jika masih berupa file lokal.
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
        // Buku yang pernah/sedang dipinjam tidak boleh dihapus.
        if ($buku->transaksi()->exists()) {
            return redirect()
                ->route('admin.buku.index')
                ->with(
                    'error',
                    'Buku tidak dapat dihapus karena sudah memiliki data transaksi peminjaman.'
                );
        }

        // Hapus cover lokal jika ada.
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
     *
     * Mengembalikan URL HTTPS Cloudinary.
     */
    private function uploadCover(Request $request): ?string
    {
        if (! $request->hasFile('gambar')) {
            return null;
        }

        $file = $request->file('gambar');

        // Pastikan file benar-benar diterima Laravel.
        if (! $file->isValid()) {
            throw new \RuntimeException(
                'File gambar gagal diupload: ' . $file->getErrorMessage()
            );
        }

        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
        ]);

        $result = $cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            [
                'folder' => 'perpustakaan/buku',
                'resource_type' => 'image',
            ]
        );

        // Pastikan Cloudinary mengembalikan URL.
        if (empty($result['secure_url'])) {
            throw new \RuntimeException(
                'Upload Cloudinary gagal: secure_url tidak ditemukan.'
            );
        }

        return $result['secure_url'];
    }

    /**
     * Hapus cover lama.
     *
     * Cover Cloudinary tidak dihapus.
     * Cover lokal lama tetap bisa dihapus.
     */
    private function hapusCoverLama(?string $gambar): void
    {
        if (! $gambar) {
            return;
        }

        // Jika sudah URL Cloudinary, jangan hapus dari storage lokal.
        if (
            str_starts_with($gambar, 'http://') ||
            str_starts_with($gambar, 'https://')
        ) {
            return;
        }

        // Hapus file lokal lama.
        if (Storage::disk('public')->exists($gambar)) {
            Storage::disk('public')->delete($gambar);
        }
    }
}
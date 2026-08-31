<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku';

    protected $primaryKey = 'id_buku';

    protected $fillable = [
        'judul_buku',
        'pengarang',
        'penerbit',
        'tahun_terbit',
        'kategori',
        'stok',
        'gambar',
        'isbn',
        'sinopsis',
    ];

    /**
     * Akses URL publik cover buku.
     * Jika belum ada gambar, mengembalikan null agar view
     * dapat menampilkan placeholder SVG berbasis nama.
     */
    public function getUrlCoverAttribute(): ?string
    {
        if (! $this->gambar) {
            return null;
        }

        return asset('storage/' . $this->gambar);
    }

    /**
     * ERD: buku 1 -- N transaksi.
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_buku', 'id_buku');
    }
}

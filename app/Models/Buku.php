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
     * URL cover buku.
     *
     * Jika gambar berupa URL Cloudinary,
     * langsung gunakan URL tersebut.
     *
     * Jika gambar berupa path lokal,
     * gunakan storage/public.
     */
    public function getUrlCoverAttribute(): ?string
    {
        if (empty($this->gambar)) {
            return null;
        }

        $gambar = trim($this->gambar);

        // Cover dari Cloudinary / URL eksternal
        if (
            str_starts_with($gambar, 'https://') ||
            str_starts_with($gambar, 'http://')
        ) {
            return $gambar;
        }

        // Cover lokal
        return asset('storage/' . ltrim($gambar, '/'));
    }

    /**
     * Relasi buku dengan transaksi.
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(
            Transaksi::class,
            'id_buku',
            'id_buku'
        );
    }
}
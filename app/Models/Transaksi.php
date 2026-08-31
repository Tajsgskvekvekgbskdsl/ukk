<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'id_anggota',
        'id_buku',
        'tgl_pinjam',
        'tgl_kembali',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tgl_pinjam' => 'date',
            'tgl_kembali' => 'date',
        ];
    }

    /**
     * Status transaksi yang masih meminjam buku.
     */
    public static function statusAktif(): array
    {
        return ['dipinjam'];
    }

    public function isAktif(): bool
    {
        return in_array($this->status, self::statusAktif(), true);
    }

    /**
     * ERD: transaksi N -- 1 anggota.
     */
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    /**
     * ERD: transaksi N -- 1 buku.
     */
    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }
}

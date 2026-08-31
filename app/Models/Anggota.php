<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggota';

    protected $primaryKey = 'id_anggota';

    protected $fillable = [
        'id_user',
        'nis',
        'nama',
        'kelas',
        'alamat',
    ];

    /**
     * ERD: anggota N -- 1 users.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * ERD: anggota 1 -- N transaksi.
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_anggota', 'id_anggota');
    }
}

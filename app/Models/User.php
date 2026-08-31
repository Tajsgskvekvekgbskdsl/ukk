<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'username',
        'nama_lengkap',
        'email',
        'password',
        'role',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Cek apakah user ini admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * ERD: users 1 -- 1 anggota.
     */
    public function anggota(): HasOne
    {
        return $this->hasOne(Anggota::class, 'id_user', 'id_user');
    }

    /**
     * Transaksi milik user ini (melalui anggotanya).
     */
    public function transaksis(): HasManyThrough
    {
        return $this->hasManyThrough(
            Transaksi::class,
            Anggota::class,
            'id_user',
            'id_anggota',
            'id_user',
            'id_anggota'
        );
    }
}

# TODO - Perpustakaan Sekolah Digital (UKK)

## Setup
- [x] Analisis kebutuhan & rencana
- [x] Konfigurasi .env ke MySQL database `ukk`

## Database & Migration
- [x] Tambah kolom role di users
- [x] Migration bukus
- [x] Migration anggotas
- [x] Migration transaksis

## Model & Relasi
- [x] Model Buku, Anggota, Transaksi
- [x] Update Model User (role + relasi)

## Middleware
- [x] AdminMiddleware
- [x] UserMiddleware

## Controller
- [x] AuthController (login/register)
- [x] DashboardController
- [x] BukuController (CRUD)
- [x] AnggotaController (CRUD)
- [x] TransaksiController (CRUD)
- [x] PeminjamanController
- [x] PengembalianController

## Route
- [x] Route admin & user

## View (Bootstrap 5)
- [x] Layout (navbar + sidebar)
- [x] Login, Register
- [x] Dashboard
- [x] Buku CRUD
- [x] Anggota CRUD
- [x] Transaksi CRUD
- [x] Peminjaman
- [x] Pengembalian

## Seeder
- [x] Admin & User
- [x] Data buku, anggota, transaksi

## Run
- [x] composer install
- [x] key:generate
- [x] migrate --seed
- [x] serve & testing

## Testing Results
- [x] Login Admin / User berhasil
- [x] Register berhasil
- [x] CRUD Buku berjalan
- [x] CRUD Anggota berjalan
- [x] CRUD Transaksi berjalan
- [x] Peminjaman (stok berkurang)
- [x] Pengembalian (stok bertambah)
- [x] Pencarian Data berjalan
- [x] Middleware role separation berjalan
- [x] Dashboard berjalan

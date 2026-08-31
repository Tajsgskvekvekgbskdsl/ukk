@extends('layouts.site')

@section('title', 'Tentang Perpustakaan')

@section('content')
    <header class="page-header">
        <div class="site-container">
            <h1><i class="bi bi-info-circle me-2" style="color:#16C94A;"></i>Tentang Perpustakaan Sekolah</h1>
            <p>Pusat sumber belajar untuk seluruh siswa dan guru.</p>
        </div>
    </header>

    <div class="site-container page-body">
        <div class="card mb-4" style="box-shadow: var(--shadow-halus);">
            <div class="card-body p-4 p-md-5">
                <p class="mb-0" style="font-size:1.02rem; max-width: 820px;">
                    Perpustakaan sekolah adalah pusat sumber belajar yang melayani seluruh siswa, guru,
                    dan tenaga kependidikan. Koleksi kami mencakup buku pelajaran, fiksi, teknologi,
                    dan referensi umum untuk mendukung proses belajar mengajar serta menumbuhkan minat baca.
                </p>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card h-100" style="box-shadow: var(--shadow-halus);">
                    <div class="card-body p-4">
                        <div class="stat-icon green mb-3"><i class="bi bi-journals"></i></div>
                        <h5 class="fw-bold mb-3">Layanan Kami</h5>
                        <ul class="mb-0 small text-muted" style="line-height: 2;">
                            <li><i class="bi bi-check2 me-2" style="color:#16C94A;"></i>Peminjaman buku untuk anggota terdaftar</li>
                            <li><i class="bi bi-check2 me-2" style="color:#16C94A;"></i>Baca di tempat untuk semua siswa</li>
                            <li><i class="bi bi-check2 me-2" style="color:#16C94A;"></i>Katalog daring (website ini)</li>
                            <li><i class="bi bi-check2 me-2" style="color:#16C94A;"></i>Riwayat pinjam milik anggota</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100" style="box-shadow: var(--shadow-halus);">
                    <div class="card-body p-4">
                        <div class="stat-icon yellow mb-3"><i class="bi bi-journal-check"></i></div>
                        <h5 class="fw-bold mb-3">Aturan Peminjaman</h5>
                        <ul class="mb-0 small text-muted" style="line-height: 2;">
                            <li><i class="bi bi-dot me-1"></i>Masa pinjam 7 hari</li>
                            <li><i class="bi bi-dot me-1"></i>Satu anggota satu eksemplar per judul</li>
                            <li><i class="bi bi-dot me-1"></i>Kembalikan tepat waktu agar bisa meminjam lagi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel ajakan --}}
        @guest
            <div class="login-home">
                <div class="lh-form">
                    <h2 class="mt-2 mb-2" style="font-size:1.5rem;">Mau ikut meminjam buku?</h2>
                    <p class="text-muted mb-4">Jadi anggota perpustakaan itu gratis — cukup daftar dengan NIS dan kelasmu.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('register') }}" class="btn btn-green"><i class="bi bi-person-plus"></i> Daftar Anggota</a>
                        <a href="{{ route('katalog') }}" class="btn btn-outline-navy"><i class="bi bi-collection"></i> Lihat Katalog</a>
                    </div>
                </div>
                <div class="lh-art">
                    @include('site._illustrasi')
                </div>
            </div>
        @endguest
    </div>
@endsection


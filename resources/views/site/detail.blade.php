@extends('layouts.site')

@section('title', $buku->judul_buku)

@php
    $coverClasses = ['cover-green', 'cover-blue', 'cover-pink', 'cover-yellow', 'cover-navy'];
    $seed = $buku->kategori ?: ($buku->judul_buku ?: 'x');
    $coverClass = $coverClasses[intdiv(hexdec(substr(md5($seed), 0, 6)), 1) % count($coverClasses)];
@endphp

@section('content')
    <header class="page-header">
        <div class="site-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('beranda') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('katalog') }}">Katalog</a></li>
                    <li class="breadcrumb-item active text-truncate" style="max-width:260px;">{{ $buku->judul_buku }}</li>
                </ol>
            </nav>
        </div>
    </header>

    <div class="site-container page-body">
        {{-- ===== DETAIL : cover KIRI, informasi KANAN ===== --}}
        <div class="card mb-5" style="box-shadow: var(--shadow-halus);">
            <div class="card-body p-4 p-md-5">
                <div class="row g-4 g-md-5 align-items-start">
                    <div class="col-md-4 col-lg-3">
                        <div class="book-cover detail-cover w-100 {{ $coverClass }}">
                            @if($buku->url_cover)
                                <img src="{{ $buku->url_cover }}" alt="Cover {{ $buku->judul_buku }}"
                                    style="width:100%; height:auto; max-height:460px; object-fit:contain;">
                            @else
                                <i class="bi bi-journal-bookmark-fill"></i>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-8 col-lg-9">
                        @if($buku->kategori)
                            <span class="badge-soft blue mb-2">{{ $buku->kategori }}</span>
                        @endif
                        <h1 class="mt-2 mb-1" style="font-size: clamp(1.45rem, 1.2rem + 1vw, 2rem);">{{ $buku->judul_buku }}</h1>
                        <p class="text-muted mb-4">oleh <strong>{{ $buku->pengarang ?: '-' }}</strong></p>

                        <dl class="row detail-info mb-4">
                            <dt class="col-sm-3">Penerbit</dt>
                            <dd class="col-sm-9">{{ $buku->penerbit ?? '-' }}</dd>

                            <dt class="col-sm-3">Tahun Terbit</dt>
                            <dd class="col-sm-9">{{ $buku->tahun_terbit ?? '-' }}</dd>

                            <dt class="col-sm-3">ISBN</dt>
                            <dd class="col-sm-9">{{ $buku->isbn ?? '-' }}</dd>

                            <dt class="col-sm-3">Kategori</dt>
                            <dd class="col-sm-9">{{ $buku->kategori ? strtoupper($buku->kategori) : '-' }}</dd>

                            <dt class="col-sm-3">Stok Tersedia</dt>
                            <dd class="col-sm-9">
                                @if($buku->stok > 0)
                                    <span class="badge-soft green">{{ $buku->stok }} eksemplar</span>
                                @else
                                    <span class="badge-soft gray">Habis</span>
                                @endif
                            </dd>

                            <dt class="col-sm-3">Status</dt>
                            <dd class="col-sm-9">
                                @if($sedangDipinjam)
                                    <span class="badge-soft yellow">Sedang Anda pinjam</span>
                                @elseif($buku->stok > 0)
                                    <span class="badge-soft green">Bisa dipinjam</span>
                                @else
                                    <span class="badge-soft gray">Tidak tersedia</span>
                                @endif
                            </dd>
                        </dl>

                        @if($buku->sinopsis)
                            <div class="mb-4">
                                <h3 class="h6 fw-bold mb-2" style="color:var(--navy);">Deskripsi</h3>
                                <p class="text-muted mb-0">{{ $buku->sinopsis }}</p>
                            </div>
                        @endif
                        @auth
                            @unless(auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('user.peminjaman.store') }}"
                                    onsubmit="return confirm('Pinjam buku ini sekarang?');">
                                    @csrf
                                    <input type="hidden" name="id_buku" value="{{ $buku->id_buku }}">
                                    <button type="submit" class="btn btn-green btn-lg"
                                        @if($buku->stok < 1 || $sedangDipinjam) disabled @endif>
                                        <i class="bi bi-journal-arrow-down"></i> Pinjam Buku
                                    </button>
                                    @if($buku->stok < 1)
                                        <small class="text-muted ms-2 d-inline-block mt-2">Stok habis — silakan pilih judul lain.</small>
                                    @elseif($sedangDipinjam)
                                        <small class="text-muted ms-2 d-inline-block mt-2">Anda masih meminjam buku ini.</small>
                                    @endif
                                </form>
                            @endunless
                        @else
                            <a href="{{ route('login') }}" class="btn btn-navy me-2"><i class="bi bi-box-arrow-in-right"></i> Login untuk Meminjam</a>
                            <a href="{{ route('register') }}" class="btn btn-outline-navy">Daftar Anggota</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== BUKU SEJENIS ===== --}}
        @if($serupa->isNotEmpty())
            <div class="section-head">
                <h2>Buku Sejenis</h2>
            </div>
            <div class="row g-3 g-md-4">
                @foreach($serupa as $s)
                    @include('site._buku-card', ['buku' => $s])
                @endforeach
            </div>
        @endif
    </div>
@endsection


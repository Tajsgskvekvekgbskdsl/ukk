@extends('layouts.site')

@section('title', 'Katalog Buku')

@section('content')
    {{-- ================= HEADER HALAMAN ================= --}}
    <header class="page-header">
        <div class="site-container d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h1><i class="bi bi-collection me-2" style="color:#16C94A;"></i>Katalog Buku</h1>
                <p>Jelajahi seluruh koleksi perpustakaan.</p>
            </div>
            <span class="chip" style="cursor:default;">
                <i class="bi bi-books"></i> Total: {{ $bukus->total() }} judul
            </span>
        </div>
    </header>

    <div class="site-container page-body">
        {{-- ===== FILTER BAR (fungsi pencarian tidak diubah) ===== --}}
        <form method="GET" action="{{ route('katalog') }}" class="filter-bar mb-4">
            <div class="row g-2 g-md-3 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label" for="f-search">Cari</label>
                    <input type="text" id="f-search" name="search" value="{{ $search }}"
                        class="form-control" placeholder="Judul, pengarang, penerbit...">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label" for="f-kategori">Kategori</label>
                    <select name="kategori" id="f-kategori" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($daftarKategori as $k)
                            <option value="{{ $k }}" {{ $kategori === $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <div class="form-check" style="padding-top:0.55rem;">
                        <input class="form-check-input" type="checkbox" id="tersedia"
                            name="tersedia" value="1" {{ $tersedia ? 'checked' : '' }}>
                        <label class="form-check-label" for="tersedia" style="font-weight:500;">Hanya tersedia</label>
                    </div>
                </div>
                <div class="col-12 col-md-2 d-grid d-md-block">
                    <button type="submit" class="btn btn-green w-100"><i class="bi bi-search"></i> Filter</button>
                </div>
            </div>
        </form>

        {{-- ===== GRID BUKU ===== --}}
        @if($bukus->isEmpty())
            <div class="empty-state">
                <div><i class="bi bi-search es-icon"></i></div>
                <p class="mb-0 fw-semibold">Buku tidak ditemukan.</p>
                <p class="small mb-0">Coba kata kunci atau kategori lain.</p>
            </div>
        @else
            <div class="row g-3 g-md-4">
                @foreach($bukus as $buku)
                    @include('site._buku-card', ['buku' => $buku])
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $bukus->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection


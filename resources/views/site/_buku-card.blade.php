{{-- Kartu Buku untuk katalog/beranda. Warna cover dipilih otomatis
     berdasarkan kategori/judul agar variasi terlihat natural. --}}
@php
    $coverClasses = ['cover-green', 'cover-blue', 'cover-pink', 'cover-yellow', 'cover-navy'];
    $seed = $buku->kategori ?: ($buku->judul_buku ?: 'x');
    $idx = intdiv(hexdec(substr(md5($seed), 0, 6)), 1) % count($coverClasses);
    $coverClass = $coverClasses[$idx];
@endphp

<div class="col-6 col-md-4 col-lg-3">
    <article class="card book-card h-100">
        <a href="{{ route('buku.detail', $buku) }}" class="book-cover {{ $coverClass }}" aria-label="Lihat detail {{ $buku->judul_buku }}">
            @if($buku->url_cover)
                <img src="{{ $buku->url_cover }}" alt="Cover {{ $buku->judul_buku }}"
                    style="width:100%; height:100%; object-fit:cover;">
            @else
                <i class="bi bi-journal-bookmark-fill"></i>
            @endif
        </a>
        <div class="card-body">
            <a href="{{ route('buku.detail', $buku) }}" class="book-title">{{ Str::limit($buku->judul_buku, 46) }}</a>
            <p class="book-author mb-1">oleh {{ $buku->pengarang ?: '-' }}</p>

            <div class="book-meta">
                @if($buku->kategori)
                    <span class="badge-soft blue">{{ Str::limit($buku->kategori, 22) }}</span>
                @endif
                @if($buku->tahun_terbit)
                    <span class="badge-soft gray">{{ $buku->tahun_terbit }}</span>
                @endif
            </div>

            <div class="mt-auto">
                @if($buku->stok > 0)
                    <span class="badge-soft green"><i class="bi bi-check2 me-1"></i>Tersedia: {{ $buku->stok }}</span>
                @else
                    <span class="badge-soft gray">Stok Habis</span>
                @endif
                <a href="{{ route('buku.detail', $buku) }}" class="btn btn-sm btn-green w-100 mt-2">
                    Detail Buku
                </a>
            </div>
        </div>
    </article>
</div>


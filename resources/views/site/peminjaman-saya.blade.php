@extends('layouts.site')

@section('title', 'Peminjaman Saya')

@section('content')
    <header class="page-header">
        <div class="site-container d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h1><i class="bi bi-journal-bookmark me-2" style="color:#16C94A;"></i>Peminjaman Saya</h1>
                <p>Daftar buku yang sedang kamu pinjam.</p>
            </div>
            <a href="{{ route('katalog') }}" class="btn btn-green"><i class="bi bi-plus-lg"></i> Pinjam Buku Lagi</a>
        </div>
    </header>

    <div class="site-container page-body">
        @if($peminjamans->isEmpty())
            <div class="empty-state">
                <div><i class="bi bi-journal-x es-icon"></i></div>
                <p class="mb-2 fw-semibold">Kamu tidak sedang meminjam buku.</p>
                <a href="{{ route('katalog') }}" class="btn btn-sm btn-green">Lihat katalog buku</a>
            </div>
        @else
            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Batas Kembali</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peminjamans as $i => $t)
                                @php($batas = $t->tgl_pinjam->copy()->addDays(7))
                                <tr>
                                    <td>{{ $peminjamans->firstItem() + $i }}</td>
                                    <td>
                                        <span class="fw-bold">{{ $t->buku->judul_buku }}</span><br>
                                        <small class="text-muted">{{ $t->buku->pengarang }}</small>
                                    </td>
                                    <td>{{ $t->tgl_pinjam->format('d M Y') }}</td>
                                    <td>
                                        {{ $batas->format('d M Y') }}
                                        @if(now()->gt($batas))
                                            <span class="badge-soft red ms-1">Terlambat</span>
                                        @endif
                                    </td>
                                    <td><span class="badge-soft blue">Dipinjam</span></td>
                                    <td class="text-end">
                                        <a href="{{ route('user.pengembalian.index') }}"
                                            class="btn btn-sm btn-green">Kembalikan</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $peminjamans->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection


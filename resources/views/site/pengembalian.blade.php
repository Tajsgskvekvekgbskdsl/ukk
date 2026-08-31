@extends('layouts.site')

@section('title', 'Pengembalian Buku')

@section('content')
    <header class="page-header">
        <div class="site-container">
            <h1><i class="bi bi-arrow-return-left me-2" style="color:#16C94A;"></i>Pengembalian Buku</h1>
            <p>Kembalikan buku yang sudah selesai kamu baca di sini.</p>
        </div>
    </header>

    <div class="site-container page-body">
        @if($pengembalians->isEmpty())
            <div class="empty-state">
                <div><i class="bi bi-check2-circle es-icon"></i></div>
                <p class="mb-1 fw-semibold">Tidak ada buku yang perlu dikembalikan.</p>
                <p class="small mb-0">Terima kasih sudah tertib mengembalikan buku! 🎉</p>
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
                                <th>Lama Pinjam</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengembalians as $i => $t)
                                @php($hari = $t->tgl_pinjam->diffInDays(now()))
                                <tr>
                                    <td>{{ $pengembalians->firstItem() + $i }}</td>
                                    <td>
                                        <span class="fw-bold">{{ $t->buku->judul_buku }}</span><br>
                                        <small class="text-muted">{{ $t->buku->pengarang }}</small>
                                    </td>
                                    <td>{{ $t->tgl_pinjam->format('d M Y') }}</td>
                                    <td>
                                        {{ $hari }} hari
                                        @if($hari > 7)
                                            <span class="badge-soft red ms-1">Terlambat</span>
                                        @else
                                            <span class="badge-soft green ms-1">Tepat waktu</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        {{-- Logic pengembalian tidak diubah --}}
                                        <form method="POST" action="{{ route('user.pengembalian.store', $t) }}"
                                            onsubmit="return confirm('Kembalikan buku ini sekarang?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-green">
                                                <i class="bi bi-box-arrow-up"></i> Kembalikan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $pengembalians->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection


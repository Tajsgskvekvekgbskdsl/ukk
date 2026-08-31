@extends('layouts.site')

@section('title', 'History Peminjaman Saya')

@section('content')
    <header class="page-header">
        <div class="site-container d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h1><i class="bi bi-clock-history me-2" style="color:#16C94A;"></i>History Peminjaman</h1>
                <p>Semua catatan pinjam &amp; kembali buku milikmu.</p>
            </div>
            <span class="chip" style="cursor:default;">
                <i class="bi bi-receipt"></i> {{ $totalData }} transaksi
            </span>
        </div>
    </header>

    <div class="site-container page-body">
        {{-- Filter riwayat: fungsi tidak berubah --}}
        <form method="GET" action="{{ route('user.riwayat.index') }}" class="filter-bar mb-4">
            <div class="row g-2 g-md-3 align-items-end">
                <div class="col-12 col-md-5">
                    <label class="form-label" for="h-search">Cari</label>
                    <input type="text" id="h-search" name="search" value="{{ $search }}"
                        class="form-control" placeholder="Judul / pengarang...">
                </div>
                <div class="col-8 col-md-4">
                    <label class="form-label" for="h-status">Status</label>
                    <select name="status" id="h-status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="dipinjam" {{ $status === 'dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                        <option value="dikembalikan" {{ $status === 'dikembalikan' ? 'selected' : '' }}>Sudah Dikembalikan</option>
                    </select>
                </div>
                <div class="col-4 col-md-2 d-grid">
                    <button type="submit" class="btn btn-green"><i class="bi bi-search"></i> Cari</button>
                </div>
            </div>
        </form>

        @if($riwayat->isEmpty())
            <div class="empty-state">
                <div><i class="bi bi-clock-history es-icon"></i></div>
                <p class="mb-0 fw-semibold">Belum ada riwayat transaksi.</p>
                <p class="small mb-0">Riwayat muncul setelah kamu meminjam buku pertama.</p>
            </div>
        @else
            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayat as $i => $t)
                                <tr>
                                    <td>{{ $riwayat->firstItem() + $i }}</td>
                                    <td>
                                        <span class="fw-bold">{{ $t->buku->judul_buku }}</span><br>
                                        <small class="text-muted">{{ $t->buku->pengarang }}</small>
                                    </td>
                                    <td>{{ $t->tgl_pinjam->format('d M Y') }}</td>
                                    <td>{{ $t->tgl_kembali?->format('d M Y') ?? '-' }}</td>
                                    <td>
                                        @if($t->status === 'dipinjam')
                                            <span class="badge-soft blue">Dipinjam</span>
                                        @else
                                            <span class="badge-soft green">Dikembalikan</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $riwayat->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection


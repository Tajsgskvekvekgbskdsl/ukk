@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Perpustakaan')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted">Koleksi Judul</small>
                        <h2 class="mb-0">{{ $totalJudulBuku }}</h2>
                        <small class="text-muted">Total stok: {{ $totalStokBuku }}</small>
                    </div>
                    <i class="bi bi-book fs-1" style="color:#16C94A;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted">Anggota</small>
                        <h2 class="mb-0">{{ $totalAnggota }}</h2>
                        <small class="text-muted">siswa terdaftar</small>
                    </div>
                    <i class="bi bi-people fs-1" style="color:#16C94A;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted">Sedang Dipinjam</small>
                        <h2 class="mb-0">{{ $sedangDipinjam }}</h2>
                        <small class="text-muted">{{ $sudahKembali }} sudah kembali</small>
                    </div>
                    <i class="bi bi-journal-arrow-down fs-1" style="color:#16C94A;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted">Total Transaksi</small>
                        <h2 class="mb-0">{{ $totalTransaksi }}</h2>
                        <small class="text-muted">{{ $peminjamanHariIni }} pinjam hari ini</small>
                    </div>
                    <i class="bi bi-arrow-left-right fs-1" style="color:#16C94A;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Transaksi Terbaru --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                Transaksi Terbaru
                <a href="{{ route('admin.transaksi.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransaksi as $t)
                            <tr>
                                <td>{{ $t->anggota?->nama ?? '-' }}<br>
                                    <small class="text-muted">{{ $t->anggota?->kelas }}</small></td>
                                <td>{{ $t->buku?->judul_buku }}</td>
                                <td>{{ \Carbon\Carbon::parse($t->tgl_pinjam)->format('d M Y') }}</td>
                                <td>
                                    <span class="badge {{ $t->status === 'dipinjam' ? 'bg-primary' : 'bg-success' }}">
                                        {{ ucfirst($t->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Aktivitas hari ini --}}
    <div class="col-lg-4 d-flex flex-column gap-4">
        <div class="card">
            <div class="card-header">Hari Ini</div>
            <div class="card-body small">
                <p class="mb-2"><i class="bi bi-plus-circle" style="color:#16C94A;"></i> Peminjaman baru:
                    <strong>{{ $peminjamanHariIni }}</strong></p>
                <p class="mb-0"><i class="bi bi-arrow-return-left" style="color:#16C94A;"></i> Pengembalian:
                    <strong>{{ $pengembalianHariIni }}</strong></p>
            </div>
        </div>

        <div class="card flex-grow-1">
            <div class="card-header">Aksi Cepat</div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('admin.buku.create') }}" class="btn btn-navy btn-sm"><i class="bi bi-book"></i> Tambah Buku</a>
                <a href="{{ route('admin.anggota.create') }}" class="btn btn-navy btn-sm"><i class="bi bi-person-plus"></i> Tambah Anggota</a>
                <a href="{{ route('admin.transaksi.create') }}" class="btn btn-navy btn-sm"><i class="bi bi-plus-circle"></i> Buat Transaksi</a>
                <a href="{{ route('admin.report.dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-bar-graph"></i> Lihat Report</a>
            </div>
        </div>
    </div>
</div>
@endsection

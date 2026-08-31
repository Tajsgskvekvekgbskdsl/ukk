@extends('layouts.app')

@section('title', 'Report Ringkasan')
@section('page-title', 'Report Ringkasan Perpustakaan')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-2">
        <div class="card text-center py-3 stat-card"><small class="text-muted">Total Judul Buku</small><h3 class="mb-0">{{ $totalBuku }}</h3></div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="card text-center py-3 stat-card"><small class="text-muted">Total Stok</small><h3 class="mb-0">{{ $totalStok }}</h3></div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="card text-center py-3 stat-card"><small class="text-muted">Total Anggota</small><h3 class="mb-0">{{ $totalAnggota }}</h3></div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="card text-center py-3 stat-card"><small class="text-muted">Sedang Dipinjam</small><h3 class="mb-0">{{ $totalPeminjaman }}</h3></div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="card text-center py-3 stat-card"><small class="text-muted">Pengembalian</small><h3 class="mb-0">{{ $totalPengembalian }}</h3></div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="card text-center py-3 stat-card"><small class="text-muted">Semua Transaksi</small><h3 class="mb-0">{{ $semuaTransaksi }}</h3></div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center py-3 h-100">
            <small class="text-muted">Anggota Baru Hari Ini</small><h4 class="mb-0" style="color:#111827;">{{ $anggotaBaruHariIni }}</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-3 h-100">
            <small class="text-muted">Peminjaman Hari Ini</small><h4 class="mb-0" style="color:#111827;">{{ $peminjamanHariIni }}</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-3 h-100">
            <small class="text-muted">Pengembalian Hari Ini</small><h4 class="mb-0" style="color:#111827;">{{ $pengembalianHariIni }}</h4>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">Peminjaman Terbaru</div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead><tr><th>Anggota</th><th>Buku</th><th>Tgl</th></tr></thead>
                    <tbody>
                        @forelse($recentPeminjaman as $t)
                            <tr>
                                <td>{{ $t->anggota?->nama }}</td>
                                <td>{{ Str::limit($t->buku?->judul_buku, 30) }}</td>
                                <td>{{ \Carbon\Carbon::parse($t->tgl_pinjam)->format('d M') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">Pengembalian Terbaru</div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead><tr><th>Anggota</th><th>Buku</th><th>Tgl Kembali</th></tr></thead>
                    <tbody>
                        @forelse($recentPengembalian as $t)
                            <tr>
                                <td>{{ $t->anggota?->nama }}</td>
                                <td>{{ Str::limit($t->buku?->judul_buku, 30) }}</td>
                                <td>{{ \Carbon\Carbon::parse($t->tgl_kembali)->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

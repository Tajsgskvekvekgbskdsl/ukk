@extends('layouts.app')

@section('title', 'Report Buku')
@section('page-title', 'Report Buku')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>Report Per Buku — total transaksi & stok</span>
        <form method="GET" action="{{ route('admin.report.buku') }}" class="d-flex gap-1">
            <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm"
                placeholder="Cari judul / pengarang / kategori..." style="min-width:220px;">
            <button type="submit" class="btn btn-sm btn-navy"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Buku</th>
                    <th>Pengarang</th>
                    <th>Kategori</th>
                    <th class="text-center">Stok Kini</th>
                    <th class="text-center">Total Dipinjam</th>
                    <th class="text-center">Sedang Keluar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bukus as $i => $b)
                    <tr>
                        <td>{{ $bukus->firstItem() + $i }}</td>
                        <td class="fw-semibold">{{ $b->judul_buku }}</td>
                        <td>{{ $b->pengarang }}</td>
                        <td>{{ $b->kategori ? strtoupper($b->kategori) : '-' }}</td>
                        <td class="text-center">{{ $b->stok }}</td>
                        <td class="text-center">{{ $b->transaksi_count }}</td>
                        <td class="text-center">{{ $b->dipinjam_count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Data tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bukus->hasPages())
        <div class="card-footer bg-white">{{ $bukus->links() }}</div>
    @endif
</div>
@endsection

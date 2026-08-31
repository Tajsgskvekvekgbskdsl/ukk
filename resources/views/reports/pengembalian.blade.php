@extends('layouts.app')

@section('title', 'Report Pengembalian')
@section('page-title', 'Report Pengembalian')

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('admin.report.pengembalian') }}" class="row g-2 align-items-end">
            <div class="col-md-3 col-6">
                <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Cari anggota / buku...">
            </div>
            <div class="col-md-3 col-6">
                <label class="form-label small mb-0">Kembali dari</label>
                <input type="date" name="dari" value="{{ $dari }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3 col-6">
                <label class="form-label small mb-0">sampai</label>
                <input type="date" name="sampai" value="{{ $sampai }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3 col-6 d-grid">
                <button type="submit" class="btn btn-sm btn-navy"><i class="bi bi-funnel"></i> Filter</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr><th>No</th><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th><th>Tgl Kembali</th></tr>
            </thead>
            <tbody>
                @forelse($transaksis as $i => $t)
                    <tr>
                        <td>{{ $transaksis->firstItem() + $i }}</td>
                        <td>{{ $t->anggota?->nama ?? '-' }}<br>
                            <small class="text-muted">{{ $t->anggota?->kelas }} · NIS {{ $t->anggota?->nis }}</small></td>
                        <td>{{ $t->buku?->judul_buku }}</td>
                        <td>{{ \Carbon\Carbon::parse($t->tgl_pinjam)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($t->tgl_kembali)->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Data tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transaksis->hasPages())
        <div class="card-footer bg-white">{{ $transaksis->links() }}</div>
    @endif
</div>
@endsection

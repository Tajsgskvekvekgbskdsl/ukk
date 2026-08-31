@extends('layouts.app')

@section('title', 'History')
@section('page-title', 'History Seluruh Transaksi')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>History Peminjaman & Pengembalian</span>
        <form method="GET" action="{{ route('admin.history') }}" class="d-flex gap-1 flex-wrap">
            <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm"
                placeholder="Cari anggota / buku..." style="min-width:180px;">
            <select name="status" class="form-select form-select-sm">
                <option value="">Semua Status</option>
                <option value="dipinjam" {{ $status === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="dikembalikan" {{ $status === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
            </select>
            <button type="submit" class="btn btn-sm btn-navy"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Anggota</th>
                    <th>Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($histories as $i => $t)
                    <tr>
                        <td>{{ $histories->firstItem() + $i }}</td>
                        <td>{{ $t->anggota?->nama ?? '-' }}
                            <small class="d-block text-muted">{{ $t->anggota?->kelas }} · NIS {{ $t->anggota?->nis }}</small></td>
                        <td>{{ $t->buku?->judul_buku }}</td>
                        <td>{{ \Carbon\Carbon::parse($t->tgl_pinjam)->format('d M Y') }}</td>
                        <td>{{ $t->tgl_kembali ? \Carbon\Carbon::parse($t->tgl_kembali)->format('d M Y') : '-' }}</td>
                        <td>
                            <span class="badge {{ $t->status === 'dipinjam' ? 'bg-primary' : 'bg-success' }}">
                                {{ ucfirst($t->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada history transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($histories->hasPages())
        <div class="card-footer bg-white">{{ $histories->links() }}</div>
    @endif
</div>
@endsection

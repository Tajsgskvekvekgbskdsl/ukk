@extends('layouts.app')

@section('title', 'Report Anggota')
@section('page-title', 'Report Anggota')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>Report Per Anggota — jumlah transaksi peminjaman</span>
        <form method="GET" action="{{ route('admin.report.anggota') }}" class="d-flex gap-1">
            <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm"
                placeholder="Cari nama / NIS / kelas..." style="min-width:200px;">
            <button type="submit" class="btn btn-sm btn-navy"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama Anggota</th>
                    <th>Kelas</th>
                    <th>Username</th>
                    <th class="text-center">Total Transaksi</th>
                    <th class="text-center">Sedang Pinjam</th>
                </tr>
            </thead>
            <tbody>
                @forelse($anggotas as $i => $a)
                    <tr>
                        <td>{{ $anggotas->firstItem() + $i }}</td>
                        <td>{{ $a->nis ?? '-' }}</td>
                        <td class="fw-semibold">{{ $a->nama }}</td>
                        <td>{{ $a->kelas ?? '-' }}</td>
                        <td>{{ $a->user->username ?? '-' }}</td>
                        <td class="text-center">{{ $a->transaksi_count }}</td>
                        <td class="text-center">
                            <span class="badge {{ $a->sedang_pinjam > 0 ? 'bg-primary' : 'bg-light text-dark border' }}">
                                {{ $a->sedang_pinjam }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Data tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($anggotas->hasPages())
        <div class="card-footer bg-white">{{ $anggotas->links() }}</div>
    @endif
</div>
@endsection

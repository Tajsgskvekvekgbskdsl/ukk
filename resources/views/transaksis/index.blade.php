@extends('layouts.app')

@section('title', 'Transaksi')
@section('page-title', 'Data Transaksi')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>Daftar Transaksi Peminjaman</span>
        <div class="d-flex gap-2 flex-wrap">
            <form method="GET" action="{{ route('admin.transaksi.index') }}" class="d-flex gap-1 flex-wrap">
                <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm"
                    placeholder="Cari anggota / buku..." style="min-width:180px;">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="dipinjam" {{ $status === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="dikembalikan" {{ $status === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                </select>
                <button type="submit" class="btn btn-sm btn-navy"><i class="bi bi-search"></i></button>
            </form>
            <a href="{{ route('admin.transaksi.create') }}" class="btn btn-sm btn-gold">
                <i class="bi bi-plus-lg"></i> Tambah Transaksi
            </a>
        </div>
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
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksis as $i => $t)
                    <tr>
                        <td>{{ $transaksis->firstItem() + $i }}</td>
                        <td>
                            {{ $t->anggota->nama ?? '-' }}
                            <small class="d-block text-muted">{{ $t->anggota?->kelas }} · {{ $t->anggota?->nis }}</small>
                        </td>
                        <td>{{ $t->buku->judul_buku ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($t->tgl_pinjam)->format('d M Y') }}</td>
                        <td>{{ $t->tgl_kembali ? \Carbon\Carbon::parse($t->tgl_kembali)->format('d M Y') : '-' }}</td>
                        <td>
                            <span class="badge {{ $t->status === 'dipinjam' ? 'bg-primary' : 'bg-success' }}">
                                {{ ucfirst($t->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.transaksi.show', $t) }}" class="btn btn-outline-info" title="Detail"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.transaksi.edit', $t) }}" class="btn btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.transaksi.destroy', $t) }}"
                                    onsubmit="return confirm('Yakin hapus transaksi ini?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Data transaksi tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transaksis->hasPages())
        <div class="card-footer bg-white">{{ $transaksis->links() }}</div>
    @endif
</div>
@endsection

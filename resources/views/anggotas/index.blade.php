@extends('layouts.app')

@section('title', 'Data Anggota')
@section('page-title', 'Data Anggota')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>Daftar Anggota Perpustakaan</span>
        <div class="d-flex gap-2 flex-wrap">
            <form method="GET" action="{{ route('admin.anggota.index') }}" class="d-flex gap-1">
                <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm"
                    placeholder="Cari nama / NIS / kelas / username..." style="min-width:220px;">
                <button type="submit" class="btn btn-sm btn-navy"><i class="bi bi-search"></i></button>
                @if($search !== '')
                    <a href="{{ route('admin.anggota.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </form>
            <a href="{{ route('admin.anggota.create') }}" class="btn btn-sm btn-gold">
                <i class="bi bi-person-plus"></i> Tambah Anggota
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama Anggota</th>
                    <th>Kelas</th>
                    <th>Akun (Username)</th>
                    <th>Email Akun</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($anggotas as $i => $anggota)
                    <tr>
                        <td>{{ $anggotas->firstItem() + $i }}</td>
                        <td>{{ $anggota->nis ?? '-' }}</td>
                        <td class="fw-semibold">{{ $anggota->nama }}</td>
                        <td>{{ $anggota->kelas ?? '-' }}</td>
                        <td>{{ $anggota->user->username ?? '-' }}</td>
                        <td>{{ $anggota->user->email ?? '-' }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.anggota.show', $anggota) }}" class="btn btn-outline-info" title="Detail"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.anggota.edit', $anggota) }}" class="btn btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.anggota.destroy', $anggota) }}"
                                    onsubmit="return confirm('Yakin hapus anggota ini beserta akunnya?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Data anggota tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($anggotas->hasPages())
        <div class="card-footer bg-white">{{ $anggotas->links() }}</div>
    @endif
</div>
@endsection

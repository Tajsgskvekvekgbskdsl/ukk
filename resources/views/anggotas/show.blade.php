@extends('layouts.app')

@section('title', 'Detail Anggota')
@section('page-title', 'Detail Anggota')

@section('content')
<div class="row g-4">
    {{-- Identitas --}}
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header">Identitas Anggota</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr><th style="width:130px;">NIS</th><td>{{ $anggota->nis ?? '-' }}</td></tr>
                        <tr><th>Nama</th><td class="fw-semibold">{{ $anggota->nama }}</td></tr>
                        <tr><th>Kelas</th><td>{{ $anggota->kelas ?? '-' }}</td></tr>
                        <tr><th>Alamat</th><td>{{ $anggota->alamat ?? '-' }}</td></tr>
                        <tr><th>Terdaftar</th><td>{{ $anggota->created_at->format('d M Y') }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Akun + transaksi --}}
    <div class="col-md-7 d-flex flex-column gap-4">
        <div class="card">
            <div class="card-header">Akun Login Terhubung (users.id_user = {{ $anggota->id_user }})</div>
            <div class="card-body py-2 small">
                Username: <strong>{{ $anggota->user->username ?? '-' }}</strong> &nbsp;|&nbsp;
                Email: {{ $anggota->user->email ?? '-' }} &nbsp;|&nbsp;
                Role: {{ $anggota->user->role ?? '-' }}
            </div>
        </div>

        <div class="card flex-grow-1">
            <div class="card-header">Riwayat Transaksi Anggota</div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($anggota->transaksi as $t)
                            <tr>
                                <td>{{ $t->buku->judul_buku }}</td>
                                <td>{{ $t->tgl_pinjam->format('d M Y') }}</td>
                                <td>{{ $t->tgl_kembali?->format('d M Y') ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $t->status === 'dipinjam' ? 'bg-primary' : 'bg-success' }}">
                                        {{ ucfirst($t->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.anggota.edit', $anggota) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('admin.anggota.index') }}" class="btn btn-outline-secondary ms-auto">Kembali</a>
        </div>
    </div>
</div>
@endsection

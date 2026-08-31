@extends('layouts.app')

@section('title', 'Edit Transaksi')
@section('page-title', 'Edit Transaksi')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header">Form Edit Transaksi</div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger small">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger small">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.transaksi.update', $transaksi) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Anggota / Peminjam</label>
                        <select name="id_anggota" class="form-select @error('id_anggota') is-invalid @enderror" required>
                            @foreach($anggotas as $a)
                                <option value="{{ $a->id_anggota }}"
                                    {{ (int) old('id_anggota', $transaksi->id_anggota) === $a->id_anggota ? 'selected' : '' }}>
                                    {{ $a->nama }}{{ $a->kelas ? " ({$a->kelas})" : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_anggota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Buku</label>
                        <select name="id_buku" class="form-select @error('id_buku') is-invalid @enderror" required>
                            {{-- Buku milik transaksi ini tetap tampil walau stok 0 --}}
                            <option value="{{ $transaksi->buku->id_buku }}">
                                {{ $transaksi->buku->judul_buku }} (saat ini)
                            </option>
                            @foreach($bukus as $b)
                                @continue((int) $b->id_buku === (int) $transaksi->buku->id_buku)
                                <option value="{{ $b->id_buku }}"
                                    {{ (int) old('id_buku', $transaksi->id_buku) === $b->id_buku ? 'selected' : '' }}>
                                    {{ $b->judul_buku }} (Stok: {{ $b->stok }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_buku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_pinjam" value="{{ old('tgl_pinjam', $transaksi->tgl_pinjam?->format('Y-m-d')) }}"
                                class="form-control @error('tgl_pinjam') is-invalid @enderror" required>
                            @error('tgl_pinjam')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="statusSelect"
                                class="form-select @error('status') is-invalid @enderror" required>
                                <option value="dipinjam" {{ old('status', $transaksi->status) === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                <option value="dikembalikan" {{ old('status', $transaksi->status) === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Tanggal Kembali Aktual</label>
                            <input type="date" name="tgl_kembali" value="{{ old('tgl_kembali', $transaksi->tgl_kembali?->format('Y-m-d')) }}"
                                class="form-control @error('tgl_kembali') is-invalid @enderror">
                            @error('tgl_kembali')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Kosongkan bila masih dipinjam; dikosongkan otomatis saat status = Dipinjam.</small>
                        </div>
                    </div>

                    <p class="small text-muted mt-3 mb-0">
                        <i class="bi bi-info-circle"></i> Stok disesuaikan otomatis mengikuti perubahan status/buku.
                    </p>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-navy"><i class="bi bi-save"></i> Perbarui</button>
                        <a href="{{ route('admin.transaksi.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

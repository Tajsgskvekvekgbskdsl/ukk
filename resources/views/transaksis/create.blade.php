@extends('layouts.app')

@section('title', 'Tambah Transaksi')
@section('page-title', 'Tambah Transaksi')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header">Form Tambah Transaksi Peminjaman</div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger small">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger small">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.transaksi.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Anggota / Peminjam <span class="text-danger">*</span></label>
                        <select name="id_anggota" class="form-select @error('id_anggota') is-invalid @enderror" required>
                            <option value="">-- Pilih Anggota --</option>
                            @foreach($anggotas as $a)
                                <option value="{{ $a->id_anggota }}" {{ old('id_anggota') == $a->id_anggota ? 'selected' : '' }}>
                                    {{ $a->nama }}{{ $a->kelas ? " ({$a->kelas})" : '' }}{{ $a->nis ? " - NIS {$a->nis}" : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_anggota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Buku <span class="text-danger">*</span></label>
                        <select name="id_buku" class="form-select @error('id_buku') is-invalid @enderror" required>
                            <option value="">-- Pilih Buku (stok tersedia) --</option>
                            @foreach($bukus as $b)
                                <option value="{{ $b->id_buku }}" {{ old('id_buku') == $b->id_buku ? 'selected' : '' }}>
                                    {{ $b->judul_buku }} (Stok: {{ $b->stok }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_buku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                        <input type="date" name="tgl_pinjam" value="{{ old('tgl_pinjam', date('Y-m-d')) }}"
                            class="form-control @error('tgl_pinjam') is-invalid @enderror" required>
                        @error('tgl_pinjam')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <p class="small text-muted mb-3"><i class="bi bi-info-circle"></i> Stok buku otomatis berkurang 1 setelah transaksi disimpan.</p>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-navy"><i class="bi bi-save"></i> Simpan</button>
                        <a href="{{ route('admin.transaksi.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

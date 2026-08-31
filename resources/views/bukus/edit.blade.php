@extends('layouts.app')

@section('title', 'Edit Buku')
@section('page-title', 'Edit Buku')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9 col-lg-7">
        <div class="card">
            <div class="card-header">Form Edit Buku</div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger small">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.buku.update', $buku) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Judul Buku <span class="text-danger">*</span></label>
                        <input type="text" name="judul_buku" value="{{ old('judul_buku', $buku->judul_buku) }}"
                            class="form-control @error('judul_buku') is-invalid @enderror" required>
                        @error('judul_buku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Pengarang <span class="text-danger">*</span></label>
                            <input type="text" name="pengarang" value="{{ old('pengarang', $buku->pengarang) }}"
                                class="form-control @error('pengarang') is-invalid @enderror" required>
                            @error('pengarang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Penerbit</label>
                            <input type="text" name="penerbit" value="{{ old('penerbit', $buku->penerbit) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tahun Terbit</label>
                            <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}"
                                min="1900" max="{{ date('Y') }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <input type="text" name="kategori" value="{{ old('kategori', $buku->kategori) }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stok <span class="text-danger">*</span></label>
                            <input type="number" name="stok" value="{{ old('stok', $buku->stok) }}" min="0"
                                class="form-control @error('stok') is-invalid @enderror" required>
                            @error('stok')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cover Buku</label>
                            {{-- Preview cover lama (jika ada) --}}
                            @if($buku->gambar && $buku->url_cover)
                                <div class="mb-2">
                                    <label class="form-label fw-normal text-muted small mb-1">Cover saat ini:</label>
                                    <div class="d-inline-block">
                                        <img src="{{ $buku->url_cover }}" alt="Cover lama"
                                            style="max-width: 80px; max-height: 100px; object-fit: cover; border-radius: 6px; border: 1px solid var(--garis);">
                                    </div>
                                </div>
                            @endif
                            <input type="file" name="gambar" accept="image/jpeg,image/png,image/webp"
                                class="form-control @error('gambar') is-invalid @enderror">
                            @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                        <small class="text-muted d-block mt-1">Format: JPG, PNG, WebP. Maksimal 2 MB. Biarkan kosong untuk mempertahankan cover lama.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">ISBN</label>
                            <input type="text" name="isbn" value="{{ old('isbn', $buku->isbn) }}"
                                placeholder="opsional" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Sinopsis</label>
                            <textarea name="sinopsis" rows="3"
                                class="form-control" placeholder="opsional">{{ old('sinopsis', $buku->sinopsis) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-navy"><i class="bi bi-save"></i> Perbarui</button>
                        <a href="{{ route('admin.buku.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

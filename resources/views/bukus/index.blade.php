@extends('layouts.app')

@section('title', 'Data Buku')
@section('page-title', 'Data Buku')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span>Daftar Koleksi Buku</span>

        <div class="d-flex gap-2 flex-wrap">
            <form method="GET" action="{{ route('admin.buku.index') }}" class="d-flex gap-1">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    class="form-control form-control-sm"
                    placeholder="Cari judul / pengarang / kategori..."
                    style="min-width:220px;"
                >

                <button type="submit" class="btn btn-sm btn-navy">
                    <i class="bi bi-search"></i>
                </button>

                @if($search !== '')
                    <a
                        href="{{ route('admin.buku.index') }}"
                        class="btn btn-sm btn-outline-secondary"
                    >
                        Reset
                    </a>
                @endif
            </form>

            <a
                href="{{ route('admin.buku.create') }}"
                class="btn btn-sm btn-gold"
            >
                <i class="bi bi-plus-lg"></i> Tambah Buku
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Cover</th>
                    <th>Judul Buku</th>
                    <th>Pengarang</th>
                    <th>Penerbit</th>
                    <th>Tahun</th>
                    <th>Kategori</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($bukus as $i => $buku)
                    <tr>
                        <td>
                            {{ $bukus->firstItem() + $i }}
                        </td>

                        {{-- COVER --}}
                        <td class="text-center align-middle">
                            @if($buku->gambar)
                                <img
                                    src="{{ $buku->gambar }}"
                                    alt="Cover {{ $buku->judul_buku }}"
                                    style="
                                        width:40px;
                                        height:55px;
                                        object-fit:cover;
                                        border-radius:4px;
                                    "
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';"
                                >

                                <span
                                    style="
                                        width:40px;
                                        height:55px;
                                        display:none;
                                        align-items:center;
                                        justify-content:center;
                                        background:rgba(0,0,0,.08);
                                        border-radius:4px;
                                    "
                                >
                                    <i
                                        class="bi bi-image"
                                        style="font-size:1.2rem; color:#9CA3AF;"
                                    ></i>
                                </span>
                            @else
                                <span
                                    style="
                                        width:40px;
                                        height:55px;
                                        display:inline-flex;
                                        align-items:center;
                                        justify-content:center;
                                        background:rgba(0,0,0,.08);
                                        border-radius:4px;
                                    "
                                >
                                    <i
                                        class="bi bi-image"
                                        style="font-size:1.2rem; color:#9CA3AF;"
                                    ></i>
                                </span>
                            @endif
                        </td>

                        <td class="fw-semibold">
                            {{ $buku->judul_buku }}
                        </td>

                        <td>
                            {{ $buku->pengarang }}
                        </td>

                        <td>
                            {{ $buku->penerbit ?? '-' }}
                        </td>

                        <td>
                            {{ $buku->tahun_terbit ?? '-' }}
                        </td>

                        <td>
                            {{ $buku->kategori ? strtoupper($buku->kategori) : '-' }}
                        </td>

                        <td class="text-center">
                            <span
                                class="badge {{ $buku->stok > 0 ? 'bg-success' : 'bg-secondary' }}"
                            >
                                {{ $buku->stok }}
                            </span>
                        </td>

                        {{-- AKSI --}}
                        <td class="text-center">
                            <div
                                class="d-inline-flex align-items-center"
                                style="margin:0; padding:0;"
                            >

                                {{-- DETAIL --}}
                                <a
                                    href="{{ route('admin.buku.show', $buku) }}"
                                    class="btn btn-outline-info"
                                    title="Detail"
                                    style="
                                        width:52px;
                                        height:52px;
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        border-radius:10px 0 0 10px;
                                        margin:0;
                                    "
                                >
                                    <i class="bi bi-eye"></i>
                                </a>

                                {{-- EDIT --}}
                                <a
                                    href="{{ route('admin.buku.edit', $buku) }}"
                                    class="btn btn-outline-warning"
                                    title="Edit"
                                    style="
                                        width:52px;
                                        height:52px;
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        border-radius:0;
                                        margin:0 0 0 -1px;
                                    "
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>

                                {{-- HAPUS --}}
                                <form
                                    method="POST"
                                    action="{{ route('admin.buku.destroy', $buku) }}"
                                    onsubmit="return confirm('Yakin hapus buku ini?');"
                                    style="
                                        display:flex;
                                        margin:0 0 0 -1px;
                                        padding:0;
                                    "
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-outline-danger"
                                        title="Hapus"
                                        style="
                                            width:52px;
                                            height:52px;
                                            display:flex;
                                            align-items:center;
                                            justify-content:center;
                                            border-radius:0 10px 10px 0;
                                            margin:0;
                                        "
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td
                            colspan="9"
                            class="text-center text-muted py-4"
                        >
                            Data buku tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
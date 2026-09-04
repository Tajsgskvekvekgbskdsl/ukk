@extends('layouts.app')

@section('title', 'Detail Buku')
@section('page-title', 'Detail Buku')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                Detail Buku
            </div>

            <div class="card-body">

                <div class="row g-4 mb-3">

                    {{-- COVER --}}
                    <div class="col-md-4 text-center">
                        @if($buku->url_cover)
                            <img
                                src="{{ $buku->url_cover }}"
                                alt="Cover {{ $buku->judul_buku }}"
                                class="img-fluid border rounded"
                                style="max-height:280px; object-fit:cover;"
                            >
                        @else
                            <div
                                class="d-flex align-items-center justify-content-center w-100"
                                style="
                                    max-height:280px;
                                    min-height:200px;
                                    margin:0 auto;
                                    background:#E8FBEE;
                                    color:#23A455;
                                    border-radius:10px;
                                    border:1px solid #BEEBCB;
                                "
                            >
                                <i
                                    class="bi bi-journal-bookmark-fill"
                                    style="font-size:3rem;"
                                ></i>
                            </div>
                        @endif
                    </div>

                    {{-- INFORMASI BUKU --}}
                    <div class="col-md-8">
                        <table class="table table-bordered mb-0">
                            <tbody>

                                <tr>
                                    <th style="width:200px;">
                                        Judul Buku
                                    </th>
                                    <td class="fw-semibold">
                                        {{ $buku->judul_buku }}
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        Pengarang
                                    </th>
                                    <td>
                                        {{ $buku->pengarang }}
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        Penerbit
                                    </th>
                                    <td>
                                        {{ $buku->penerbit ?? '-' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        Tahun Terbit
                                    </th>
                                    <td>
                                        {{ $buku->tahun_terbit ?? '-' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        Kategori
                                    </th>
                                    <td>
                                        {{ $buku->kategori ? strtoupper($buku->kategori) : '-' }}
                                    </td>
                                </tr>

                                {{-- CEK GAMBAR --}}
                                <tr>
                                    <th>
                                        Cover
                                    </th>

                                    <td>
                                        @if($buku->gambar)
                                            <div class="mb-1">
                                                <span class="text-success">
                                                    Terupload
                                                </span>
                                            </div>

                                            <small
                                                class="text-muted"
                                                style="
                                                    display:block;
                                                    word-break:break-all;
                                                    overflow-wrap:anywhere;
                                                "
                                            >
                                                {{ $buku->gambar }}
                                            </small>
                                        @else
                                            <span class="text-muted">
                                                Tidak ada
                                            </span>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        Stok Saat Ini
                                    </th>

                                    <td>
                                        <span
                                            class="badge {{ $buku->stok > 0 ? 'bg-success' : 'bg-secondary' }}"
                                        >
                                            {{ $buku->stok }}
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        Total Pernah Dipinjam
                                    </th>

                                    <td>
                                        {{ $totalDipinjam }} kali
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        Sedang Dipinjam
                                    </th>

                                    <td>
                                        {{ $sedangDipinjam }} eksemplar
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="d-flex gap-2">

                    <a
                        href="{{ route('admin.buku.edit', $buku) }}"
                        class="btn btn-warning"
                    >
                        <i class="bi bi-pencil"></i>
                        Edit
                    </a>

                    <form
                        method="POST"
                        action="{{ route('admin.buku.destroy', $buku) }}"
                        onsubmit="return confirm('Yakin hapus buku ini?');"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            <i class="bi bi-trash"></i>
                            Hapus
                        </button>
                    </form>

                    <a
                        href="{{ route('admin.buku.index') }}"
                        class="btn btn-outline-secondary ms-auto"
                    >
                        Kembali
                    </a>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection
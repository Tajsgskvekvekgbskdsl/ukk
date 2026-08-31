<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;

/**
 * ADMIN - HISTORY seluruh transaksi.
 */
class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $status = trim((string) $request->input('status'));

        $histories = Transaksi::query()
            ->with(['anggota.user', 'buku'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->whereHas('anggota', function ($a) use ($search) {
                        $a->where('nama', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    })->orWhereHas('buku', function ($b) use ($search) {
                        $b->where('judul_buku', 'like', "%{$search}%");
                    });
                });
            })
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->orderByDesc('tgl_pinjam')
            ->paginate(15)
            ->withQueryString();

        return view('admin.history', compact('histories', 'search', 'status'));
    }
}

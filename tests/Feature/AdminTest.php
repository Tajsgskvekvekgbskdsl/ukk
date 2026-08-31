<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TEST ADMIN (bagian 1): login, dashboard, CRUD Buku, Search.
 */
class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    // ---------- LOGIN & DASHBOARD ----------
    public function test_admin_login_redirects_to_dashboard(): void
    {
        $this->post('/login', [
            'login' => $this->admin->username,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_dashboard_loads(): void
    {
        Buku::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Perpustakaan');
    }

    // ---------- CRUD BUKU ----------
    public function test_admin_can_create_buku(): void
    {
        $this->actingAs($this->admin)->post(route('admin.buku.store'), [
            'judul_buku' => 'Buku Uji Coba',
            'pengarang' => 'Penulis Uji',
            'penerbit' => 'Penerbit Uji',
            'tahun_terbit' => 2024,
            'kategori' => 'Teknologi',
            'stok' => 7,
        ])->assertRedirect(route('admin.buku.index'));

        $this->assertDatabaseHas('buku', ['judul_buku' => 'Buku Uji Coba', 'stok' => 7]);
    }

    public function test_admin_buku_stok_cannot_be_negative(): void
    {
        $this->actingAs($this->admin)->post(route('admin.buku.store'), [
            'judul_buku' => 'Stok Negatif',
            'pengarang' => 'X',
            'stok' => -3,
        ]);

        $this->assertDatabaseMissing('buku', ['judul_buku' => 'Stok Negatif']);
    }

    public function test_admin_can_search_buku(): void
    {
        Buku::factory()->create(['judul_buku' => 'Laravel Panduan Lengkap']);
        Buku::factory()->create(['judul_buku' => 'Belajar PHP Dasar']);

        $this->actingAs($this->admin)
            ->get(route('admin.buku.index', ['search' => 'Laravel']))
            ->assertOk()
            ->assertSee('Laravel Panduan Lengkap')
            ->assertDontSee('Belajar PHP Dasar');
    }

    public function test_admin_can_update_and_delete_buku(): void
    {
        $buku = Buku::factory()->create(['judul_buku' => 'Judul Lama', 'stok' => 2]);

        $this->actingAs($this->admin)->put(route('admin.buku.update', $buku), [
            'judul_buku' => 'Judul Baru',
            'pengarang' => $buku->pengarang,
            'penerbit' => $buku->penerbit,
            'tahun_terbit' => $buku->tahun_terbit,
            'kategori' => $buku->kategori,
            'stok' => 12,
        ])->assertRedirect(route('admin.buku.index'));

        $this->assertDatabaseHas('buku', ['id_buku' => $buku->id_buku, 'judul_buku' => 'Judul Baru', 'stok' => 12]);

        // Hapus buku yang belum punya transaksi -> boleh.
        $this->actingAs($this->admin)->delete(route('admin.buku.destroy', $buku))
            ->assertRedirect(route('admin.buku.index'));
        $this->assertDatabaseMissing('buku', ['id_buku' => $buku->id_buku]);
    }

    public function test_admin_cannot_delete_buku_that_has_transaksi(): void
    {
        $buku = Buku::factory()->create();
        Transaksi::factory()->dikembalikan()->create(['id_buku' => $buku->id_buku]);

        $this->actingAs($this->admin)->delete(route('admin.buku.destroy', $buku))
            ->assertRedirect(route('admin.buku.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('buku', ['id_buku' => $buku->id_buku]);
    }

    // ---------- CRUD ANGGOTA ----------
    public function test_admin_can_create_anggota_with_linked_user(): void
    {
        $this->actingAs($this->admin)->post(route('admin.anggota.store'), [
            'nama' => 'Siswa Uji',
            'username' => 'siswauji',
            'email' => 'siswa@uji.id',
            'password' => 'secret123',
            'nis' => '20240999',
            'kelas' => 'X IPA 5',
            'alamat' => 'Jl. Test No. 1',
        ])->assertRedirect(route('admin.anggota.index'));

        $user = User::where('username', 'siswauji')->first();
        $this->assertNotNull($user, 'Akun user anggota harus dibuat');

        $this->assertDatabaseHas('anggota', [
            'nama' => 'Siswa Uji',
            'nis' => '20240999',
            'kelas' => 'X IPA 5',
            'id_user' => $user->id_user,
        ]);
    }

    public function test_admin_can_search_anggota(): void
    {
        Anggota::factory()->create(['nama' => 'Budi Khusus', 'nis' => '100001']);
        Anggota::factory()->create(['nama' => 'Citra Umum', 'nis' => '100002']);

        $this->actingAs($this->admin)
            ->get(route('admin.anggota.index', ['search' => 'Budi']))
            ->assertOk()
            ->assertSee('Budi Khusus')
            ->assertDontSee('Citra Umum');
    }

    public function test_admin_can_update_anggota_and_account_stays_linked(): void
    {
        $anggota = Anggota::factory()->create();

        $this->actingAs($this->admin)->put(route('admin.anggota.update', $anggota), [
            'nama' => 'Nama Setelah Edit',
            'username' => $anggota->user->username,
            'email' => $anggota->user->email,
            'nis' => $anggota->nis,
            'kelas' => 'XII IPS 9',
            'alamat' => 'Alamat Baru',
        ])->assertRedirect(route('admin.anggota.index'));

        $this->assertDatabaseHas('anggota', [
            'id_anggota' => $anggota->id_anggota,
            'nama' => 'Nama Setelah Edit',
            'kelas' => 'XII IPS 9',
        ]);

        // Akun tetap terhubung (id_user tidak berubah).
        $this->assertSame($anggota->fresh()->id_user, $anggota->user->id_user);
    }

    public function test_admin_can_delete_anggota_without_transaksi(): void
    {
        $anggota = Anggota::factory()->create();

        $this->actingAs($this->admin)->delete(route('admin.anggota.destroy', $anggota))
            ->assertRedirect(route('admin.anggota.index'));

        $this->assertDatabaseMissing('anggota', ['id_anggota' => $anggota->id_anggota]);
        $this->assertDatabaseMissing('users', ['id_user' => $anggota->id_user]);
    }

    // ---------- CRUD TRANSAKSI ----------
    public function test_admin_create_transaksi_decreases_stock(): void
    {
        $anggota = Anggota::factory()->create();
        $buku = Buku::factory()->create(['stok' => 3]);

        $this->actingAs($this->admin)->post(route('admin.transaksi.store'), [
            'id_anggota' => $anggota->id_anggota,
            'id_buku' => $buku->id_buku,
            'tgl_pinjam' => now()->toDateString(),
        ])->assertRedirect(route('admin.transaksi.index'));

        $this->assertDatabaseHas('transaksi', [
            'id_anggota' => $anggota->id_anggota,
            'id_buku' => $buku->id_buku,
            'status' => 'dipinjam',
        ]);

        $this->assertSame(2, $buku->fresh()->stok, 'Stok harus berkurang dari 3 menjadi 2');
    }

    public function test_admin_store_blocked_when_stock_zero(): void
    {
        $anggota = Anggota::factory()->create();
        $buku = Buku::factory()->create(['stok' => 0]);

        $this->actingAs($this->admin)
            ->from(route('admin.transaksi.create'))
            ->post(route('admin.transaksi.store'), [
                'id_anggota' => $anggota->id_anggota,
                'id_buku' => $buku->id_buku,
                'tgl_pinjam' => now()->toDateString(),
            ]);

        $this->assertDatabaseCount('transaksi', 0);
        $this->assertSame(0, $buku->fresh()->stok);
    }

    public function test_admin_edit_status_to_dikembalikan_restores_stock_once(): void
    {
        $anggota = Anggota::factory()->create();
        $buku = Buku::factory()->create(['stok' => 10]);

        $trx = Transaksi::factory()->create([
            'id_anggota' => $anggota->id_anggota,
            'id_buku' => $buku->id_buku,
        ]);
        // Simulasikan stok sudah dikurangi saat pinjam: 10 -> 9
        $buku->decrement('stok');

        $this->actingAs($this->admin)->put(route('admin.transaksi.update', $trx), [
            'id_anggota' => $anggota->id_anggota,
            'id_buku' => $buku->id_buku,
            'tgl_pinjam' => $trx->tgl_pinjam->format('Y-m-d'),
            'tgl_kembali' => now()->toDateString(),
            'status' => 'dikembalikan',
        ])->assertRedirect(route('admin.transaksi.index'));

        $this->assertDatabaseHas('transaksi', [
            'id_transaksi' => $trx->id_transaksi,
            'status' => 'dikembalikan',
        ]);

        $this->assertSame(10, $buku->fresh()->stok, 'Stok harus pulih tepat sekali menjadi 10');
    }

    public function test_admin_edit_already_returned_does_not_inflate_stock(): void
    {
        $anggota = Anggota::factory()->create();
        $buku = Buku::factory()->create(['stok' => 9]);

        $trx = Transaksi::factory()->dikembalikan()->create([
            'id_anggota' => $anggota->id_anggota,
            'id_buku' => $buku->id_buku,
        ]);

        $this->actingAs($this->admin)->put(route('admin.transaksi.update', $trx), [
            'id_anggota' => $anggota->id_anggota,
            'id_buku' => $buku->id_buku,
            'tgl_pinjam' => $trx->tgl_pinjam->format('Y-m-d'),
            'tgl_kembali' => $trx->tgl_kembali->format('Y-m-d'),
            'status' => 'dikembalikan',
        ])->assertRedirect(route('admin.transaksi.index'));

        $this->assertSame(9, $buku->fresh()->stok, 'Edit transaksi lama TIDAK boleh menambah stok palsu');
    }

    public function test_admin_destroy_active_transaksi_restores_stock(): void
    {
        $anggota = Anggota::factory()->create();
        $buku = Buku::factory()->create(['stok' => 4]);

        $trx = Transaksi::factory()->create([
            'id_anggota' => $anggota->id_anggota,
            'id_buku' => $buku->id_buku,
        ]);
        $buku->decrement('stok'); // 4 -> 3

        $this->actingAs($this->admin)->delete(route('admin.transaksi.destroy', $trx))
            ->assertRedirect(route('admin.transaksi.index'));

        $this->assertDatabaseMissing('transaksi', ['id_transaksi' => $trx->id_transaksi]);
        $this->assertSame(4, $buku->fresh()->stok, 'Hapus transaksi aktif harus memulihkan stok');
    }

    public function test_admin_transaksi_index_search_works(): void
    {
        $a1 = Anggota::factory()->create(['nama' => 'Peminjam Spesial']);
        $a2 = Anggota::factory()->create(['nama' => 'Peminjam Biasa']);

        Transaksi::factory()->create(['id_anggota' => $a1->id_anggota]);
        Transaksi::factory()->create(['id_anggota' => $a2->id_anggota]);

        $this->actingAs($this->admin)
            ->get(route('admin.transaksi.index', ['search' => 'Spesial']))
            ->assertOk()
            ->assertSee('Peminjam Spesial')
            ->assertDontSee('Peminjam Biasa');
    }

    // ---------- HISTORY & REPORT ----------
    public function test_admin_history_shows_all_transactions(): void
    {
        $a1 = Anggota::factory()->create();
        $a2 = Anggota::factory()->create();

        Transaksi::factory()->create(['id_anggota' => $a1->id_anggota]);
        Transaksi::factory()->create(['id_anggota' => $a2->id_anggota]);

        $this->actingAs($this->admin)
            ->get(route('admin.history'))
            ->assertOk()
            ->assertSee($a1->nama)
            ->assertSee($a2->nama);
    }

    public function test_report_pages_load(): void
    {
        $anggota = Anggota::factory()->create();
        $buku = Buku::factory()->create();

        Transaksi::factory()->create([
            'id_anggota' => $anggota->id_anggota,
            'id_buku' => $buku->id_buku,
        ]);
        Transaksi::factory()->dikembalikan()->create([
            'id_anggota' => $anggota->id_anggota,
            'id_buku' => $buku->id_buku,
        ]);

        $this->actingAs($this->admin)->get(route('admin.report.dashboard'))->assertOk()
            ->assertSee('Report Ringkasan Perpustakaan');
        $this->actingAs($this->admin)->get(route('admin.report.buku'))->assertOk()
            ->assertSee($buku->judul_buku);
        $this->actingAs($this->admin)->get(route('admin.report.anggota'))->assertOk()
            ->assertSee($anggota->nama);
        $this->actingAs($this->admin)->get(route('admin.report.peminjaman'))->assertOk()
            ->assertSee($anggota->nama);
        $this->actingAs($this->admin)->get(route('admin.report.pengembalian'))->assertOk()
            ->assertSee($anggota->nama);
    }
}

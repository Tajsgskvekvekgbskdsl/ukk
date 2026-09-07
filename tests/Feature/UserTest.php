<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TEST USER: register, login, website publik, katalog, peminjaman,
 * pengembalian, history milik sendiri, profile, security.
 */
class UserTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['username' => 'siswa1']);
        Anggota::factory()->create([
            'id_user' => $this->user->id_user,
            'nis' => '20240001',
        ]);
    }

    // ================= REGISTER =================

    public function test_register_creates_linked_users_and_anggota(): void
    {
        $this->post('/register', [
            'nama_lengkap' => 'Siswa Baru',
            'username' => 'siswabaru',
            'email' => 'baru@siswa.id',
            'password' => 'password',
            'password_confirmation' => 'password',
            'nis' => '20240777',
            'kelas' => 'X IPS 2',
            'alamat' => 'Jl. Baru No. 2',
        ])->assertRedirect(route('katalog'));

        // 1. Record users dibuat dengan role user & password ter-hash.
        $newUser = User::where('username', 'siswabaru')->first();
        $this->assertNotNull($newUser);
        $this->assertSame('user', $newUser->role);
        $this->assertNotSame('password', (string) $newUser->getRawOriginal('password'));

        // 2. Record anggota dibuat dan terhubung via id_user.
        $this->assertDatabaseHas('anggota', [
            'nama' => 'Siswa Baru',
            'nis' => '20240777',
            'kelas' => 'X IPS 2',
            'id_user' => $newUser->id_user,
        ]);

        // 3. Admin dapat melihat anggota tersebut di Data Anggota.
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)
            ->get(route('admin.anggota.index'))
            ->assertOk()
            ->assertSee('Siswa Baru');
    }

    public function test_register_cannot_pick_admin_role(): void
    {
        $this->post('/register', [
            'nama_lengkap' => 'Penyusup',
            'username' => 'penyusup',
            'email' => 'penyusup@x.id',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin', // dicoba menyelundupkan role
        ])->assertRedirect(route('katalog'));

        $u = User::where('username', 'penyusup')->first();
        $this->assertNotNull($u);
        $this->assertSame('user', $u->role, 'Register tidak boleh menghasilkan admin');
    }

    public function test_user_can_login_with_username(): void
    {
        $this->post('/login', [
            'login' => 'siswa1',
            'password' => 'password',
        ])->assertRedirect(route('katalog'));
    }

    // ================= WEBSITE PUBLIK =================

    public function test_public_homepage_and_katalog_accessible_without_login(): void
    {
        Buku::factory()->count(4)->create();

                $this->get(route('beranda'))->assertOk()->assertSee('Perpustakaan');
        $this->get(route('katalog'))->assertOk();
    }

    public function test_katalog_search_category_and_availability_filter(): void
    {
        Buku::factory()->create(['judul_buku' => 'Kamus Bahasa Jawa', 'kategori' => 'Kamus', 'stok' => 5]);
        Buku::factory()->create(['judul_buku' => 'Ensiklopedia Sains', 'kategori' => 'Sains', 'stok' => 0]);
        Buku::factory()->create(['judul_buku' => 'Kamus Astronomi', 'kategori' => 'Kamus', 'stok' => 2]);

        // search "Kamus"
        $this->actingAs($this->user)
            ->get(route('katalog', ['search' => 'Kamus']))
            ->assertOk()
            ->assertSee('Kamus Bahasa Jawa')
            ->assertSee('Kamus Astronomi')
            ->assertDontSee('Ensiklopedia Sains');

        // filter kategori Kamus + hanya tersedia
        $this->actingAs($this->user)
            ->get(route('katalog', ['kategori' => 'Kamus', 'tersedia' => 1]))
            ->assertOk()
            ->assertSee('Kamus Bahasa Jawa')
            ->assertDontSee('Ensiklopedia Sains');

        // filter tersedia saja: buku stok 0 tidak muncul
        $this->actingAs($this->user)
            ->get(route('katalog', ['tersedia' => 1]))
            ->assertOk()
            ->assertSee('Kamus Bahasa Jawa')
            ->assertDontSee('Ensiklopedia Sains');
    }

    public function test_book_detail_page_loads(): void
    {
        $buku = Buku::factory()->create(['judul_buku' => 'Buku Detail Uji']);

        $this->actingAs($this->user)
            ->get(route('buku.detail', $buku))
            ->assertOk()
            ->assertSee('Buku Detail Uji');
    }

    // ================= PEMINJAMAN =================

    public function test_user_borrows_book_creates_transaksi_and_decreases_stock(): void
    {
        $buku = Buku::factory()->create(['stok' => 4]);
        $anggota = Anggota::where('id_user', $this->user->id_user)->first();

        $this->actingAs($this->user)
            ->post(route('user.peminjaman.store'), [
                'id_buku' => $buku->id_buku,
                // coba selundupkan id_anggota milik orang lain:
                'id_anggota' => Anggota::factory()->create()->id_anggota,
            ])
            ->assertRedirect(route('user.peminjaman.index'));

        // Transaksi WAJIB memakai anggota milik user yang login.
        $trx = Transaksi::firstOrFail();
        $this->assertSame($anggota->id_anggota, (int) $trx->id_anggota,
            'Transaksi wajib terhubung ke anggota milik user yang sedang login');
        $this->assertSame($buku->id_buku, (int) $trx->id_buku);
        $this->assertSame('dipinjam', $trx->status);
        $this->assertNull($trx->tgl_kembali);

        // Stok berkurang 4 -> 3.
        $this->assertSame(3, $buku->fresh()->stok);
    }

    public function test_cannot_borrow_when_stock_zero(): void
    {
        $buku = Buku::factory()->create(['stok' => 0]);

        $this->actingAs($this->user)
            ->post(route('user.peminjaman.store'), ['id_buku' => $buku->id_buku])
            ->assertSessionHasErrors();

        $this->assertDatabaseCount('transaksi', 0);
        $this->assertSame(0, $buku->fresh()->stok, 'Stok tidak boleh menjadi negatif');
    }

    public function test_cannot_borrow_same_title_twice_concurrently(): void
    {
        $buku = Buku::factory()->create(['stok' => 5]);

        $this->actingAs($this->user)
            ->post(route('user.peminjaman.store'), ['id_buku' => $buku->id_buku]);

        $this->actingAs($this->user)
            ->from(route('katalog'))
            ->post(route('user.peminjaman.store'), ['id_buku' => $buku->id_buku])
            ->assertSessionHasErrors();

        // Tetap satu transaksi & stok hanya turun sekali.
        $this->assertDatabaseCount('transaksi', 1);
        $this->assertSame(4, $buku->fresh()->stok);
    }

    // ================= PENGEMBALIAN =================

    public function test_user_returns_book_status_changes_and_stock_increases(): void
    {
        $buku = Buku::factory()->create(['stok' => 5]);
        $anggota = Anggota::where('id_user', $this->user->id_user)->first();

        $trx = Transaksi::factory()->create([
            'id_anggota' => $anggota->id_anggota,
            'id_buku' => $buku->id_buku,
        ]);
        // Simulasikan stok berkurang saat pinjam: 5 -> 4
        $buku->decrement('stok');

        $this->actingAs($this->user)
            ->post(route('user.pengembalian.store', $trx))
            ->assertRedirect(route('user.pengembalian.index'));

        // Status & tanggal kembali berubah.
        $this->assertSame('dikembalikan', $trx->fresh()->status);
        $this->assertNotNull($trx->fresh()->tgl_kembali);

        // Stok bertambah 4 -> 5.
        $this->assertSame(5, $buku->fresh()->stok);
    }

    public function test_book_cannot_be_returned_twice(): void
    {
        $buku = Buku::factory()->create(['stok' => 4]);
        $anggota = Anggota::where('id_user', $this->user->id_user)->first();

        $trx = Transaksi::factory()->dikembalikan()->create([
            'id_anggota' => $anggota->id_anggota,
            'id_buku' => $buku->id_buku,
        ]);

        $this->actingAs($this->user)
            ->post(route('user.pengembalian.store', $trx))
            ->assertRedirect(route('user.pengembalian.index'))
            ->assertSessionHas('error');

        $this->assertSame(4, $buku->fresh()->stok, 'Stok tidak boleh bertambah dua kali');
    }

    public function test_user_cannot_return_other_users_transaction(): void
    {
        $buku = Buku::factory()->create(['stok' => 3]);
        $otherAnggota = Anggota::factory()->create();

        $trx = Transaksi::factory()->create([
            'id_anggota' => $otherAnggota->id_anggota,
            'id_buku' => $buku->id_buku,
        ]);
        $buku->decrement('stok'); // 3 -> 2

        $this->actingAs($this->user)
            ->post(route('user.pengembalian.store', $trx))
            ->assertForbidden();

        $this->assertSame('dipinjam', $trx->fresh()->status);
        $this->assertSame(2, $buku->fresh()->stok, 'Stok buku orang lain tidak boleh terpengaruh');
    }

    // ================= HISTORY =================

    public function test_history_shows_only_own_transactions(): void
    {
        $bukuA = Buku::factory()->create();
        $bukuB = Buku::factory()->create();
        $anggota = Anggota::where('id_user', $this->user->id_user)->first();

        Transaksi::factory()->dikembalikan()->create([
            'id_anggota' => $anggota->id_anggota,
            'id_buku' => $bukuA->id_buku,
        ]);

        $otherAnggota = Anggota::factory()->create();
        Transaksi::factory()->create([
            'id_anggota' => $otherAnggota->id_anggota,
            'id_buku' => $bukuB->id_buku,
        ]);

        $this->actingAs($this->user)
            ->get(route('user.riwayat.index'))
            ->assertOk()
            ->assertSee($bukuA->judul_buku)
            ->assertDontSee($bukuB->judul_buku);
    }

    // ================= PROFILE =================

    public function test_user_can_view_and_update_profile(): void
    {
        $this->actingAs($this->user)->get(route('user.profile.index'))->assertOk();

        $this->actingAs($this->user)
            ->put(route('user.profile.update'), [
                'nama_lengkap' => 'Nama Baru Profil',
                'nis' => '20240001',
                'kelas' => 'XI IPA 7',
                'alamat' => 'Alamat Baru Profil',
            ])
            ->assertRedirect(route('user.profile.index'));

        // users.nama_lengkap dan anggota.nama keduanya diperbarui.
        $this->assertDatabaseHas('users', [
            'id_user' => $this->user->id_user,
            'nama_lengkap' => 'Nama Baru Profil',
        ]);
        $anggota = Anggota::where('id_user', $this->user->id_user)->first();
        $this->assertSame('XI IPA 7', $anggota->kelas);

        // Ganti password.
        $this->actingAs($this->user)
            ->put(route('user.profile.password.update'), [
                'current_password' => 'password',
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ])
            ->assertRedirect(route('user.profile.index'));

        $this->assertTrue(password_verify('newpassword', $this->user->fresh()->password));
    }

    // ================= SECURITY =================

    public function test_regular_user_cannot_access_admin_pages(): void
    {
        foreach ([
            route('admin.dashboard'),
            route('admin.buku.index'),
            route('admin.buku.create'),
            route('admin.anggota.index'),
            route('admin.transaksi.index'),
            route('admin.history'),
            route('admin.report.dashboard'),
        ] as $url) {
            $this->actingAs($this->user)->get($url)->assertForbidden();
        }
    }

    public function test_admin_is_redirected_from_member_only_pages(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('user.peminjaman.index'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_guest_redirected_to_login_for_member_pages(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('user.peminjaman.index'))->assertRedirect(route('login'));
        $this->post(route('user.peminjaman.store'))->assertRedirect(route('login'));
    }
}

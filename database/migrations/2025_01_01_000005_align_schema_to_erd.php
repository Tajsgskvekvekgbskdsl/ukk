<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menyelaraskan struktur database dengan ERD:
 * users(id_user) 1--1 anggota(id_anggota,id_user) 1--N transaksi(id_transaksi,id_anggota,id_buku) N--1 buku(id_buku)
 *
 * Non-destruktif terhadap RECORD data dan AMAN DIJALANKAN ULANG:
 * setiap langkah dicek keberadaannya terlebih dahulu.
 */
return new class extends Migration
{
    /**
     * Jalankan operasi skema sekali saja (abaikan bila sudah diterapkan).
     * Diperlukan karena DDL MySQL tidak transaksional dan nama index/FK
     * berbeda antar driver (MySQL vs SQLite).
     */
    private function once(callable $fn): void
    {
        try {
            $fn();
        } catch (QueryException $e) {
            $msg = strtolower($e->getMessage());

            $sudahAda = str_contains($msg, "can't drop")
                || str_contains($msg, 'duplicate key name')
                || str_contains($msg, 'already exists')
                || str_contains($msg, 'duplicate column')
                || str_contains($msg, 'check that column')
                || str_contains($msg, 'no such index')
                || str_contains($msg, 'no such column');

            if (! $sudahAda) {
                throw $e;
            }
        }
    }

    public function up(): void
    {
        $isMysql = DB::getDriverName() === 'mysql';

        // ================= USERS =================
        $this->once(function () {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'id')) {
                    $table->renameColumn('id', 'id_user');
                }
                if (Schema::hasColumn('users', 'name')) {
                    $table->renameColumn('name', 'nama_lengkap');
                }
            });
        });

        if (! Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->nullable();
                $table->string('status')->default('aktif');
            });

            $rows = DB::table('users')->select('id_user', 'email')->get();
            $used = [];
            foreach ($rows as $row) {
                $base = strtolower(strtok((string) $row->email, '@')) ?: 'user'.$row->id_user;
                $username = $base;
                $i = 1;
                while (isset($used[$username])) {
                    $username = $base.$i++;
                }
                $used[$username] = true;

                DB::table('users')->where('id_user', $row->id_user)->update(['username' => $username]);
            }
        }

        if (! Schema::hasIndex('users', 'users_username_unique')) {
            $this->once(function () {
                Schema::table('users', function (Blueprint $table) {
                    $table->unique('username');
                });
            });
        }

        // ================= ANGGOTA =================
        if (Schema::hasTable('anggotas') && ! Schema::hasTable('anggota')) {
            if ($isMysql) {
                $this->once(fn () => Schema::table('anggotas', fn (Blueprint $t) => $t->dropForeign(['user_id'])));
            }
            $this->once(fn () => Schema::table('anggotas', fn (Blueprint $t) => $t->dropUnique('anggotas_kode_anggota_unique')));
            Schema::rename('anggotas', 'anggota');
        }

        if (Schema::hasTable('anggota')) {
            $this->once(function () {
                Schema::table('anggota', function (Blueprint $table) {
                    if (Schema::hasColumn('anggota', 'id')) {
                        $table->renameColumn('id', 'id_anggota');
                    }
                    if (Schema::hasColumn('anggota', 'user_id')) {
                        $table->renameColumn('user_id', 'id_user');
                    }
                });
            });

            $dropLamaAnggota = array_values(array_filter(
                ['kode_anggota', 'email', 'telepon'],
                fn ($c) => Schema::hasColumn('anggota', $c)
            ));
            if ($dropLamaAnggota !== []) {
                Schema::table('anggota', function (Blueprint $table) use ($dropLamaAnggota) {
                    $table->dropColumn($dropLamaAnggota);
                });
            }

            Schema::table('anggota', function (Blueprint $table) {
                if (! Schema::hasColumn('anggota', 'nis')) {
                    $table->string('nis')->nullable();
                }
                if (! Schema::hasColumn('anggota', 'kelas')) {
                    $table->string('kelas')->nullable();
                }
            });
        }

        // ================= BUKU =================
        if (Schema::hasTable('bukus') && ! Schema::hasTable('buku')) {
            $this->once(fn () => Schema::table('bukus', fn (Blueprint $t) => $t->dropUnique('bukus_kode_buku_unique')));
            Schema::rename('bukus', 'buku');
        }

        if (Schema::hasTable('buku')) {
            $this->once(function () {
                Schema::table('buku', function (Blueprint $table) {
                    if (Schema::hasColumn('buku', 'id')) {
                        $table->renameColumn('id', 'id_buku');
                    }
                    if (Schema::hasColumn('buku', 'judul')) {
                        $table->renameColumn('judul', 'judul_buku');
                    }
                });
            });

            if (Schema::hasColumn('buku', 'kode_buku')) {
                Schema::table('buku', function (Blueprint $table) {
                    $table->dropColumn('kode_buku');
                });
            }
        }

        $this->migrateTransaksi($isMysql);
        $this->rebuildForeignKeys($isMysql);
    }

    private function migrateTransaksi(bool $isMysql): void
    {
        /*
         * SQLite (jalur testing): kolom lama seperti user_id adalah bagian dari
         * definisi FOREIGN KEY sehingga TIDAK BISA di-drop per kolom.
         * Solusinya: bangun ulang tabel langsung dengan skema ERD.
         */
        if (! $isMysql && Schema::hasTable('transaksis') && ! Schema::hasTable('transaksi')
            && Schema::hasColumn('anggota', 'id_anggota')) {
            Schema::create('transaksi_tmp', function (Blueprint $t) {
                $t->id('id_transaksi');
                $t->unsignedBigInteger('id_anggota')->nullable();
                $t->unsignedBigInteger('id_buku');
                $t->date('tgl_pinjam');
                $t->date('tgl_kembali')->nullable();
                $t->string('status')->default('dipinjam');
                $t->timestamps();

                $t->foreign('id_anggota')->references('id_anggota')->on('anggota')->cascadeOnDelete();
                $t->foreign('id_buku')->references('id_buku')->on('buku')->cascadeOnDelete();
            });

            DB::statement(
                'INSERT INTO transaksi_tmp
                    (id_transaksi, id_anggota, id_buku, tgl_pinjam, tgl_kembali, status,
                     created_at, updated_at)
                 SELECT id, anggota_id, buku_id, tgl_pinjam, tgl_kembali_aktual, status,
                        created_at, updated_at
                 FROM transaksis
                 WHERE anggota_id IS NOT NULL'
            );

            Schema::drop('transaksis');
            Schema::rename('transaksi_tmp', 'transaksi');

            return;
        }

        if (Schema::hasTable('transaksis') && ! Schema::hasTable('transaksi')) {
            if ($isMysql) {
                foreach (['user_id', 'buku_id', 'anggota_id'] as $fk) {
                    $this->once(fn () => Schema::table('transaksis', fn (Blueprint $t) => $t->dropForeign([$fk])));
                }
            }

            $this->once(fn () => Schema::table('transaksis', fn (Blueprint $t) => $t->dropUnique('transaksis_kode_transaksi_unique')));
            $this->once(fn () => Schema::table('transaksis', fn (Blueprint $t) => $t->dropIndex('transaksis_user_id_index')));

            Schema::rename('transaksis', 'transaksi');
        }

        if (! Schema::hasTable('transaksi')) {
            return;
        }

        // Rename kolom ke penamaan ERD.
        Schema::table('transaksi', function (Blueprint $table) {
            if (Schema::hasColumn('transaksi', 'id')) {
                $table->renameColumn('id', 'id_transaksi');
            }
            if (Schema::hasColumn('transaksi', 'anggota_id')) {
                $table->renameColumn('anggota_id', 'id_anggota');
            }
            if (Schema::hasColumn('transaksi', 'buku_id')) {
                $table->renameColumn('buku_id', 'id_buku');
            }
            if (Schema::hasColumn('transaksi', 'tgl_kembali_aktual')) {
                $table->renameColumn('tgl_kembali_aktual', 'tgl_kembali');
            }
        });

        // Buang kolom operasional lama yang tidak ada di ERD.
        $dropLama = array_values(array_filter(
            ['kode_transaksi', 'user_id', 'jumlah', 'tgl_kembali_rencana'],
            fn ($c) => Schema::hasColumn('transaksi', $c)
        ));
        if ($dropLama !== []) {
            Schema::table('transaksi', function (Blueprint $table) use ($dropLama) {
                $table->dropColumn($dropLama);
            });
        }

        // Setiap transaksi wajib terhubung ke anggota (baris yatim dihapus).
        if (Schema::hasColumn('transaksi', 'id_anggota')) {
            DB::table('transaksi')->whereNull('id_anggota')->delete();
        }
    }

    private function rebuildForeignKeys(bool $isMysql): void
    {
        if (! $isMysql) {
            return; // sqlite: FK inline pada CREATE TABLE ikut ter-update otomatis saat rename.
        }

        if (Schema::hasTable('anggota') && $this->foreignKeyMissing('anggota', 'id_user')) {
            $this->once(function () {
                Schema::table('anggota', function (Blueprint $table) {
                    $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
                });
            });
        }

        if (Schema::hasTable('transaksi')) {
            if ($this->foreignKeyMissing('transaksi', 'id_anggota')) {
                $this->once(function () {
                    Schema::table('transaksi', function (Blueprint $table) {
                        $table->foreign('id_anggota')->references('id_anggota')->on('anggota')->cascadeOnDelete();
                    });
                });
            }
            if ($this->foreignKeyMissing('transaksi', 'id_buku')) {
                $this->once(function () {
                    Schema::table('transaksi', function (Blueprint $table) {
                        $table->foreign('id_buku')->references('id_buku')->on('buku')->cascadeOnDelete();
                    });
                });
            }
        }
    }

    private function foreignKeyMissing(string $table, string $column): bool
    {
        $rows = DB::select(
            'SELECT COUNT(*) AS n FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$table, $column]
        );

        return ((int) $rows[0]->n) === 0;
    }

    public function down(): void
    {
        // Rollback ringkas ke penamaan semula (data tidak dijamin identik).
        $isMysql = DB::getDriverName() === 'mysql';

        if ($isMysql) {
            Schema::table('anggota', function (Blueprint $table) {
                $table->dropForeign(['id_user']);
            });
            Schema::table('transaksi', function (Blueprint $table) {
                $table->dropForeign(['id_anggota']);
                $table->dropForeign(['id_buku']);
            });
        }

        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('kode_transaksi')->nullable();
            $table->integer('jumlah')->default(1);
            $table->date('tgl_kembali_rencana')->nullable();
            $table->renameColumn('tgl_kembali', 'tgl_kembali_aktual');
            $table->renameColumn('id_anggota', 'anggota_id');
            $table->renameColumn('id_buku', 'buku_id');
            $table->renameColumn('id_transaksi', 'id');
        });
        Schema::rename('transaksi', 'transaksis');

        Schema::table('buku', function (Blueprint $table) {
            $table->string('kode_buku')->nullable();
            $table->renameColumn('judul_buku', 'judul');
            $table->renameColumn('id_buku', 'id');
        });
        Schema::rename('buku', 'bukus');

        Schema::table('anggota', function (Blueprint $table) {
            $table->string('kode_anggota')->nullable();
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->dropColumn(['nis', 'kelas']);
            $table->renameColumn('id_user', 'user_id');
            $table->renameColumn('id_anggota', 'id');
        });
        Schema::rename('anggota', 'anggotas');

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn(['username', 'status']);
            $table->renameColumn('nama_lengkap', 'name');
            $table->renameColumn('id_user', 'id');
        });
    }
};

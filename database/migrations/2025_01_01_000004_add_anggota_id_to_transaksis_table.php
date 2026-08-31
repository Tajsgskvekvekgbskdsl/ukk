<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // Tautkan transaksi langsung ke anggota (users -> anggota -> transaksi -> buku)
            $table->foreignId('anggota_id')
                ->nullable()
                ->after('user_id')
                ->constrained('anggotas')
                ->nullOnDelete();
        });

        // Backfill: hubungkan transaksi lama dengan anggota milik user yang sama.
        // Memakai Query Builder agar kompatibel di semua driver database.
        $transaksis = DB::table('transaksis')
            ->whereNull('anggota_id')
            ->select('id', 'user_id')
            ->get();

        foreach ($transaksis as $trx) {
            $anggotaId = DB::table('anggotas')
                ->where('user_id', $trx->user_id)
                ->value('id');

            if ($anggotaId) {
                DB::table('transaksis')->where('id', $trx->id)->update([
                    'anggota_id' => $anggotaId,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropConstrainedForeignId('anggota_id');
        });
    }
};

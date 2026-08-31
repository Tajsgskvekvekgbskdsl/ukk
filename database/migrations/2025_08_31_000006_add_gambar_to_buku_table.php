<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            // Kolom path relatif file cover (misal: buku/abc123.jpg)
            // nullable agar buku yang sudah ada tidak terpengaruh.
            if (! Schema::hasColumn('buku', 'gambar')) {
                $table->string('gambar', 255)->nullable()->after('stok');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            if (Schema::hasColumn('buku', 'gambar')) {
                $table->dropColumn('gambar');
            }
        });
    }
};
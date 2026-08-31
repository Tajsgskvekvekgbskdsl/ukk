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
            if (! Schema::hasColumn('buku', 'isbn')) {
                $table->string('isbn', 255)->nullable()->after('gambar');
            }
            if (! Schema::hasColumn('buku', 'sinopsis')) {
                $table->text('sinopsis')->nullable()->after('isbn');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->dropColumn(['isbn', 'sinopsis']);
        });
    }
};
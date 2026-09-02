<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            // Hapus kolom denda lama
            $table->dropColumn('denda');

            // Tambahkan kolom baru
            $table->integer('denda_terlambat')->default(0)->after('kondisi_kembali');
            $table->integer('denda_kerusakan')->default(0)->after('denda_terlambat');
            $table->integer('total_denda')->default(0)->after('denda_kerusakan');
        });
    }

    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropColumn(['denda_terlambat', 'denda_kerusakan', 'total_denda']);
            $table->integer('denda')->default(0)->after('kondisi_kembali');
        });
    }
};
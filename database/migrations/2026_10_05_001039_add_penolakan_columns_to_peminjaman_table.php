<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->text('alasan_penolakan')->nullable()->after('status');
            $table->foreignId('ditolak_oleh')->nullable()->after('alasan_penolakan')
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('ditolak_pada')->nullable()->after('ditolak_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['ditolak_oleh']);
            $table->dropColumn(['alasan_penolakan', 'ditolak_oleh', 'ditolak_pada']);
        });
    }
};
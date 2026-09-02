<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->enum('status', ['diajukan', 'dipinjam', 'menunggu_verifikasi', 'dikembalikan', 'telat'])
                  ->default('diajukan')
                  ->change();
        });
    }

    public function down()
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->enum('status', ['diajukan', 'dipinjam', 'dikembalikan', 'telat'])
                  ->default('diajukan')
                  ->change();
        });
    }
};
<?php

namespace Database\Seeders;

use App\Models\Pengembalian;
use Illuminate\Database\Seeder;

class PengembalianSeeder extends Seeder
{
    public function run(): void
    {
        $pengembalian = [
            [
                'peminjaman_id' => 1,
                'tgl_kembali' => '2026-06-04',
                'kondisi_kembali' => 'Lengkap dan Berfungsi Baik',
                'denda_terlambat' => 0,
                'denda_kerusakan' => 0,
                'total_denda' => 0,
                'petugas_id' => 2,
            ],
            [
                'peminjaman_id' => 2,
                'tgl_kembali' => '2026-06-05',
                'kondisi_kembali' => 'Lengkap dan Berfungsi Baik',
                'denda_terlambat' => 0,
                'denda_kerusakan' => 0,
                'total_denda' => 0,
                'petugas_id' => 2,
            ],
            [
                'peminjaman_id' => 3,
                'tgl_kembali' => '2026-06-09',
                'kondisi_kembali' => 'Lengkap, Casing Sedikit Tergores',
                'denda_terlambat' => 15000,
                'denda_kerusakan' => 10000,
                'total_denda' => 25000,
                'petugas_id' => 2,
            ],
        ];

        foreach ($pengembalian as $kembali) {
            Pengembalian::create($kembali);
        }
    }
}
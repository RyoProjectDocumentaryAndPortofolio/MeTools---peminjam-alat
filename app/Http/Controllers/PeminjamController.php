<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PeminjamController extends Controller
{
    public function katalog()
    {
        $alats = Alat::with('kategori')->where('stok', '>', 0)->get();
        return view('peminjam.katalog', compact('alats'));
    }

    public function ajukanPeminjaman(Request $request)
    {
        // 1. Validasi dasar
        $request->validate([
            'tgl_kembali_plan' => 'required|date',
            'alat_id'          => 'required|array|min:1',
            'alat_id.*'        => 'exists:alat,id',
            'jumlah'           => 'required|array|min:1',
            'jumlah.*'         => 'integer|min:1',
        ], [
            'tgl_kembali_plan.required' => 'Tanggal kembali wajib diisi.',
            'tgl_kembali_plan.date'     => 'Format tanggal kembali tidak valid.',
            'alat_id.required'          => 'Pilih minimal 1 alat untuk dipinjam.',
            'alat_id.min'               => 'Pilih minimal 1 alat untuk dipinjam.',
            'jumlah.required'           => 'Jumlah alat wajib diisi.',
            'jumlah.*.min'              => 'Jumlah alat minimal 1.',
        ]);

        // 2. Validasi logika: tanggal kembali HARUS setelah tanggal pinjam
        $tglPinjam  = Carbon::now()->startOfDay();
        $tglKembali = Carbon::parse($request->tgl_kembali_plan)->startOfDay();

        if ($tglKembali->lessThanOrEqualTo($tglPinjam)) {
            return back()
                ->withInput()
                ->with('error', 'Tanggal kembali harus setelah tanggal pinjam (minimal besok).');
        }

        DB::beginTransaction();
        try {
            // 3. Buat data peminjaman
            $peminjaman = Peminjaman::create([
                'user_id'          => auth()->id(),
                'tgl_pinjam'       => $tglPinjam->toDateString(),
                'tgl_kembali_plan' => $tglKembali->toDateString(),
                'status'           => 'diajukan',
            ]);

            // 4. Simpan detail alat + cek stok
            foreach ($request->alat_id as $index => $alatId) {
                $jumlah = $request->jumlah[$index] ?? 0;

                $alat = Alat::lockForUpdate()->find($alatId);

                if (!$alat) {
                    throw new Exception("Alat dengan ID {$alatId} tidak ditemukan.");
                }

                if ($alat->stok < $jumlah) {
                    throw new Exception("Stok alat '{$alat->nama_alat}' tidak mencukupi. Sisa stok: {$alat->stok}, diminta: {$jumlah}.");
                }

                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id'       => $alatId,
                    'jumlah'        => $jumlah,
                ]);
            }

            DB::commit();
            return redirect()->route('peminjam.riwayat')
                ->with('success', 'Pengajuan peminjaman berhasil dikirim.');

        } catch (Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Gagal mengajukan peminjaman! ' . $e->getMessage());
        }
    }

    public function riwayatPeminjaman()
    {
        $peminjaman = Peminjaman::with('detailPinjam.alat')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('peminjam.riwayat', compact('peminjaman'));
    }

    public function kembalikan($id)
    {
        $peminjaman = Peminjaman::where('user_id', auth()->id())
            ->where('id', $id)
            ->where('status', 'dipinjam')
            ->firstOrFail();

        $peminjaman->update(['status' => 'menunggu_verifikasi']);

        return redirect()->back()
            ->with('success', 'Pengembalian berhasil diajukan. Menunggu verifikasi petugas.');
    }
}
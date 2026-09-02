<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PetugasController extends Controller
{
    private const DENDA_PER_HARI = 5000;

    // Menampilkan daftar pengajuan peminjaman dari siswa/peminjam
    public function indexPeminjaman()
    {
        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])->latest()->get();
        return view('petugas.peminjaman.index', compact('peminjamans'));
    }

    // Menyetujui peminjaman
    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($id);
            $peminjaman->update(['status' => 'dipinjam']);

            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Proses pengembalian alat (LENGKAP DENGAN DENDA)
   public function prosesPengembalian(Request $request, $peminjamanId)
{
    $request->validate([
        'kondisi_kembali' => 'required|string',
        'denda_kerusakan' => 'nullable|integer|min:0',
    ]);

    try {
        $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($peminjamanId);

        // Cek status
        if ($peminjaman->status !== 'dipinjam') {
            throw new \Exception("Peminjaman ini berstatus '{$peminjaman->status}', bukan 'dipinjam'.");
        }

        // Hitung denda keterlambatan
        $tglKembaliPlan = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
        $tglKembali = \Carbon\Carbon::now()->startOfDay();

        $dendaTerlambat = 0;
        if ($tglKembali->gt($tglKembaliPlan)) {
            $selisihHari = $tglKembali->diffInDays($tglKembaliPlan);
            $dendaTerlambat = $selisihHari * self::DENDA_PER_HARI;
        }

        $dendaKerusakan = $request->denda_kerusakan ?? 0;
        $totalDenda = $dendaTerlambat + $dendaKerusakan;

        
        $statusBaru = $tglKembali->gt($tglKembaliPlan) ? 'telat' : 'dikembalikan';
        $peminjaman->update(['status' => $statusBaru]);

        // Kembalikan stok
        foreach ($peminjaman->detailPinjam as $detail) {
            $alat = Alat::findOrFail($detail->alat_id);
            $alat->stok += $detail->jumlah;
            $alat->save();
        }

        // Simpan pengembalian
        Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'tgl_kembali' => now(),
            'kondisi_kembali' => $request->kondisi_kembali,
            'denda_terlambat' => $dendaTerlambat,
            'denda_kerusakan' => $dendaKerusakan,
            'total_denda' => $totalDenda,
            'petugas_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Pengembalian berhasil dicatat. Total denda: Rp ' . number_format($totalDenda, 0, ',', '.'));
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
    // Pemantauan Pengembalian (menampilkan peminjaman yang sedang dipinjam)
    public function indexPengembalian()
    {
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'dipinjam')
            ->latest()
            ->get();

        return view('petugas.pengembalian.index', compact('peminjaman'));
    }

    // Form Pengembalian (untuk halaman form)
    public function formPengembalian($id)
    {
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])->findOrFail($id);
        return view('petugas.pengembalian.form', compact('peminjaman'));
    }

    // Generate Laporan PDF
    public function generateLaporanPDF()
    {
        // Ambil data peminjaman yang sudah selesai (dikembalikan & telat)
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian'])
            ->whereIn('status', ['dikembalikan', 'telat'])
            ->latest()
            ->get();

        // Hitung total denda
        $totalDenda = $peminjaman->sum(function ($item) {
            return $item->pengembalian->total_denda ?? 0;
        });

        $data = [
            'peminjaman' => $peminjaman,
            'totalDenda' => $totalDenda,
            'totalTransaksi' => $peminjaman->count(),
            'tanggal' => now()->format('d-m-Y H:i:s'),
            'petugas' => auth()->user()->name,
        ];

        $pdf = Pdf::loadView('petugas.laporan.pdf', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan_peminjaman_' . now()->format('Y-m-d') . '.pdf');
    }
    public function verifikasiPengembalian($id)
{
    $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($id);

    // Cek apakah statusnya menunggu verifikasi
    if ($peminjaman->status !== 'menunggu_verifikasi') {
        return redirect()->back()->with('error', 'Peminjaman ini tidak menunggu verifikasi.');
    }

    // Hitung denda keterlambatan
    $tglKembaliPlan = Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
    $tglKembali = Carbon::now()->startOfDay();

    $dendaTerlambat = 0;
    if ($tglKembali->gt($tglKembaliPlan)) {
        $selisihHari = $tglKembali->diffInDays($tglKembaliPlan);
        $dendaTerlambat = $selisihHari * self::DENDA_PER_HARI;
    }

    $statusBaru = $tglKembali->gt($tglKembaliPlan) ? 'telat' : 'dikembalikan';

    DB::beginTransaction();
    try {
        // Update status
        $peminjaman->update(['status' => $statusBaru]);

        // Kembalikan stok
        foreach ($peminjaman->detailPinjam as $detail) {
            $alat = Alat::findOrFail($detail->alat_id);
            $alat->stok += $detail->jumlah;
            $alat->save();
        }

        // Simpan data pengembalian
        Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'tgl_kembali' => now(),
            'kondisi_kembali' => 'Baik', // default, petugas bisa update nanti
            'denda_terlambat' => $dendaTerlambat,
            'denda_kerusakan' => 0,
            'total_denda' => $dendaTerlambat,
            'petugas_id' => auth()->id(),
        ]);

        DB::commit();
        return redirect()->back()->with('success', 'Pengembalian berhasil diverifikasi. Status: ' . $statusBaru);
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
}
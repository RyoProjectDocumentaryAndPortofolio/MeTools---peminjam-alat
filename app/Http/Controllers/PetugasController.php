<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PetugasController extends Controller
{
    private const DENDA_PER_HARI = 5000;

    // ============================================
    // PEMINJAMAN
    // ============================================

    /**
     * Menampilkan daftar pengajuan peminjaman.
     * Support filter (dropdown) & sort (dropdown).
     */
    public function indexPeminjaman(Request $request)
    {
        $filter = $request->input('filter', 'semua');
        $sort   = $request->input('sort', 'terbaru');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat', 'ditolakOleh'])
            ->filter($filter)
            ->sort($sort)
            ->get();

        return view('petugas.peminjaman.index', compact('peminjamans', 'filter', 'sort'));
    }

    /**
     * Menyetujui peminjaman.
     * Opsi B: Siapa cepat dia dapat — validasi stok real-time.
     */
    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')
                ->lockForUpdate()
                ->findOrFail($id);

            if ($peminjaman->status !== 'diajukan') {
                DB::rollback();
                return back()->with('error', "Peminjaman ini berstatus '{$peminjaman->status}', bukan 'diajukan'.");
            }

            if ($peminjaman->detailPinjam->isEmpty()) {
                DB::rollback();
                return back()->with('error', 'Peminjaman ini tidak memiliki detail alat.');
            }

            $errors = [];
            $alatTerkunci = [];

            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::lockForUpdate()->find($detail->alat_id);

                if (!$alat) {
                    $errors[] = "Alat dengan ID {$detail->alat_id} tidak ditemukan.";
                    continue;
                }

                if ($alat->stok < $detail->jumlah) {
                    $errors[] = "Stok alat '{$alat->nama_alat}' tidak mencukupi. Sisa: {$alat->stok}, dibutuhkan: {$detail->jumlah}.";
                }

                $alatTerkunci[$alat->id] = $alat;
            }

            if (!empty($errors)) {
                DB::rollback();
                return back()->with('error', 'Gagal menyetujui: ' . implode(' | ', $errors));
            }

            foreach ($peminjaman->detailPinjam as $detail) {
                $alatTerkunci[$detail->alat_id]->decrement('stok', $detail->jumlah);
            }

            $peminjaman->update(['status' => 'dipinjam']);

            LogAktivitas::create([
                'user_id'    => auth()->id(),
                'aktivitas'  => 'Menyetujui peminjaman ID: ' . $peminjaman->id,
                'modul'      => 'Peminjaman',
                'aksi'       => 'Approve',
                'ip_address' => request()->ip(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menolak peminjaman (FITUR BARU).
     * Cuma petugas, alasan wajib diisi.
     */
    public function tolakPeminjaman(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|min:10|max:500',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi.',
            'alasan_penolakan.min'      => 'Alasan penolakan minimal 10 karakter.',
            'alasan_penolakan.max'      => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::lockForUpdate()->findOrFail($id);

            if ($peminjaman->status !== 'diajukan') {
                DB::rollback();
                return back()->with('error', "Peminjaman ini berstatus '{$peminjaman->status}', bukan 'diajukan'.");
            }

            $peminjaman->update([
                'status'           => 'ditolak',
                'alasan_penolakan' => $request->alasan_penolakan,
                'ditolak_oleh'     => auth()->id(),
                'ditolak_pada'     => now(),
            ]);

            LogAktivitas::create([
                'user_id'    => auth()->id(),
                'aktivitas'  => 'Menolak peminjaman ID: ' . $peminjaman->id . ' — Alasan: ' . $request->alasan_penolakan,
                'modul'      => 'Peminjaman',
                'aksi'       => 'Reject',
                'ip_address' => request()->ip(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman berhasil ditolak.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ============================================
    // PENGEMBALIAN (SAMA KAYAK SEBELUMNYA)
    // ============================================

    public function prosesPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string',
            'denda_kerusakan' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')
                ->lockForUpdate()
                ->findOrFail($peminjamanId);

            if ($peminjaman->status !== 'dipinjam') {
                throw new \Exception("Peminjaman ini berstatus '{$peminjaman->status}', bukan 'dipinjam'.");
            }

            $tglKembaliPlan = Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
            $tglKembali     = Carbon::now()->startOfDay();

            $dendaTerlambat = 0;
            if ($tglKembali->gt($tglKembaliPlan)) {
                $selisihHari    = $tglKembali->diffInDays($tglKembaliPlan);
                $dendaTerlambat = $selisihHari * self::DENDA_PER_HARI;
            }

            $dendaKerusakan = $request->denda_kerusakan ?? 0;
            $totalDenda     = $dendaTerlambat + $dendaKerusakan;

            $statusBaru = $tglKembali->gt($tglKembaliPlan) ? 'telat' : 'dikembalikan';
            $peminjaman->update(['status' => $statusBaru]);

            foreach ($peminjaman->detailPinjam as $detail) {
                Alat::lockForUpdate()
                    ->find($detail->alat_id)
                    ->increment('stok', $detail->jumlah);
            }

            Pengembalian::create([
                'peminjaman_id'    => $peminjaman->id,
                'tgl_kembali'      => now(),
                'kondisi_kembali'  => $request->kondisi_kembali,
                'denda_terlambat'  => $dendaTerlambat,
                'denda_kerusakan'  => $dendaKerusakan,
                'total_denda'      => $totalDenda,
                'petugas_id'       => auth()->id(),
            ]);

            LogAktivitas::create([
                'user_id'    => auth()->id(),
                'aktivitas'  => 'Memproses pengembalian peminjaman ID: ' . $peminjaman->id . ' (Status: ' . $statusBaru . ')',
                'modul'      => 'Pengembalian',
                'aksi'       => 'Create',
                'ip_address' => request()->ip(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian berhasil dicatat. Total denda: Rp ' . number_format($totalDenda, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function indexPengembalian()
    {
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->whereIn('status', ['dipinjam', 'menunggu_verifikasi'])
            ->orderByRaw("FIELD(status, 'menunggu_verifikasi', 'dipinjam')")
            ->orderBy('created_at', 'asc')
            ->get();

        return view('petugas.pengembalian.index', compact('peminjaman'));
    }

    public function formPengembalian($id)
    {
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])->findOrFail($id);
        return view('petugas.pengembalian.form', compact('peminjaman'));
    }

    public function verifikasiPengembalian($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')
                ->lockForUpdate()
                ->findOrFail($id);

            if ($peminjaman->status !== 'menunggu_verifikasi') {
                DB::rollback();
                return redirect()->back()->with('error', 'Peminjaman ini tidak menunggu verifikasi.');
            }

            $tglKembaliPlan = Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
            $tglKembali     = Carbon::now()->startOfDay();

            $dendaTerlambat = 0;
            if ($tglKembali->gt($tglKembaliPlan)) {
                $selisihHari    = $tglKembali->diffInDays($tglKembaliPlan);
                $dendaTerlambat = $selisihHari * self::DENDA_PER_HARI;
            }

            $statusBaru = $tglKembali->gt($tglKembaliPlan) ? 'telat' : 'dikembalikan';

            $peminjaman->update(['status' => $statusBaru]);

            foreach ($peminjaman->detailPinjam as $detail) {
                Alat::lockForUpdate()
                    ->find($detail->alat_id)
                    ->increment('stok', $detail->jumlah);
            }

            Pengembalian::create([
                'peminjaman_id'    => $peminjaman->id,
                'tgl_kembali'      => now(),
                'kondisi_kembali'  => 'Baik',
                'denda_terlambat'  => $dendaTerlambat,
                'denda_kerusakan'  => 0,
                'total_denda'      => $dendaTerlambat,
                'petugas_id'       => auth()->id(),
            ]);

            LogAktivitas::create([
                'user_id'    => auth()->id(),
                'aktivitas'  => 'Memverifikasi pengembalian peminjaman ID: ' . $peminjaman->id . ' (Status: ' . $statusBaru . ')',
                'modul'      => 'Pengembalian',
                'aksi'       => 'Verify',
                'ip_address' => request()->ip(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian berhasil diverifikasi. Status: ' . $statusBaru);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ============================================
    // LAPORAN (SAMA KAYAK SEBELUMNYA)
    // ============================================

    public function laporan(Request $request)
    {
        $query = Peminjaman::with(['user', 'detailPinjam.alat']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('tgl_pinjam', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tgl_pinjam', '<=', $request->end_date);
        }
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $peminjaman = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'        => Peminjaman::count(),
            'dipinjam'     => Peminjaman::where('status', 'dipinjam')->count(),
            'dikembalikan' => Peminjaman::where('status', 'dikembalikan')->count(),
            'telat'        => Peminjaman::where('status', 'telat')->count(),
            'menunggu'     => Peminjaman::where('status', 'menunggu_verifikasi')->count(),
            'diajukan'     => Peminjaman::where('status', 'diajukan')->count(),
            'ditolak'      => Peminjaman::where('status', 'ditolak')->count(),
        ];

        return view('petugas.laporan.index', compact('peminjaman', 'stats'));
    }

    public function generateLaporanPDF(Request $request)
    {
        $query = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('tgl_pinjam', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tgl_pinjam', '<=', $request->end_date);
        }
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $peminjaman = $query->latest()->get();

        $totalDenda = $peminjaman->sum(function ($item) {
            return $item->pengembalian->total_denda ?? 0;
        });

        $data = [
            'peminjaman'     => $peminjaman,
            'total'          => $peminjaman->count(),
            'totalTransaksi' => $peminjaman->count(),
            'totalDenda'     => $totalDenda,
            'tanggal'        => now()->format('d-m-Y H:i:s'),
            'petugas'        => auth()->user()->name,
            'filter_status'  => $request->status ?? 'Semua',
        ];

        $pdf = Pdf::loadView('petugas.laporan.pdf', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan_peminjaman_' . now()->format('Y-m-d') . '.pdf');
    }
}
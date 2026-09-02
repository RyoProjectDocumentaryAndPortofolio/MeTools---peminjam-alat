<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use App\Models\Pengembalian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // ============================================
    // DASHBOARD
    // ============================================
    public function index()
    {
        $logs = LogAktivitas::with('user')->latest()->take(10)->get();
        return view('admin.dashboard', compact('logs'));
    }

    // ============================================
    // CRUD ALAT (LENGKAP + SEARCH & PAGINATION)
    // ============================================

    public function indexAlat(Request $request)
    {
        $search = $request->input('search');

        $alats = Alat::with('kategori')
            ->when($search, function ($query, $search) {
                return $query->where('nama_alat', 'like', '%' . $search . '%')
                    ->orWhere('status_kondisi', 'like', '%' . $search . '%')
                    ->orWhereHas('kategori', function ($q) use ($search) {
                        $q->where('nama_kategori', 'like', '%' . $search . '%');
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.alat.index', compact('alats', 'search'));
    }

    public function createAlat()
    {
        $kategoris = Kategori::all();
        return view('admin.alat.create', compact('kategoris'));
    }

    public function storeAlat(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'nama_alat' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'status_kondisi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }

        $alat = Alat::create($data);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menambahkan alat baru: ' . $request->nama_alat,
            'modul' => 'Alat',
            'aksi' => 'Create',
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.alat.index')->with('success', 'Alat berhasil ditambahkan');
    }

    public function editAlat($id)
    {
        $alat = Alat::findOrFail($id);
        $kategoris = Kategori::all();
        return view('admin.alat.edit', compact('alat', 'kategoris'));
    }

    public function updateAlat(Request $request, $id)
    {
        $alat = Alat::findOrFail($id);

        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'stok' => 'required|integer|min:0',
            'status_kondisi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('gambar')) {
            if ($alat->gambar && file_exists(public_path($alat->gambar))) {
                unlink(public_path($alat->gambar));
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }

        $alat->update($data);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Mengupdate alat: ' . $request->nama_alat,
            'modul' => 'Alat',
            'aksi' => 'Update',
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil diperbarui.');
    }

    public function destroyAlat($id)
    {
        $alat = Alat::findOrFail($id);

        if ($alat->gambar && file_exists(public_path($alat->gambar))) {
            unlink(public_path($alat->gambar));
        }

        $alat->delete();

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menghapus alat: ' . $alat->nama_alat,
            'modul' => 'Alat',
            'aksi' => 'Delete',
        ]);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil dihapus.');
    }

    // ============================================
    // CRUD USER (LENGKAP + SEARCH & PAGINATION)
    // ============================================

    public function indexUser(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('role', 'like', '%' . $search . '%');
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view('admin.user.index', compact('users', 'search'));
    }

    public function createUser()
    {
        return view('admin.user.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,petugas,peminjam',
            'no_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menambahkan user baru: ' . $user->name . ' (' . $user->role . ')',
            'modul' => 'User',
            'aksi' => 'Create',
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,petugas,peminjam',
            'no_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
        ]);

        $user = User::findOrFail($id);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Mengupdate user: ' . $user->name,
            'modul' => 'User',
            'aksi' => 'Update',
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil diperbarui!');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menghapus user: ' . $user->name,
            'modul' => 'User',
            'aksi' => 'Delete',
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus');
    }

    // ============================================
    // CRUD KATEGORI (LENGKAP + SEARCH & PAGINATION)
    // ============================================

    public function indexKategori(Request $request)
    {
        $search = $request->input('search');

        $kategori = Kategori::when($search, function ($query, $search) {
            return $query->where('nama_kategori', 'like', '%' . $search . '%');
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view('admin.kategori.index', compact('kategori', 'search'));
    }

    public function createKategori()
    {
        return view('admin.kategori.create');
    }

    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
        ]);

        $kategori = Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menambahkan kategori baru: ' . $request->nama_kategori,
            'modul' => 'Kategori',
            'aksi' => 'Create',
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function editKategori($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function updateKategori(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,' . $id,
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Mengupdate kategori: ' . $request->nama_kategori,
            'modul' => 'Kategori',
            'aksi' => 'Update',
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        if ($kategori->alat()->count() > 0) {
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data alat.');
        }

        $kategori->delete();

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menghapus kategori: ' . $kategori->nama_kategori,
            'modul' => 'Kategori',
            'aksi' => 'Delete',
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus');
    }

    // ============================================
    // CRUD PEMINJAMAN (LENGKAP)
    // ============================================

    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->when($search, function ($query, $search) {
                return $query->where('status', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.peminjaman.index', compact('peminjaman', 'search'));
    }

    public function createPeminjaman()
    {
        $users = User::where('role', 'peminjam')->get();
        $alats = Alat::where('stok', '>', 0)->get();
        return view('admin.peminjaman.create', compact('users', 'alats'));
    }

    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tgl_pinjam' => 'required|date',
            'tgl_kembali_plan' => 'required|date|after_or_equal:tgl_pinjam',
            'alat_id' => 'required|array',
            'alat_id.*' => 'exists:alat,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::create([
                'user_id' => $request->user_id,
                'tgl_pinjam' => $request->tgl_pinjam,
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status' => 'diajukan',
            ]);

            foreach ($request->alat_id as $index => $alatId) {
                $jumlahPinjam = $request->jumlah[$index];

                $alat = Alat::findOrFail($alatId);

                if ($alat->stok < $jumlahPinjam) {
                    throw new \Exception("Stok alat {$alat->nama_alat} tidak mencukupi.");
                }

                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => $jumlahPinjam,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.peminjaman.index')->with('success', 'Peminjaman berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function showPeminjaman($id)
    {
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])->findOrFail($id);
        return view('admin.peminjaman.show', compact('peminjaman'));
    }

    public function updateStatusPeminjaman(Request $request, $id)
    {
        $peminjaman = Peminjaman::with('detailPinjam.alat')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:diajukan,dipinjam,dikembalikan,telat',
        ]);

        DB::beginTransaction();
        try {
            $statusLama = $peminjaman->status;
            $statusBaru = $request->status;

            if ($statusLama != 'dipinjam' && $statusBaru == 'dipinjam') {
                foreach ($peminjaman->detailPinjam as $detail) {
                    $alat = $detail->alat;
                    if ($alat->stok < $detail->jumlah) {
                        throw new \Exception("Stok alat ({$alat->nama_alat}) tidak mencukupi untuk dipinjam.");
                    }
                    $alat->decrement('stok', $detail->jumlah);
                }
            } elseif ($statusLama == 'dipinjam' && ($statusBaru == 'dikembalikan' || $statusBaru == 'telat')) {
                foreach ($peminjaman->detailPinjam as $detail) {
                    $detail->alat->increment('stok', $detail->jumlah);
                }
            }

            $peminjaman->update(['status' => $statusBaru]);

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Mengubah status peminjaman ID: ' . $id . ' menjadi ' . $statusBaru,
                'modul' => 'Peminjaman',
                'aksi' => 'Update',
                'ip_address' => $request->ip()
            ]);

            DB::commit();
            return redirect()->route('admin.peminjaman.index')->with('success', 'Status peminjaman berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroyPeminjaman($id)
    {
        $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($id);

        DB::beginTransaction();
        try {
            if ($peminjaman->status == 'dipinjam') {
                foreach ($peminjaman->detailPinjam as $detail) {
                    $detail->alat->increment('stok', $detail->jumlah);
                }
            }

            $peminjaman->detailPinjam()->delete();
            $peminjaman->delete();

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Menghapus peminjaman ID: ' . $id,
                'modul' => 'Peminjaman',
                'aksi' => 'Delete',
            ]);

            DB::commit();
            return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    // ============================================
    // CRUD PENGEMBALIAN (WEB)
    // ============================================

    private const DENDA_PER_HARI = 5000;

    public function indexPengembalian()
    {
        $pengembalian = Pengembalian::with(['peminjaman.user', 'petugas'])
            ->whereHas('peminjaman', function ($query) {
                $query->whereIn('status', ['dikembalikan', 'telat']);
            })
            ->latest()
            ->paginate(10);

        return view('admin.pengembalian.index', compact('pengembalian'));
    }

    public function createPengembalian()
    {
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->whereIn('status', ['dikembalikan', 'telat'])
            ->get();

        return view('admin.pengembalian.create', compact('peminjaman'));
    }

    public function storePengembalian(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'kondisi_kembali' => 'required|in:Baik,Rusak,Perbaikan',
            'denda_kerusakan' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')
                ->lockForUpdate()
                ->findOrFail($request->peminjaman_id);

            // HAPUS PENGECEKAN INI!
            // if ($peminjaman->status !== 'dipinjam') {
            //     throw new \Exception("Peminjaman ini tidak sedang dipinjam.");
            // }

            // Hitung denda keterlambatan
            $tglKembaliPlan = Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
            $tglKembali = Carbon::now()->startOfDay();

            $dendaTerlambat = 0;
            if ($tglKembali->gt($tglKembaliPlan)) {
                $selisihHari = $tglKembali->diffInDays($tglKembaliPlan);
                $dendaTerlambat = $selisihHari * self::DENDA_PER_HARI;
            }

            $dendaKerusakan = $request->denda_kerusakan ?? 0;
            $totalDenda = $dendaTerlambat + $dendaKerusakan;

            // Update status peminjaman (kalau perlu)
            // $statusBaru = $tglKembali->gt($tglKembaliPlan) ? 'telat' : 'dikembalikan';
            // $peminjaman->update(['status' => $statusBaru]);

            // Kembalikan stok alat (kalau perlu)
            // foreach ($peminjaman->detailPinjam as $detail) {
            //     $alat = Alat::lockForUpdate()->find($detail->alat_id);
            //     $alat->increment('stok', $detail->jumlah);
            // }

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

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Memproses pengembalian peminjaman ID: ' . $peminjaman->id,
                'modul' => 'Pengembalian',
                'aksi' => 'Create',
                'ip_address' => $request->ip()
            ]);

            DB::commit();

            return redirect()->route('admin.pengembalian.index')
                ->with('success', 'Pengembalian berhasil diproses.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    public function showPengembalian($id)
    {
        $pengembalian = Pengembalian::with([
            'peminjaman.user',
            'peminjaman.detailPinjam.alat',
            'petugas'
        ])->findOrFail($id);

        return view('admin.pengembalian.show', compact('pengembalian'));
    }

    public function destroyPengembalian($id)
    {
        $pengembalian = Pengembalian::findOrFail($id);

        DB::transaction(function () use ($pengembalian) {
            $peminjaman = Peminjaman::with('detailPinjam')
                ->lockForUpdate()
                ->findOrFail($pengembalian->peminjaman_id);

            // Kembalikan status ke 'dipinjam'
            $peminjaman->update(['status' => 'dipinjam']);

            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::lockForUpdate()->findOrFail($detail->alat_id);
                if ($alat->stok < $detail->jumlah) {
                    throw new \Exception("Stok alat '{$alat->nama_alat}' tidak mencukupi.");
                }
                $alat->decrement('stok', $detail->jumlah);
            }

            $pengembalian->delete();
        });

        return redirect()->route('admin.pengembalian.index')
            ->with('success', 'Pengembalian berhasil dihapus.');
    }
}
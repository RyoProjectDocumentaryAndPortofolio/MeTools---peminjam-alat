<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
    // CRUD ALAT
    // ============================================
    public function indexAlat()
    {
        $alats = Alat::with('kategori')->get();
        return view('admin.alat.index', compact('alats'));
    }

    public function storeAlat(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'nama_alat' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'status_kondisi' => 'required|string',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('alat', 'public');
        }

        $alat = Alat::create([
            'kategori_id' => $request->kategori_id,
            'nama_alat' => $request->nama_alat,
            'stok' => $request->stok,
            'status_kondisi' => $request->status_kondisi,
            'deskripsi' => $request->deskripsi,
            'gambar' => $gambarPath
        ]);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menambahkan alat baru: ' . $request->nama_alat,
            'modul' => 'Alat',
            'aksi' => 'Create',
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', 'Alat berhasil ditambahkan');
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

        // Cek apakah kategori masih dipakai oleh alat
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
}
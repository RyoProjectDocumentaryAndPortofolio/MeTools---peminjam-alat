<?php

use Illuminate\Support\Facades\Route;
use App\Models\Alat;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PeminjamController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\testController;

// Route utama
Route::get('/', function () {
    $alats = Alat::with('kategori')
        ->where('stok', '>', 0)
        ->where('status_kondisi', 'Baik')
        ->latest()
        ->take(10)
        ->get();

    return view('landing', compact('alats'));
})->name('landing');

// Route tamu (belum login)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);


Route::middleware(['auth', 'role.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // CRUD Alat
    Route::get('/alat', [AdminController::class, 'indexAlat'])->name('alat.index');
    Route::get('/alat/create', [AdminController::class, 'createAlat'])->name('alat.create');
    Route::post('/alat', [AdminController::class, 'storeAlat'])->name('alat.store');
    Route::get('/alat/{id}/edit', [AdminController::class, 'editAlat'])->name('alat.edit');
    Route::put('/alat/{id}', [AdminController::class, 'updateAlat'])->name('alat.update');
    Route::delete('/alat/{id}', [AdminController::class, 'destroyAlat'])->name('alat.destroy');

    // CRUD Kategori
    Route::get('/kategori', [AdminController::class, 'indexKategori'])->name('kategori.index');
    Route::get('/kategori/create', [AdminController::class, 'createKategori'])->name('kategori.create');
    Route::post('/kategori', [AdminController::class, 'storeKategori'])->name('kategori.store');
    Route::get('/kategori/{id}/edit', [AdminController::class, 'editKategori'])->name('kategori.edit');
    Route::put('/kategori/{id}', [AdminController::class, 'updateKategori'])->name('kategori.update');
    Route::delete('/kategori/{id}', [AdminController::class, 'destroyKategori'])->name('kategori.destroy');

    // CRUD User
    Route::get('/users', [AdminController::class, 'indexUser'])->name('user.index');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('user.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('user.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('user.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('user.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('user.destroy');

    // CRUD Peminjaman (Admin)
    Route::get('/peminjaman', [AdminController::class, 'indexPeminjaman'])->name('peminjaman.index');
    Route::get('/peminjaman/create', [AdminController::class, 'createPeminjaman'])->name('peminjaman.create');
    Route::post('/peminjaman', [AdminController::class, 'storePeminjaman'])->name('peminjaman.store');
    Route::put('/peminjaman/{id}/status', [AdminController::class, 'updateStatusPeminjaman'])->name('peminjaman.updateStatus');
    Route::delete('/peminjaman/{id}', [AdminController::class, 'destroyPeminjaman'])->name('peminjaman.destroy');

    // CRUD Pengembalian (Admin)
    Route::get('/pengembalian', [AdminController::class, 'indexPengembalian'])->name('pengembalian.index');
    Route::get('/pengembalian/create', [AdminController::class, 'createPengembalian'])->name('pengembalian.create');
    Route::post('/pengembalian', [AdminController::class, 'storePengembalian'])->name('pengembalian.store');
    Route::get('/pengembalian/{id}', [AdminController::class, 'showPengembalian'])->name('pengembalian.show');
    Route::delete('/pengembalian/{id}', [AdminController::class, 'destroyPengembalian'])->name('pengembalian.destroy');

    // CRUD Log Aktivitas (Admin)
    Route::get('/log-aktivitas', [AdminController::class, 'indexLogAktivitas'])->name('log-aktivitas.index');
});

// ============================================
// ROUTE PETUGAS
// ============================================
Route::middleware(['auth', 'role.petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    // Peminjaman
    Route::get('/peminjaman', [PetugasController::class, 'indexPeminjaman'])->name('peminjaman.index');
    Route::post('/peminjaman/{id}/setujui', [PetugasController::class, 'setujuiPeminjaman'])->name('peminjaman.setujui');
    Route::post('/peminjaman/{id}/tolak', [PetugasController::class, 'tolakPeminjaman'])->name('peminjaman.tolak');
    Route::post('/peminjaman/{id}/proses', [PetugasController::class, 'prosesPengembalian'])->name('peminjaman.proses');

    // Pengembalian
    Route::get('/pengembalian', [PetugasController::class, 'indexPengembalian'])->name('pengembalian.index');
    Route::post('/pengembalian/{id}', [PetugasController::class, 'prosesPengembalian'])->name('pengembalian.proses');

    Route::post('/verifikasi-kembali/{id}', [PetugasController::class, 'verifikasiPengembalian'])->name('verifikasi.kembali');

      Route::get('/laporan', [PetugasController::class, 'laporan'])->name('laporan.index');
    Route::get('/laporan/pdf', [PetugasController::class, 'generateLaporanPDF'])->name('laporan.pdf');
});

// ============================================
// ROUTE PEMINJAM
// ============================================
Route::middleware(['auth', 'role.peminjam'])->prefix('peminjam')->name('peminjam.')->group(function () {
    Route::get('/katalog', [PeminjamController::class, 'katalog'])->name('katalog');
    Route::post('/peminjaman/ajukan', [PeminjamController::class, 'ajukanPeminjaman'])->name('peminjaman.ajukan');
    Route::get('/riwayat', [PeminjamController::class, 'riwayatPeminjaman'])->name('riwayat');
    Route::post('/kembalikan/{id}', [PeminjamController::class, 'kembalikan'])->name('kembalikan');
});

// ============================================
// ROUTE LOGOUT
// ============================================
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

//test
Route::get('/test', [testController::class, 'test'])->name('test');
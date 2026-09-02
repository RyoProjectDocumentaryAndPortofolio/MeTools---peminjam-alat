<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\AlatController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PeminjamanController;
use App\Http\Controllers\Api\PengembalianController;
use App\Http\Controllers\Api\LogAktivitasController;
use App\Http\Controllers\Api\LaporanController; 

// Public Routes (Tidak perlu token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Wajib membawa Bearer Token dari Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin Only
    Route::middleware('role.admin')->group(function () {
        Route::apiResource('kategori', KategoriController::class);
        Route::apiResource('alat', AlatController::class);
        Route::get('/katalog', [AlatController::class, 'katalog']);
        Route::apiResource('users', UserController::class);

        // Route Peminjaman untuk Admin
        Route::get('/peminjaman', [PeminjamanController::class, 'index']);
        Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show']);
        Route::post('/peminjaman/{peminjaman}/approve', [PeminjamanController::class, 'approve']);
        Route::put('/peminjaman/{peminjaman}', [PeminjamanController::class, 'update']);
        Route::delete('/peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy']);

        // Route Pengembalian untuk Admin
        Route::get('/pengembalian', [PengembalianController::class, 'index']);
        Route::get('/pengembalian/{pengembalian}', [PengembalianController::class, 'show']);
        Route::put('/pengembalian/{pengembalian}', [PengembalianController::class, 'update']);
        Route::delete('/pengembalian/{pengembalian}', [PengembalianController::class, 'destroy']);

        // Route Log Aktivitas untuk Admin
        Route::get('/log-aktivitas', [LogAktivitasController::class, 'index']);
        
        // Route Laporan untuk Admin
        Route::get('/laporan-peminjaman', [LaporanController::class, 'index']);
    });

    // Petugas Only
    Route::middleware('role.petugas')->group(function () {
        // Route Peminjaman untuk Petugas
        Route::post('/peminjaman/{peminjaman}/approve', [PeminjamanController::class, 'approve']);

        // Route Pengembalian untuk Petugas
        Route::post('/pengembalian', [PengembalianController::class, 'store']);

        // Route Laporan untuk Petugas
        Route::get('/laporan-peminjaman', [LaporanController::class, 'index']);
    });

    // Peminjam Only
    Route::middleware('role.peminjam')->group(function () {
        Route::get('/katalog', [AlatController::class, 'katalog']);

        // Route Peminjaman untuk Peminjam
        Route::post('/peminjaman', [PeminjamanController::class, 'store']);
        Route::get('/riwayat-pinjam', [PeminjamanController::class, 'riwayat']);
    });
});
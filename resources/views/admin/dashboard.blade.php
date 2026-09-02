@extends('layouts.app')

@section('title', 'Dashboard Admin - Toolsme')
@section('header-title', 'Ringkasan Aktivitas Sistem')

@section('content')
<div class="stats-grid">

    <!-- Total User -->
    <div class="stat-card">
        <div class="stat-icon">
            <span class="material-symbols-outlined">people</span>
        </div>
        <div class="stat-info">
            <p class="stat-label">Total User</p>
            <p class="stat-value">{{ $totalUsers ?? 0 }}</p>
        </div>
    </div>

    <!-- Total Alat -->
    <div class="stat-card">
        <div class="stat-icon">
            <span class="material-symbols-outlined">inventory_2</span>
        </div>
        <div class="stat-info">
            <p class="stat-label">Total Alat</p>
            <p class="stat-value">{{ $totalAlat ?? 0 }}</p>
        </div>
    </div>

    <!-- Total Peminjaman -->
    <div class="stat-card">
        <div class="stat-icon">
            <span class="material-symbols-outlined">assignment</span>
        </div>
        <div class="stat-info">
            <p class="stat-label">Total Peminjaman</p>
            <p class="stat-value">{{ $totalPeminjaman ?? 0 }}</p>
        </div>
    </div>

    <!-- Peminjaman Aktif -->
    <div class="stat-card">
        <div class="stat-icon">
            <span class="material-symbols-outlined">check_circle</span>
        </div>
        <div class="stat-info">
            <p class="stat-label">Peminjaman Aktif</p>
            <p class="stat-value">{{ $peminjamanAktif ?? 0 }}</p>
        </div>
    </div>

</div>

<!-- ============================================
ALERT SELAMAT DATANG
============================================ -->
<div class="alert-welcome">
    <span class="material-symbols-outlined">handshake</span>
    <div>
        Selamat datang, <strong>{{ auth()->user()->name }}</strong>!
        Anda login sebagai <span class="role-badge">{{ auth()->user()->role }}</span>.
    </div>
</div>

<!-- ============================================
LOG AKTIVITAS TERBARU
============================================ -->
<div class="log-card">
    <div class="log-header">
        <h3>
            <span class="material-symbols-outlined">history</span>
            Log Aktivitas Terbaru
        </h3>
    </div>

    <div class="log-table-wrapper">
        <table class="log-table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Aktivitas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs ?? [] as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d-m-Y H:i') }}</td>
                        <td class="user-cell">{{ $log->user->name ?? 'Sistem' }}</td>
                        <td>{{ $log->aktivitas }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty-cell">Belum ada aktivitas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush
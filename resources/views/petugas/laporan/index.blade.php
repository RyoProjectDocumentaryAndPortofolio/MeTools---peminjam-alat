@extends('layouts.app')

@section('title', 'Laporan Peminjaman - Toolsme')
@section('header-title', 'Laporan Peminjaman Alat')

@section('content')

<!-- Statistik -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><span class="material-symbols-outlined">assignment</span></div>
        <div class="stat-info">
            <p class="stat-label">Total Peminjaman</p>
            <p class="stat-value">{{ $stats['total'] }}</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><span class="material-symbols-outlined">inventory_2</span></div>
        <div class="stat-info">
            <p class="stat-label">Sedang Dipinjam</p>
            <p class="stat-value" style="color:#4facfe;">{{ $stats['dipinjam'] }}</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><span class="material-symbols-outlined">check_circle</span></div>
        <div class="stat-info">
            <p class="stat-label">Dikembalikan</p>
            <p class="stat-value" style="color:#4caf50;">{{ $stats['dikembalikan'] }}</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><span class="material-symbols-outlined">error</span></div>
        <div class="stat-info">
            <p class="stat-label">Telat</p>
            <p class="stat-value" style="color:#ff1744;">{{ $stats['telat'] }}</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><span class="material-symbols-outlined">hourglass_top</span></div>
        <div class="stat-info">
            <p class="stat-label">Menunggu Verifikasi</p>
            <p class="stat-value" style="color:#ff9800;">{{ $stats['menunggu'] }}</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><span class="material-symbols-outlined">pending</span></div>
        <div class="stat-info">
            <p class="stat-label">Diajukan</p>
            <p class="stat-value" style="color:#ffc107;">{{ $stats['diajukan'] }}</p>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="filter-card">
    <form action="{{ route('petugas.laporan.index') }}" method="GET" class="filter-form">
        <div class="filter-row">
            <!-- Search -->
            <div class="filter-group">
                <label>🔍 Cari Peminjam</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama peminjam..." class="form-input">
            </div>

            <!-- Status -->
            <div class="filter-group">
                <label>📌 Status</label>
                <select name="status" class="form-input">
                    <option value="">Semua Status</option>
                    <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="menunggu_verifikasi" {{ request('status') == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="telat" {{ request('status') == 'telat' ? 'selected' : '' }}>Telat</option>
                </select>
            </div>

            <!-- Start Date -->
            <div class="filter-group">
                <label>📅 Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-input">
            </div>

            <!-- End Date -->
            <div class="filter-group">
                <label>📅 Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-input">
            </div>

            <!-- Buttons -->
            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <span class="material-symbols-outlined">filter_alt</span>
                    Filter
                </button>
                <a href="{{ route('petugas.laporan.index') }}" class="btn-reset">
                    <span class="material-symbols-outlined">refresh</span>
                    Reset
                </a>
                <a href="{{ route('petugas.laporan.pdf', request()->query()) }}" class="btn-pdf" target="_blank">
                    <span class="material-symbols-outlined">picture_as_pdf</span>
                    Download PDF
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Table -->
<div class="laporan-card">
    <div class="laporan-header">
        <h3>
            <span class="material-symbols-outlined">receipt_long</span>
            Data Peminjaman
        </h3>
        <span class="total-data">Total: {{ $peminjaman->total() }} data</span>
    </div>

    <div class="table-wrapper">
        <table class="laporan-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Peminjam</th>
                    <th>Tanggal Pinjam</th>
                    <th>Rencana Kembali</th>
                    <th>Status</th>
                    <th>Alat</th>
                    <th>Denda</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjaman as $item)
                    <tr>
                        <td>#{{ $item->id }}</td>
                        <td>{{ $item->user->name ?? '-' }}</td>
                        <td>{{ $item->tgl_pinjam }}</td>
                        <td>{{ $item->tgl_kembali_plan }}</td>
                        <td>
                            <span class="status-badge {{ $item->status }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td>
                            <ul class="alat-list">
                                @foreach($item->detailPinjam as $detail)
                                    <li>{{ $detail->alat->nama_alat ?? 'Alat' }} ({{ $detail->jumlah }} pcs)</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            @if($item->pengembalian)
                                Rp {{ number_format($item->pengembalian->total_denda ?? 0, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-cell">Tidak ada data peminjaman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $peminjaman->links() }}
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/petugas.css') }}">
<link rel="stylesheet" href="{{ asset('css/laporan.css') }}">
@endpush
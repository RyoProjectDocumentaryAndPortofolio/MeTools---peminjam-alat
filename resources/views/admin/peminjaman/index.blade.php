@extends('layouts.app')

@section('title', 'Kelola Peminjaman - Toolsme')
@section('header-title', 'Manajemen Transaksi Peminjaman')

@section('content')

<!-- Alert -->
@if(session('success'))
    <div class="alert-success">
        <span class="material-symbols-outlined">check_circle</span>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert-error">
        <span class="material-symbols-outlined">error</span>
        {{ session('error') }}
    </div>
@endif

<!-- Card -->
<div class="peminjaman-card">
    <div class="peminjaman-header">
        <h3>
            <span class="material-symbols-outlined">assignment</span>
            Daftar Transaksi Peminjaman
        </h3>

        <div class="header-actions">
            <!-- Search -->
            <form action="{{ route('admin.peminjaman.index') }}" method="GET" class="search-form">
                <div class="search-wrapper">
                    <span class="material-symbols-outlined search-icon">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam / status..." class="search-input">
                    @if(request('search'))
                        <a href="{{ route('admin.peminjaman.index') }}" class="search-reset" title="Reset Pencarian">
                            <span class="material-symbols-outlined">close</span>
                        </a>
                    @endif
                    <button type="submit" class="search-btn">Cari</button>
                </div>
            </form>

            <!-- Tambah -->
            <a href="{{ route('admin.peminjaman.create') }}" class="btn-add">
                <span class="material-symbols-outlined">add</span>
                Tambah Peminjaman
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="table-wrapper">
        <table class="peminjaman-table">
            <thead>
                <tr>
                    <th>Peminjam</th>
                    <th>Alat Dipinjam</th>
                    <th>Tanggal Pinjam</th>
                    <th>Rencana Kembali</th>
                    <th>Status</th>
                    <th width="200">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjaman as $item)
                    <tr>
                        <td class="peminjam-name">{{ $item->user->name ?? 'User Dihapus' }}</td>
                        <td>
                            <ul class="alat-list">
                                @foreach($item->detailPinjam as $detail)
                                    <li>
                                        {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}
                                        <span class="jumlah-badge">x{{ $detail->jumlah }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{ $item->tgl_pinjam }}</td>
                        <td>{{ $item->tgl_kembali_plan }}</td>
                        <td>
                            <span class="status-badge {{ $item->status }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <!-- Form Update Status -->
                                <form action="{{ route('admin.peminjaman.updateStatus', $item->id) }}" method="POST" class="status-form">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="status-select">
                                        <option value="diajukan" {{ $item->status == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                                        <option value="dipinjam" {{ $item->status == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                        <option value="dikembalikan" {{ $item->status == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                                        <option value="telat" {{ $item->status == 'telat' ? 'selected' : '' }}>Telat</option>
                                    </select>
                                    <button type="submit" class="btn-update" title="Update Status">
                                        <span class="material-symbols-outlined">refresh</span>
                                    </button>
                                </form>

                                <!-- Delete -->
                                <form action="{{ route('admin.peminjaman.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus peminjaman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" title="Hapus Peminjaman">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-cell">Belum ada data peminjaman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $peminjaman->links() }}
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/peminjaman.css') }}">
@endpush
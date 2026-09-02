@extends('layouts.app')

@section('title', 'Kelola Pengembalian - Toolsme')
@section('header-title', 'Daftar Transaksi Pengembalian')

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
<div class="pengembalian-card">
    <div class="pengembalian-header">
        <h3>
            <span class="material-symbols-outlined">swap_horiz</span>
            Daftar Transaksi Pengembalian
        </h3>
        <div class="header-actions">
            <a href="{{ route('admin.pengembalian.create') }}" class="btn-add">
                <span class="material-symbols-outlined">add</span>
                Proses Pengembalian
            </a>
            <a href="{{ route('admin.peminjaman.index') }}" class="btn-add" style="background: linear-gradient(135deg, #4facfe, #1a2a6c);">
                <span class="material-symbols-outlined">assignment</span>
                Lihat Peminjaman
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="table-wrapper">
        <table class="pengembalian-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Peminjaman</th>
                    <th>Peminjam</th>
                    <th>Tgl Kembali</th>
                    <th>Kondisi</th>
                    <th>Denda Terlambat</th>
                    <th>Denda Kerusakan</th>
                    <th>Total Denda</th>
                    <th>Petugas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengembalian as $item)
                    <tr>
                        <td>#{{ $item->id }}</td>
                        <td>#{{ $item->peminjaman_id }}</td>
                        <td>{{ $item->peminjaman->user->name ?? '-' }}</td>
                        <td>{{ $item->tgl_kembali->format('d-m-Y') }}</td>
                        <td>{{ $item->kondisi_kembali }}</td>
                        <td>Rp {{ number_format($item->denda_terlambat, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->denda_kerusakan, 0, ',', '.') }}</td>
                        <td class="total-denda">Rp {{ number_format($item->total_denda, 0, ',', '.') }}</td>
                        <td>{{ $item->petugas->name ?? '-' }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.pengembalian.show', $item->id) }}" class="btn-edit" title="Detail">
                                    <span class="material-symbols-outlined">visibility</span>
                                </a>
                                <form action="{{ route('admin.pengembalian.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengembalian ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" title="Hapus">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="empty-cell">Belum ada data pengembalian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $pengembalian->links() }}
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pengembalian.css') }}">
@endpush
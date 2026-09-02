@extends('layouts.app')

@section('title', 'Kelola Alat - Toolsme')
@section('header-title', 'Manajemen Data Alat')

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
<div class="alat-card">
    <div class="alat-header">
        <h3>
            <span class="material-symbols-outlined">inventory_2</span>
            Daftar Alat Laboratorium
        </h3>

        <div class="header-actions">
            <!-- Search -->
            <form action="{{ route('admin.alat.index') }}" method="GET" class="search-form">
                <div class="search-wrapper">
                    <span class="material-symbols-outlined search-icon">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama alat, kategori..." class="search-input">
                    @if(request('search'))
                        <a href="{{ route('admin.alat.index') }}" class="search-reset" title="Reset Pencarian">
                            <span class="material-symbols-outlined">close</span>
                        </a>
                    @endif
                    <button type="submit" class="search-btn">Cari</button>
                </div>
            </form>

            <!-- Tambah -->
            <a href="{{ route('admin.alat.create') }}" class="btn-add">
                <span class="material-symbols-outlined">add</span>
                Tambah Alat
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="table-wrapper">
        <table class="alat-table">
            <thead>
                <tr>
                    <th width="70">Gambar</th>
                    <th>Nama Alat</th>
                    <th>Kategori</th>
                    <th width="80">Stok</th>
                    <th>Kondisi</th>
                    <th width="180">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alats as $alat)
                    <tr>
                        <td>
                            @if($alat->gambar)
                                <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="alat-image">
                            @else
                                <span class="no-image">Tidak ada</span>
                            @endif
                        </td>
                        <td class="alat-name">{{ $alat->nama_alat }}</td>
                        <td>{{ $alat->kategori->nama_kategori ?? '-' }}</td>
                        <td class="text-center">{{ $alat->stok }}</td>
                        <td>
                            <span class="kondisi-badge {{ strtolower($alat->status_kondisi) == 'baik' ? 'baik' : 'rusak' }}">
                                {{ $alat->status_kondisi }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.alat.edit', $alat->id) }}" class="btn-edit" title="Edit Alat">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>
                                <form action="{{ route('admin.alat.destroy', $alat->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus alat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" title="Hapus Alat">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-cell">Belum ada data alat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $alats->links() }}
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/alat.css') }}">
@endpush
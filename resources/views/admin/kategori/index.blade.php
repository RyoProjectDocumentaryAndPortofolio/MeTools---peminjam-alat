@extends('layouts.app')

@section('title', 'Kelola Kategori - Toolsme')
@section('header-title', 'Manajemen Kategori Alat')

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
<div class="kategori-card">
    <div class="kategori-header">
        <h3>
            <span class="material-symbols-outlined">category</span>
            Daftar Kategori Alat
        </h3>

        <div class="header-actions">
            <!-- Search -->
            <form action="{{ route('admin.kategori.index') }}" method="GET" class="search-form">
                <div class="search-wrapper">
                    <span class="material-symbols-outlined search-icon">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori..." class="search-input">
                    @if(request('search'))
                        <a href="{{ route('admin.kategori.index') }}" class="search-reset" title="Reset Pencarian">
                            <span class="material-symbols-outlined">close</span>
                        </a>
                    @endif
                    <button type="submit" class="search-btn">Cari</button>
                </div>
            </form>

            <!-- Tambah -->
            <a href="{{ route('admin.kategori.create') }}" class="btn-add">
                <span class="material-symbols-outlined">add</span>
                Tambah Kategori
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="table-wrapper">
        <table class="kategori-table">
            <thead>
                <tr>
                    <th width="60">No</th>
                    <th>Nama Kategori</th>
                    <th width="180">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kategori as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + ($kategori->currentPage() - 1) * $kategori->perPage() }}</td>
                        <td class="kategori-name">{{ $item->nama_kategori }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.kategori.edit', $item->id) }}" class="btn-edit" title="Edit Kategori">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>
                                <form action="{{ route('admin.kategori.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" title="Hapus Kategori">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty-cell">Belum ada data kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $kategori->links() }}
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/kategori.css') }}">
@endpush
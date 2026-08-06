@extends('layouts.app')

@section('title', 'Kelola User - Toolsme')
@section('header-title', 'Manajemen Pengguna Sistem')

@section('content')

<!-- Alert Success -->
@if(session('success'))
    <div class="alert-success">
        <span class="material-symbols-outlined">check_circle</span>
        {{ session('success') }}
    </div>
@endif

<!-- Alert Error -->
@if(session('error'))
    <div class="alert-error">
        <span class="material-symbols-outlined">error</span>
        {{ session('error') }}
    </div>
@endif

<!-- User Card -->
<div class="user-card">
    <!-- Header -->
    <div class="user-header">
        <h3>
            <span class="material-symbols-outlined">people</span>
            Daftar Pengguna Sistem
        </h3>

        <!-- Search & Tambah User -->
        <div class="header-actions">
            <!-- Form Search -->
            <form action="{{ route('admin.user.index') }}" method="GET" class="search-form">
                <div class="search-wrapper">
                    <span class="material-symbols-outlined search-icon">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, role..." class="search-input">
                    @if(request('search'))
                        <a href="{{ route('admin.user.index') }}" class="search-reset" title="Reset Pencarian">
                            <span class="material-symbols-outlined">close</span>
                        </a>
                    @endif
                    <button type="submit" class="search-btn">Cari</button>
                </div>
            </form>

            <!-- Tombol Tambah User -->
            <a href="{{ route('admin.user.create') }}" class="btn-add">
                <span class="material-symbols-outlined">add</span>
                Tambah User
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="table-wrapper">
        <table class="user-table">
            <thead>
                <tr>
                    <th width="60">No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>No. HP</th>
                    <th width="180">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                        <td class="user-name">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="role-badge {{ $user->role }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>{{ $user->no_hp ?? '-' }}</td>
                        <td>
                            <div class="action-buttons">
                                <!-- Edit -->
                                <a href="{{ route('admin.user.edit', $user->id) }}" class="btn-edit" title="Edit User">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>
                                <!-- Delete -->
                                <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" title="Hapus User">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-cell">Belum ada data pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $users->links() }}
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user.css') }}">
@endpush
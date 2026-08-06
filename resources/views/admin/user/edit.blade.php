@extends('layouts.app')

@section('title', 'Edit User - Toolsme')
@section('header-title', 'Edit Pengguna')

@section('content')

<!-- Alert Error -->
@if($errors->any())
    <div class="alert-error">
        <span class="material-symbols-outlined">error</span>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Form Card -->
<div class="form-card">
    <div class="form-header">
        <h3>
            <span class="material-symbols-outlined">edit</span>
            Edit Pengguna
        </h3>
        <p class="form-subtitle">Perbarui data pengguna <strong>{{ $user->name }}</strong></p>
    </div>

    <form action="{{ route('admin.user.update', $user->id) }}" method="POST" class="form-container">
        @csrf
        @method('PUT')

        <!-- Nama -->
        <div class="form-group">
            <label for="name">
                <span class="material-symbols-outlined">badge</span>
                Nama Lengkap
            </label>
            <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap" required>
            @error('name')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">
                <span class="material-symbols-outlined">mail</span>
                Email
            </label>
            <input type="email" name="email" id="email" class="form-input" value="{{ old('email', $user->email) }}" placeholder="Masukkan email" required>
            @error('email')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password (Opsional) -->
        <div class="form-group">
            <label for="password">
                <span class="material-symbols-outlined">lock</span>
                Password <span class="text-muted">(Kosongkan jika tidak diubah)</span>
            </label>
            <input type="password" name="password" id="password" class="form-input" placeholder="Minimal 8 karakter">
            @error('password')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Role -->
        <div class="form-group">
            <label for="role">
                <span class="material-symbols-outlined">manage_accounts</span>
                Role / Hak Akses
            </label>
            <select name="role" id="role" class="form-input" required>
                <option value="">Pilih Role</option>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="petugas" {{ old('role', $user->role) == 'petugas' ? 'selected' : '' }}>Petugas</option>
                <option value="peminjam" {{ old('role', $user->role) == 'peminjam' ? 'selected' : '' }}>Peminjam</option>
            </select>
            @error('role')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- No HP -->
        <div class="form-group">
            <label for="no_hp">
                <span class="material-symbols-outlined">phone</span>
                No. HP (Opsional)
            </label>
            <input type="text" name="no_hp" id="no_hp" class="form-input" value="{{ old('no_hp', $user->no_hp) }}" placeholder="Contoh: 081234567890">
            @error('no_hp')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Alamat -->
        <div class="form-group">
            <label for="alamat">
                <span class="material-symbols-outlined">home</span>
                Alamat (Opsional)
            </label>
            <textarea name="alamat" id="alamat" class="form-input" rows="2" placeholder="Masukkan alamat">{{ old('alamat', $user->alamat) }}</textarea>
            @error('alamat')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Tombol -->
        <div class="form-actions">
            <a href="{{ route('admin.user.index') }}" class="btn-cancel">
                <span class="material-symbols-outlined">close</span>
                Batal
            </a>
            <button type="submit" class="btn-submit">
                <span class="material-symbols-outlined">save</span>
                Perbarui
            </button>
        </div>
    </form>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user.css') }}">
@endpush
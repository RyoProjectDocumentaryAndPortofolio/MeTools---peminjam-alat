@extends('layouts.app')

@section('title', 'Tambah User - Toolsme')
@section('header-title', 'Tambah Pengguna Baru')

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
            <span class="material-symbols-outlined">person_add</span>
            Tambah Pengguna Baru
        </h3>
        <p class="form-subtitle">Isi data pengguna baru untuk diberikan akses ke sistem</p>
    </div>

    <form action="{{ route('admin.user.store') }}" method="POST" class="form-container">
        @csrf

        <!-- Nama -->
        <div class="form-group">
            <label for="name">
                <span class="material-symbols-outlined">badge</span>
                Nama Lengkap
            </label>
            <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
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
            <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}" placeholder="Masukkan email" required>
            @error('email')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password">
                <span class="material-symbols-outlined">lock</span>
                Password
            </label>
            <input type="password" name="password" id="password" class="form-input" placeholder="Minimal 8 karakter" required>
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
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="petugas" {{ old('role') == 'petugas' ? 'selected' : '' }}>Petugas</option>
                <option value="peminjam" {{ old('role') == 'peminjam' ? 'selected' : '' }}>Peminjam</option>
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
            <input type="text" name="no_hp" id="no_hp" class="form-input" value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890">
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
            <textarea name="alamat" id="alamat" class="form-input" rows="2" placeholder="Masukkan alamat">{{ old('alamat') }}</textarea>
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
                Simpan
            </button>
        </div>
    </form>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user.css') }}">
@endpush
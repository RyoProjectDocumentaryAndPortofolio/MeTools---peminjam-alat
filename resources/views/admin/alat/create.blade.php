@extends('layouts.app')

@section('title', 'Tambah Alat - Toolsme')
@section('header-title', 'Tambah Alat Baru')

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
            <span class="material-symbols-outlined">add</span>
            Tambah Alat Baru
        </h3>
        <p class="form-subtitle">Isi data alat yang akan ditambahkan ke inventaris</p>
    </div>

    <form action="{{ route('admin.alat.store') }}" method="POST" enctype="multipart/form-data" class="form-container">
        @csrf

        <!-- Nama Alat -->
        <div class="form-group">
            <label for="nama_alat">
                <span class="material-symbols-outlined">badge</span>
                Nama Alat
            </label>
            <input type="text" name="nama_alat" id="nama_alat" class="form-input" value="{{ old('nama_alat') }}" placeholder="Contoh: Multimeter Digital" required>
            @error('nama_alat')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Kategori -->
        <div class="form-group">
            <label for="kategori_id">
                <span class="material-symbols-outlined">category</span>
                Kategori
            </label>
            <select name="kategori_id" id="kategori_id" class="form-input" required>
                <option value="">Pilih Kategori</option>
                @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori }}
                    </option>
                @endforeach
            </select>
            @error('kategori_id')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Stok & Kondisi -->
        <div class="form-row">
            <div class="form-group half">
                <label for="stok">
                    <span class="material-symbols-outlined">numbers</span>
                    Stok
                </label>
                <input type="number" name="stok" id="stok" class="form-input" value="{{ old('stok', 1) }}" min="0" required>
                @error('stok')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group half">
                <label for="status_kondisi">
                    <span class="material-symbols-outlined">check_circle</span>
                    Status Kondisi
                </label>
                <input type="text" name="status_kondisi" id="status_kondisi" class="form-input" value="{{ old('status_kondisi', 'Baik') }}" placeholder="Baik / Rusak" required>
                @error('status_kondisi')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Deskripsi -->
        <div class="form-group">
            <label for="deskripsi">
                <span class="material-symbols-outlined">description</span>
                Deskripsi (Opsional)
            </label>
            <textarea name="deskripsi" id="deskripsi" class="form-input" rows="3" placeholder="Deskripsi alat...">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Gambar -->
        <div class="form-group">
            <label for="gambar">
                <span class="material-symbols-outlined">image</span>
                Gambar Alat (Opsional)
            </label>
            <input type="file" name="gambar" id="gambar" class="form-input-file" accept="image/*">
            <p class="file-hint">Format: JPG, PNG, JPEG. Maksimal 2MB</p>
            @error('gambar')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Tombol -->
        <div class="form-actions">
            <a href="{{ route('admin.alat.index') }}" class="btn-cancel">
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
<link rel="stylesheet" href="{{ asset('css/alat.css') }}">
@endpush
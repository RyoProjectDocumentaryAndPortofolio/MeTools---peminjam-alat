@extends('layouts.app')

@section('title', 'Edit Alat - Toolsme')
@section('header-title', 'Edit Data Alat')

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
            Edit Data Alat
        </h3>
        <p class="form-subtitle">Perbarui data alat <strong>{{ $alat->nama_alat }}</strong></p>
    </div>

    <form action="{{ route('admin.alat.update', $alat->id) }}" method="POST" enctype="multipart/form-data" class="form-container">
        @csrf
        @method('PUT')

        <!-- Nama Alat -->
        <div class="form-group">
            <label for="nama_alat">
                <span class="material-symbols-outlined">badge</span>
                Nama Alat
            </label>
            <input type="text" name="nama_alat" id="nama_alat" class="form-input" value="{{ old('nama_alat', $alat->nama_alat) }}" required>
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
                    <option value="{{ $kategori->id }}" {{ old('kategori_id', $alat->kategori_id) == $kategori->id ? 'selected' : '' }}>
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
                <input type="number" name="stok" id="stok" class="form-input" value="{{ old('stok', $alat->stok) }}" min="0" required>
                @error('stok')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group half">
                <label for="status_kondisi">
                    <span class="material-symbols-outlined">check_circle</span>
                    Status Kondisi
                </label>
                <input type="text" name="status_kondisi" id="status_kondisi" class="form-input" value="{{ old('status_kondisi', $alat->status_kondisi) }}" required>
                @error('status_kondisi')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Deskripsi -->
        <div class="form-group">
            <label for="deskripsi">
                <span class="material-symbols-outlined">description</span>
                Deskripsi
            </label>
            <textarea name="deskripsi" id="deskripsi" class="form-input" rows="3">{{ old('deskripsi', $alat->deskripsi) }}</textarea>
            @error('deskripsi')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Gambar -->
        <div class="form-group">
            <label for="gambar">
                <span class="material-symbols-outlined">image</span>
                Gambar Alat
                <span class="file-hint">(Kosongkan jika tidak ingin mengubah gambar)</span>
            </label>

            @if($alat->gambar)
                <div class="image-preview">
                    <img src="{{ asset($alat->gambar) }}" alt="Preview" class="preview-thumb">
                </div>
            @endif

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
                Perbarui
            </button>
        </div>
    </form>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/alat.css') }}">
@endpush
@extends('layouts.app')

@section('title', 'Tambah Kategori - Toolsme')
@section('header-title', 'Tambah Kategori Baru')

@section('content')

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

<div class="form-card">
    <div class="form-header">
        <h3>
            <span class="material-symbols-outlined">category</span>
            Tambah Kategori Baru
        </h3>
        <p class="form-subtitle">Masukkan nama kategori alat baru</p>
    </div>

    <form action="{{ route('admin.kategori.store') }}" method="POST" class="form-container">
        @csrf

        <div class="form-group">
            <label for="nama_kategori">
                <span class="material-symbols-outlined">label</span>
                Nama Kategori
            </label>
            <input type="text" name="nama_kategori" id="nama_kategori" class="form-input" value="{{ old('nama_kategori') }}" placeholder="Contoh: Jaringan, Elektronik, Mekanik..." required>
            @error('nama_kategori')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.kategori.index') }}" class="btn-cancel">
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
<link rel="stylesheet" href="{{ asset('css/kategori.css') }}">
@endpush
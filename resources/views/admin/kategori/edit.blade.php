@extends('layouts.app')

@section('title', 'Edit Kategori - Toolsme')
@section('header-title', 'Edit Kategori')

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
            <span class="material-symbols-outlined">edit</span>
            Edit Kategori
        </h3>
        <p class="form-subtitle">Perbarui nama kategori <strong>{{ $kategori->nama_kategori }}</strong></p>
    </div>

    <form action="{{ route('admin.kategori.update', $kategori->id) }}" method="POST" class="form-container">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nama_kategori">
                <span class="material-symbols-outlined">label</span>
                Nama Kategori
            </label>
            <input type="text" name="nama_kategori" id="nama_kategori" class="form-input" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required>
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
                Perbarui
            </button>
        </div>
    </form>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/kategori.css') }}">
@endpush
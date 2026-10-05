@extends('layouts.app')

@section('title', 'Katalog Alat - Toolsme')
@section('header-title', 'Katalog Alat Tersedia')

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

<!-- Form -->
<div class="katalog-card">
    <div class="katalog-header">
        <h3>
            <span class="material-symbols-outlined">shopping_cart</span>
            Ajukan Peminjaman
        </h3>
        <p class="katalog-subtitle">Pilih alat yang ingin dipinjam dan tentukan tanggal pengembalian</p>
    </div>

    <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST" class="katalog-form">
        @csrf

        <!-- Tanggal Kembali -->
        <div class="form-group">
            <label for="tgl_kembali_plan">
                <span class="material-symbols-outlined">calendar_today</span>
                Rencana Tanggal Kembali
            </label>
            <input type="date" name="tgl_kembali_plan" id="tgl_kembali_plan" class="form-input" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
            @error('tgl_kembali_plan')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- GRID CARD ALAT -->
        <div class="alat-grid">
            @forelse($alats as $alat)
                <div class="alat-card" data-stok="{{ $alat->stok }}">
                    <!-- Checkbox -->
                    <label class="alat-checkbox-wrapper">
                        <input type="checkbox" name="alat_id[]" value="{{ $alat->id }}" class="alat-checkbox" data-stok="{{ $alat->stok }}">
                        <span class="checkmark"></span>
                    </label>

                    <!-- Gambar -->
                    <div class="alat-image">
                        @if($alat->gambar)
                            <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}">
                        @else
                            <div class="no-image">
                                <span class="material-symbols-outlined">inventory_2</span>
                            </div>
                        @endif
                    </div>

                    <!-- Info -->
                    <div class="alat-info">
                        <span class="alat-kategori">{{ $alat->kategori->nama_kategori ?? '-' }}</span>
                        <h4 class="alat-nama">{{ $alat->nama_alat }}</h4>
                        <p class="alat-deskripsi">{{ Str::limit($alat->deskripsi ?? 'Tidak ada deskripsi', 60) }}</p>

                        <div class="alat-meta">
                            <span class="alat-stok">
                                <span class="material-symbols-outlined">inventory</span>
                                Stok: {{ $alat->stok }}
                            </span>
                            <span class="kondisi-badge {{ $alat->status_kondisi == 'Baik' ? 'baik' : 'rusak' }}">
                                {{ $alat->status_kondisi }}
                            </span>
                        </div>

                        <!-- Jumlah -->
                        <div class="alat-jumlah">
                            <label>Jumlah:</label>
                            <input type="number" name="jumlah[]" class="form-input jumlah-input" min="1" max="{{ $alat->stok }}" value="1" disabled>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-cell" style="grid-column: 1 / -1;">Belum ada alat yang tersedia.</div>
            @endforelse
        </div>

        <!-- Submit -->
        <div class="form-actions">
            <button type="submit" class="btn-submit">
                <span class="material-symbols-outlined">send</span>
                Ajukan Peminjaman
            </button>
        </div>
    </form>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/katalog.css') }}">
@endpush

@push('scripts')
<script>
    // Enable jumlah input when checkbox checked
    document.querySelectorAll('.alat-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var card = this.closest('.alat-card');
            var jumlahInput = card.querySelector('.jumlah-input');
            if (this.checked) {
                card.classList.add('selected');
                jumlahInput.disabled = false;
                jumlahInput.value = 1;
            } else {
                card.classList.remove('selected');
                jumlahInput.disabled = true;
                jumlahInput.value = 0;
            }
        });
    });

    // Validate jumlah tidak melebihi stok
    document.querySelectorAll('.jumlah-input').forEach(function(input) {
        input.addEventListener('change', function() {
            var max = parseInt(this.getAttribute('max'));
            if (parseInt(this.value) > max) {
                this.value = max;
            }
            if (parseInt(this.value) < 1) {
                this.value = 1;
            }
        });
    });
</script>
@endpush
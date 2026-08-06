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

        <!-- Tabel Alat -->
        <div class="table-wrapper">
            <table class="katalog-table">
                <thead>
                    <tr>
                        <th width="50">Pilih</th>
                        <th>Nama Alat</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Kondisi</th>
                        <th width="120">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alats as $alat)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="alat_id[]" value="{{ $alat->id }}" class="alat-checkbox" data-stok="{{ $alat->stok }}">
                            </td>
                            <td class="alat-name">{{ $alat->nama_alat }}</td>
                            <td>{{ $alat->kategori->nama_kategori ?? '-' }}</td>
                            <td>{{ $alat->stok }}</td>
                            <td>
                                <span class="kondisi-badge {{ $alat->status_kondisi == 'Baik' ? 'baik' : 'rusak' }}">
                                    {{ $alat->status_kondisi }}
                                </span>
                            </td>
                            <td>
                                <input type="number" name="jumlah[]" class="form-input jumlah-input" min="1" max="{{ $alat->stok }}" value="1" disabled>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-cell">Belum ada alat yang tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
            var row = this.closest('tr');
            var jumlahInput = row.querySelector('.jumlah-input');
            if (this.checked) {
                jumlahInput.disabled = false;
                jumlahInput.value = 1;
            } else {
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
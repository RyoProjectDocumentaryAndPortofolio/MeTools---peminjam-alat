@extends('layouts.app')

@section('title', 'Tambah Peminjaman - Toolsme')
@section('header-title', 'Form Tambah Transaksi Peminjaman')

@section('content')

<!-- Alert Error -->
@if(session('error'))
    <div class="alert-error">
        <span class="material-symbols-outlined">error</span>
        {{ session('error') }}
    </div>
@endif

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
            Tambah Transaksi Peminjaman
        </h3>
        <p class="form-subtitle">Isi data peminjaman alat</p>
    </div>

    <form action="{{ route('admin.peminjaman.store') }}" method="POST" class="form-container">
        @csrf

        <!-- Pilih Peminjam -->
        <div class="form-group">
            <label for="user_id">
                <span class="material-symbols-outlined">person</span>
                Pilih Peminjam (User)
            </label>
            <select name="user_id" id="user_id" class="form-input" required>
                <option value="">Pilih User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
            @error('user_id')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Tanggal -->
        <div class="form-row">
            <div class="form-group half">
                <label for="tgl_pinjam">
                    <span class="material-symbols-outlined">calendar_today</span>
                    Tanggal Pinjam
                </label>
                <input type="date" name="tgl_pinjam" id="tgl_pinjam" class="form-input" value="{{ old('tgl_pinjam', date('Y-m-d')) }}" required>
                @error('tgl_pinjam')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group half">
                <label for="tgl_kembali_plan">
                    <span class="material-symbols-outlined">calendar_month</span>
                    Rencana Tanggal Kembali
                </label>
                <input type="date" name="tgl_kembali_plan" id="tgl_kembali_plan" class="form-input" value="{{ old('tgl_kembali_plan', date('Y-m-d', strtotime('+3 days'))) }}" required>
                @error('tgl_kembali_plan')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Daftar Alat -->
        <div class="form-group">
            <label>
                <span class="material-symbols-outlined">inventory_2</span>
                Daftar Alat yang Dipinjam
            </label>
            <div id="alat-container" class="alat-container">
                <div class="alat-row">
                    <select name="alat_id[]" class="form-input alat-select" required>
                        <option value="">Pilih Alat</option>
                        @foreach($alats as $alat)
                            <option value="{{ $alat->id }}" data-stok="{{ $alat->stok }}">
                                {{ $alat->nama_alat }} (Stok: {{ $alat->stok }})
                            </option>
                        @endforeach
                    </select>
                    <input type="number" name="jumlah[]" class="form-input jumlah-input" value="1" min="1" placeholder="Jumlah" required>
                    <button type="button" class="btn-remove" onclick="removeRow(this)" title="Hapus Baris">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>
            <button type="button" class="btn-add-row" onclick="addRow()">
                <span class="material-symbols-outlined">add</span>
                Tambah Alat
            </button>
            @error('alat_id.*')
                <span class="form-error">{{ $message }}</span>
            @enderror
            @error('jumlah.*')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Tombol -->
        <div class="form-actions">
            <a href="{{ route('admin.peminjaman.index') }}" class="btn-cancel">
                <span class="material-symbols-outlined">close</span>
                Batal
            </a>
            <button type="submit" class="btn-submit">
                <span class="material-symbols-outlined">save</span>
                Simpan Peminjaman
            </button>
        </div>
    </form>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/peminjaman.css') }}">
@endpush

@push('scripts')
<script>
    function addRow() {
        const container = document.getElementById('alat-container');
        const firstRow = container.querySelector('.alat-row');
        const newRow = firstRow.cloneNode(true);
        newRow.querySelector('select').value = '';
        newRow.querySelector('.jumlah-input').value = '1';
        container.appendChild(newRow);
    }

    function removeRow(button) {
        const rows = document.querySelectorAll('.alat-row');
        if (rows.length > 1) {
            button.closest('.alat-row').remove();
        } else {
            alert('Minimal harus ada 1 alat yang dipilih.');
        }
    }

    // Validasi stok saat submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('.alat-row');
        let valid = true;

        rows.forEach(function(row) {
            const select = row.querySelector('.alat-select');
            const jumlah = row.querySelector('.jumlah-input');
            const selectedOption = select.options[select.selectedIndex];

            if (select.value && selectedOption) {
                const stok = parseInt(selectedOption.getAttribute('data-stok'));
                const jumlahValue = parseInt(jumlah.value);

                if (jumlahValue > stok) {
                    alert('Stok tidak mencukupi untuk alat: ' + selectedOption.text);
                    valid = false;
                }
            }
        });

        if (!valid) {
            e.preventDefault();
        }
    });
</script>
@endpush
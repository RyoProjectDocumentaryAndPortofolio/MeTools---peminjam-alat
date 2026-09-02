@extends('layouts.app')

@section('title', 'Tambah Pengembalian - Toolsme')
@section('header-title', 'Form Pengembalian Alat')

@section('content')

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

<div class="form-card">
    <div class="form-header">
        <h3>
            <span class="material-symbols-outlined">swap_horiz</span>
            Form Pengembalian Alat
        </h3>
        <p class="form-subtitle">Pilih peminjaman yang akan dikembalikan</p>
    </div>

    <form action="{{ route('admin.pengembalian.store') }}" method="POST" class="form-container">
        @csrf

        <!-- Pilih Peminjaman -->
        <div class="form-group">
            <label for="peminjaman_id">
                <span class="material-symbols-outlined">assignment</span>
                Pilih Peminjaman (Status: Dipinjam)
            </label>
            <select name="peminjaman_id" id="peminjaman_id" class="form-input" required>
                <option value="">-- Pilih Peminjaman --</option>
                @foreach($peminjaman as $item)
                    <option value="{{ $item->id }}">
                        #{{ $item->id }} - {{ $item->user->name ?? 'Unknown' }}
                        ({{ $item->tgl_pinjam }} → {{ $item->tgl_kembali_plan }})
                    </option>
                @endforeach
            </select>
            @error('peminjaman_id')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Info Peminjaman (Dynamic) -->
        <div id="info-peminjaman" class="info-box" style="display:none;">
            <div class="info-row">
                <span class="info-label">Peminjam:</span>
                <span id="info-nama" class="info-value">-</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Pinjam:</span>
                <span id="info-tgl-pinjam" class="info-value">-</span>
            </div>
            <div class="info-row">
                <span class="info-label">Rencana Kembali:</span>
                <span id="info-tgl-kembali-plan" class="info-value">-</span>
            </div>
            <div class="info-row">
                <span class="info-label">Alat:</span>
                <span id="info-alat" class="info-value">-</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Kembali (Sekarang):</span>
                <span id="info-tgl-kembali" class="info-value">{{ date('d-m-Y') }}</span>
            </div>
        </div>

        <!-- Kondisi Kembali -->
        <div class="form-group">
            <label for="kondisi_kembali">
                <span class="material-symbols-outlined">check_circle</span>
                Kondisi Kembali
            </label>
            <select name="kondisi_kembali" id="kondisi_kembali" class="form-input" required>
                <option value="">-- Pilih Kondisi --</option>
                <option value="Baik">Baik</option>
                <option value="Rusak">Rusak</option>
                <option value="Perbaikan">Perbaikan</option>
            </select>
            @error('kondisi_kembali')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Denda Kerusakan (Opsional) -->
        <div class="form-group">
            <label for="denda_kerusakan">
                <span class="material-symbols-outlined">money</span>
                Denda Kerusakan (Opsional)
            </label>
            <input type="number" name="denda_kerusakan" id="denda_kerusakan" class="form-input" value="{{ old('denda_kerusakan', 0) }}" min="0" placeholder="0">
            <p class="file-hint">Isi jika ada kerusakan atau perbaikan</p>
            @error('denda_kerusakan')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Info Denda Otomatis -->
        <div id="info-denda" class="info-box" style="display:none;">
            <div class="info-row">
                <span class="info-label">📅 Denda Terlambat:</span>
                <span id="info-denda-terlambat" class="info-value">Rp 0</span>
            </div>
            <div class="info-row">
                <span class="info-label">💰 Denda Kerusakan:</span>
                <span id="info-denda-kerusakan" class="info-value">Rp 0</span>
            </div>
            <div class="info-row total">
                <span class="info-label">💎 Total Denda:</span>
                <span id="info-total-denda" class="info-value">Rp 0</span>
            </div>
        </div>

        <!-- Tombol -->
        <div class="form-actions">
            <a href="{{ route('admin.pengembalian.index') }}" class="btn-cancel">
                <span class="material-symbols-outlined">close</span>
                Batal
            </a>
            <button type="submit" class="btn-submit">
                <span class="material-symbols-outlined">save</span>
                Proses Pengembalian
            </button>
        </div>

        <!-- Hidden field untuk tanggal kembali (otomatis diisi server) -->
        <input type="hidden" name="tgl_kembali" value="{{ date('Y-m-d') }}">

    </form>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pengembalian.css') }}">
@endpush

@push('scripts')
<script>
    // Data peminjaman dari server
    const peminjamanData = @json($peminjaman);

    document.getElementById('peminjaman_id').addEventListener('change', function() {
        const id = parseInt(this.value);
        const infoBox = document.getElementById('info-peminjaman');
        const dendaBox = document.getElementById('info-denda');

        if (!id) {
            infoBox.style.display = 'none';
            dendaBox.style.display = 'none';
            return;
        }

        const data = peminjamanData.find(item => item.id === id);
        if (!data) return;

        // Tampilkan info peminjaman
        infoBox.style.display = 'block';
        document.getElementById('info-nama').textContent = data.user.name || 'Unknown';
        document.getElementById('info-tgl-pinjam').textContent = data.tgl_pinjam;
        document.getElementById('info-tgl-kembali-plan').textContent = data.tgl_kembali_plan;

        const alatList = data.detail_pinjam.map(d => d.alat.nama_alat + ' (' + d.jumlah + ' pcs)').join(', ');
        document.getElementById('info-alat').textContent = alatList || '-';

        // ============================================
        // KALKULASI DENDA TERLAMBAT (REAL-TIME)
        // ============================================
        const tglKembaliPlan = new Date(data.tgl_kembali_plan);
        const tglKembali = new Date(); // Hari ini (tanggal pengembalian)
        tglKembali.setHours(0, 0, 0, 0);

        // Selisih hari
        const selisihHari = Math.ceil((tglKembali - tglKembaliPlan) / (1000 * 60 * 60 * 24));

        let dendaTerlambat = 0;
        let hariTelat = 0;

        if (selisihHari > 0) {
            hariTelat = selisihHari;
            dendaTerlambat = selisihHari * 5000;
        }

        const dendaKerusakan = parseInt(document.getElementById('denda_kerusakan').value) || 0;
        const totalDenda = dendaTerlambat + dendaKerusakan;

        // Tampilkan hasil kalkulasi
        document.getElementById('info-denda-terlambat').textContent = 'Rp ' + dendaTerlambat.toLocaleString('id-ID');
        document.getElementById('info-denda-kerusakan').textContent = 'Rp ' + dendaKerusakan.toLocaleString('id-ID');
        document.getElementById('info-total-denda').textContent = 'Rp ' + totalDenda.toLocaleString('id-ID');

        // Update info tanggal kembali
        const now = new Date();
        const tanggalKembali = now.getDate().toString().padStart(2, '0') + '-' +
                              (now.getMonth() + 1).toString().padStart(2, '0') + '-' +
                              now.getFullYear();
        document.getElementById('info-tgl-kembali').textContent = tanggalKembali;

        dendaBox.style.display = 'block';
    });

    // Update saat denda kerusakan berubah
    document.getElementById('denda_kerusakan').addEventListener('input', function() {
        document.getElementById('peminjaman_id').dispatchEvent(new Event('change'));
    });
</script>
@endpush
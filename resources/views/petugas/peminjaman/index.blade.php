@extends('layouts.app')

@section('title', 'Kelola Peminjaman - Toolsme')
@section('header-title', 'Daftar Pengajuan & Transaksi Peminjaman')

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

<!-- Card -->
<div class="peminjaman-card">
    <div class="peminjaman-header">
        <h3>
            <span class="material-symbols-outlined">assignment</span>
            Daftar Peminjaman
        </h3>
        <p class="peminjaman-subtitle">Kelola pengajuan dan transaksi peminjaman alat</p>
    </div>

    <!-- Filter & Sort -->
    <form action="{{ route('petugas.peminjaman.index') }}" method="GET" class="filter-bar">
        <div class="filter-group">
            <label for="filter">
                <span class="material-symbols-outlined">filter_list</span>
                Filter
            </label>
            <select name="filter" id="filter" class="filter-select" onchange="this.form.submit()">
                <option value="semua"                {{ $filter == 'semua' ? 'selected' : '' }}>Semua</option>
                <option value="diajukan"             {{ $filter == 'diajukan' ? 'selected' : '' }}>Diajukan (Belum Diverifikasi)</option>
                <option value="dipinjam"             {{ $filter == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="menunggu_verifikasi"  {{ $filter == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi Kembali</option>
                <option value="telat"                {{ $filter == 'telat' ? 'selected' : '' }}>Telat</option>
                <option value="dikembalikan"         {{ $filter == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                <option value="ditolak"              {{ $filter == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="sort">
                <span class="material-symbols-outlined">sort</span>
                Urutkan
            </label>
            <select name="sort" id="sort" class="filter-select" onchange="this.form.submit()">
                <option value="terbaru" {{ $sort == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                <option value="terlama" {{ $sort == 'terlama' ? 'selected' : '' }}>Terlama (FCFS)</option>
            </select>
        </div>

        <a href="{{ route('petugas.peminjaman.index') }}" class="btn-reset-filter">
            <span class="material-symbols-outlined">refresh</span>
            Reset
        </a>
    </form>

    <!-- Table -->
    <div class="table-wrapper">
        <table class="peminjaman-table">
            <thead>
                <tr>
                    <th>Peminjam</th>
                    <th>Tanggal Pinjam</th>
                    <th>Rencana Kembali</th>
                    <th>Status</th>
                    <th>Alat yang Dipinjam</th>
                    <th width="260">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjamans as $item)
                    <tr>
                        <td class="peminjam-name">{{ $item->user->name ?? 'Unknown' }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d-m-Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->format('d-m-Y') }}</td>
                        <td>
                            <span class="status-badge {{ $item->status }}">
                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                            </span>
                            @if($item->status === 'ditolak' && $item->alasan_penolakan)
                                <div class="alasan-tolak" title="{{ $item->alasan_penolakan }}">
                                    <span class="material-symbols-outlined">info</span>
                                    {{ Str::limit($item->alasan_penolakan, 30) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <ul class="alat-list">
                                @foreach($item->detailPinjam as $detail)
                                    <li>{{ $detail->alat->nama_alat ?? 'Alat' }} ({{ $detail->jumlah }} pcs)</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <div class="action-buttons">
                                @if($item->status == 'diajukan')
                                    <!-- Setujui -->
                                    <form action="{{ route('petugas.peminjaman.setujui', $item->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn-approve" onclick="return confirm('Setujui peminjaman ini?')">
                                            <span class="material-symbols-outlined">check_circle</span>
                                            Setujui
                                        </button>
                                    </form>

                                    <!-- Tolak -->
                                    <button type="button" class="btn-reject"
                                        onclick="openTolakModal({{ $item->id }}, '{{ $item->user->name }}')">
                                        <span class="material-symbols-outlined">cancel</span>
                                        Tolak
                                    </button>

                                @elseif($item->status == 'dipinjam')
                                    <form action="{{ route('petugas.pengembalian.proses', $item->id) }}" method="POST" onsubmit="return confirm('Proses pengembalian alat ini?')">
                                        @csrf
                                        <input type="hidden" name="kondisi_kembali" value="Baik">
                                        <input type="hidden" name="denda_kerusakan" value="0">
                                        <button type="submit" class="btn-return">
                                            <span class="material-symbols-outlined">swap_horiz</span>
                                            Proses Kembali
                                        </button>
                                    </form>

                                @elseif($item->status == 'menunggu_verifikasi')
                                    <form action="{{ route('petugas.verifikasi.kembali', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-verify" onclick="return confirm('Verifikasi pengembalian alat ini?')">
                                            <span class="material-symbols-outlined">verified</span>
                                            Verifikasi
                                        </button>
                                    </form>

                                @elseif($item->status == 'ditolak')
                                    <span class="text-muted" title="{{ $item->alasan_penolakan }}">
                                        Ditolak oleh {{ $item->ditolakOleh->name ?? '-' }}
                                    </span>

                                @else
                                    <span class="text-muted">Selesai</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-cell">
                            Tidak ada data peminjaman untuk filter "{{ $filter }}".
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tolak -->
<div id="tolakModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div class="modal-header">
            <h3>
                <span class="material-symbols-outlined">cancel</span>
                Tolak Peminjaman
            </h3>
            <button type="button" class="modal-close" onclick="closeTolakModal()">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="tolakForm" method="POST" class="modal-body">
            @csrf
            <p class="modal-desc">
                Tolak peminjaman dari <strong id="namaPeminjam"></strong>?
            </p>

            <label for="alasan_penolakan">Alasan Penolakan <span class="required">*</span></label>
            <textarea name="alasan_penolakan" id="alasan_penolakan" rows="4"
                placeholder="Contoh: Stok alat habis, silakan ajukan lain kali."
                minlength="10" maxlength="500" required></textarea>
            <small class="form-hint">Minimal 10 karakter, maksimal 500 karakter.</small>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeTolakModal()">Batal</button>
                <button type="submit" class="btn-reject-confirm">
                    <span class="material-symbols-outlined">cancel</span>
                    Tolak Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/petugas.css') }}">
@endpush

@push('scripts')
<script>
function openTolakModal(id, nama) {
    document.getElementById('tolakForm').action = `/petugas/peminjaman/${id}/tolak`;
    document.getElementById('namaPeminjam').textContent = nama;
    document.getElementById('alasan_penolakan').value = '';
    document.getElementById('tolakModal').style.display = 'flex';
}

function closeTolakModal() {
    document.getElementById('tolakModal').style.display = 'none';
}

// Tutup modal kalau klik overlay
document.getElementById('tolakModal').addEventListener('click', function(e) {
    if (e.target === this) closeTolakModal();
});
</script>
@endpush
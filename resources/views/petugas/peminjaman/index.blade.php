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

<!-- Card -->
<div class="peminjaman-card">
    <div class="peminjaman-header">
        <h3>
            <span class="material-symbols-outlined">assignment</span>
            Daftar Peminjaman
        </h3>
        <p class="peminjaman-subtitle">Kelola pengajuan dan transaksi peminjaman alat</p>
    </div>

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
                    <th width="220">Aksi</th>
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
                                {{ ucfirst($item->status) }}
                            </span>
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
                                    <!-- Setujui Peminjaman -->
                                    <form action="{{ route('petugas.peminjaman.setujui', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-approve" onclick="return confirm('Setujui peminjaman ini?')">
                                            <span class="material-symbols-outlined">check_circle</span>
                                            Setujui
                                        </button>
                                    </form>
                                @elseif($item->status == 'dipinjam')
                                    <!-- Proses Pengembalian -->
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
                                    <!-- Verifikasi Pengembalian -->
                                    <form action="{{ route('petugas.verifikasi.kembali', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-verify" onclick="return confirm('Verifikasi pengembalian alat ini?')">
                                            <span class="material-symbols-outlined">verified</span>
                                            Verifikasi
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">Selesai</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-cell">Belum ada data peminjaman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/petugas.css') }}">
@endpush
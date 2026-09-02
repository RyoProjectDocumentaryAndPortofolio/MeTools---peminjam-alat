@extends('layouts.app')

@section('title', 'Pemantauan Pengembalian - Toolsme')
@section('header-title', 'Pemantauan Pengembalian Alat')

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

<div class="pengembalian-card">
    <div class="pengembalian-header">
        <h3>
            <span class="material-symbols-outlined">swap_horiz</span>
            Daftar Peminjaman Aktif
        </h3>
        <p class="pengembalian-subtitle">Kelola peminjaman yang sedang berlangsung</p>
    </div>

    <div class="table-wrapper">
        <table class="pengembalian-table">
            <thead>
                <tr>
                    <th>Peminjam</th>
                    <th>Tanggal Pinjam</th>
                    <th>Rencana Kembali</th>
                    <th>Status</th>
                    <th>Alat Dipinjam</th>
                    <th width="200">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjaman as $item)
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
                            <!-- Tombol Aksi -->
                            @if($item->status == 'menunggu_verifikasi')
                                <!-- Verifikasi Pengembalian -->
                                <form action="{{ route('petugas.verifikasi.kembali', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-verify" onclick="return confirm('Verifikasi pengembalian alat ini?')">
                                        <span class="material-symbols-outlined">verified</span>
                                        Verifikasi
                                    </button>
                                </form>
                            @elseif($item->status == 'dipinjam')
                                <!-- Proses Pengembalian -->
                                <form action="{{ route('petugas.pengembalian.proses', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="kondisi_kembali" value="Baik">
                                    <input type="hidden" name="denda_kerusakan" value="0">
                                    <button type="submit" class="btn-return" onclick="return confirm('Proses pengembalian alat ini?')">
                                        <span class="material-symbols-outlined">check_circle</span>
                                        Proses Kembali
                                    </button>
                                </form>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-cell">Belum ada peminjaman yang sedang berlangsung.</td>
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
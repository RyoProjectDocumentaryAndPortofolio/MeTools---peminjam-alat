@extends('layouts.app')

@section('title', 'Riwayat Peminjaman - Toolsme')
@section('header-title', 'Riwayat Peminjaman Saya')

@section('content')

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

<div class="riwayat-card">
    <div class="riwayat-header">
        <h3>
            <span class="material-symbols-outlined">history</span>
            Riwayat Peminjaman Saya
        </h3>
        <p class="riwayat-subtitle">Daftar semua peminjaman yang pernah Anda lakukan</p>
    </div>

    <div class="table-wrapper">
        <table class="riwayat-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal Pinjam</th>
                    <th>Rencana Kembali</th>
                    <th>Status</th>
                    <th>Alat Dipinjam</th>
                    <th>Denda</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjaman as $item)
                    <tr>
                        <td>#{{ $item->id }}</td>
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
                            @php
                                $denda = 0;
                                if ($item->pengembalian && $item->pengembalian->total_denda > 0) {
                                    $denda = $item->pengembalian->total_denda;
                                }
                            @endphp
                            @if($denda > 0)
                                <span class="denda-amount">Rp {{ number_format($denda, 0, ',', '.') }}</span>
                            @else
                                <span class="denda-nol">-</span>
                            @endif
                        </td>
                        <td>
                            @if($item->status == 'dipinjam')
                                <form action="{{ route('peminjam.kembalikan', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-kembalikan" onclick="return confirm('Yakin ingin mengembalikan alat ini?')">
                                        <span class="material-symbols-outlined">undo</span>
                                        Kembalikan
                                    </button>
                                </form>
                            @elseif($item->status == 'menunggu_verifikasi')
                                <span class="badge-waiting">⏳ Menunggu Verifikasi</span>
                            @elseif($item->status == 'diajukan')
                                <span class="badge-diajukan">📋 Diajukan</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-cell">Belum ada riwayat peminjaman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/peminjam.css') }}">
@endpush
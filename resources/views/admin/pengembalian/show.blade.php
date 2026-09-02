@extends('layouts.app')

@section('title', 'Detail Pengembalian - Toolsme')
@section('header-title', 'Detail Transaksi Pengembalian')

@section('content')

<div class="detail-card">
    <div class="detail-header">
        <h3>
            <span class="material-symbols-outlined">receipt_long</span>
            Detail Pengembalian #{{ $pengembalian->id }}
        </h3>
        <a href="{{ route('admin.pengembalian.index') }}" class="btn-back">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali
        </a>
    </div>

    <div class="detail-body">
        <div class="detail-grid">
            <div class="detail-item">
                <label>ID Peminjaman</label>
                <p>#{{ $pengembalian->peminjaman_id }}</p>
            </div>
            <div class="detail-item">
                <label>Peminjam</label>
                <p>{{ $pengembalian->peminjaman->user->name ?? '-' }}</p>
            </div>
            <div class="detail-item">
                <label>Tanggal Kembali</label>
                <p>{{ $pengembalian->tgl_kembali->format('d-m-Y') }}</p>
            </div>
            <div class="detail-item">
                <label>Kondisi Kembali</label>
                <p>{{ $pengembalian->kondisi_kembali }}</p>
            </div>
            <div class="detail-item">
                <label>Denda Keterlambatan</label>
                <p>Rp {{ number_format($pengembalian->denda_terlambat, 0, ',', '.') }}</p>
            </div>
            <div class="detail-item">
                <label>Denda Kerusakan</label>
                <p>Rp {{ number_format($pengembalian->denda_kerusakan, 0, ',', '.') }}</p>
            </div>
            <div class="detail-item total">
                <label>Total Denda</label>
                <p>Rp {{ number_format($pengembalian->total_denda, 0, ',', '.') }}</p>
            </div>
            <div class="detail-item">
                <label>Petugas</label>
                <p>{{ $pengembalian->petugas->name ?? '-' }}</p>
            </div>
            <div class="detail-item">
                <label>Dibuat</label>
                <p>{{ $pengembalian->created_at->format('d-m-Y H:i') }}</p>
            </div>
        </div>

        @if($pengembalian->peminjaman->detailPinjam->count() > 0)
            <div class="detail-alat">
                <h4>Alat yang Dikembalikan</h4>
                <ul>
                    @foreach($pengembalian->peminjaman->detailPinjam as $detail)
                        <li>{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }} ({{ $detail->jumlah }} pcs)</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pengembalian.css') }}">
@endpush
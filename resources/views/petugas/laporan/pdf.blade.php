<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman Alat</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            padding: 20px;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #b89a5b;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-family: 'Times New Roman', serif;
            font-size: 28px;
            color: #b89a5b;
            letter-spacing: 4px;
            text-transform: uppercase;
        }
        .header h1 span {
            color: #d4af37;
        }
        .header p {
            color: #6a5f3a;
            font-size: 14px;
            margin-top: 4px;
        }
        .info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 12px;
            color: #4a4737;
        }
        .info .left span {
            font-weight: bold;
        }
        .info .right span {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 11px;
        }
        table thead th {
            background: #2a241e;
            color: #d4af37;
            padding: 8px 6px;
            text-align: left;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 1px;
            border: 1px solid #4a4737;
        }
        table tbody td {
            padding: 6px 6px;
            border: 1px solid #4a4737;
            color: #1f1b16;
        }
        table tbody tr:nth-child(even) {
            background: #f5f0e8;
        }
        table tbody tr:nth-child(odd) {
            background: #fff;
        }
        .footer {
            margin-top: 20px;
            border-top: 1px solid #4a4737;
            padding-top: 15px;
            text-align: center;
            font-size: 10px;
            color: #6a5f3a;
        }
        .footer .signature {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }
        .footer .signature .line {
            width: 200px;
            border-top: 1px solid #1f1b16;
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #1f1b16;
        }
        .total-denda {
            font-weight: bold;
            color: #b53b2a;
            font-size: 13px;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge.dikembalikan {
            background: #a5d6a7;
            color: #1f1b16;
        }
        .badge.telat {
            background: #ff8a80;
            color: #1f1b16;
        }
        .badge.dipinjam {
            background: #4facfe;
            color: #fff;
        }
        .badge.diajukan {
            background: #ffc107;
            color: #1f1b16;
        }
        @page {
            margin: 15px;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <h1><span>⚙</span> TOOLSME <span>⚙</span></h1>
        <p>Laporan Transaksi Peminjaman Alat</p>
    </div>

    <!-- INFO -->
    <div class="info">
        <div class="left">
            <span>Tanggal Cetak:</span> {{ $tanggal }}
        </div>
        <div class="right">
            <span>Petugas:</span> {{ $petugas }}
        </div>
    </div>
    <div class="info">
        <div class="left">
            <span>Total Transaksi:</span> {{ $total }}
        </div>
        <div class="right">
            <span>Total Denda:</span> <span class="total-denda">Rp {{ number_format($totalDenda, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- TABLE -->
    <table>
        <thead>
            <tr>
                <th width="40">No</th>
                <th width="120">Peminjam</th>
                <th width="100">Tgl Pinjam</th>
                <th width="100">Rencana Kembali</th>
                <th width="100">Tgl Kembali</th>
                <th width="80">Status</th>
                <th width="120">Alat</th>
                <th width="70">Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjaman as $index => $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->user->name ?? 'Unknown' }}</td>
                    <td>{{ $item->tgl_pinjam }}</td>
                    <td>{{ $item->tgl_kembali_plan }}</td>
                    <td>
                        @if($item->pengembalian)
                            {{ $item->pengembalian->tgl_kembali }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $item->status }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td>
                        @foreach($item->detailPinjam as $detail)
                            {{ $detail->alat->nama_alat ?? 'Alat' }} ({{ $detail->jumlah }} pcs)
                            @if(!$loop->last), @endif
                        @endforeach
                    </td>
                    <td class="text-center">
                        @if($item->pengembalian)
                            Rp {{ number_format($item->pengembalian->total_denda ?? 0, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding:20px;color:#6a5f3a;">
                        Belum ada data peminjaman yang selesai.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <p>Dicetak pada: {{ $tanggal }}</p>
        <p>&copy; {{ date('Y') }} Toolsme - Sistem Peminjaman Alat</p>
        <div class="signature">
            <div class="line">
                {{ $petugas }}
                <br>
                <span style="font-size:10px;color:#6a5f3a;">Petugas</span>
            </div>
        </div>
    </div>

</body>
</html>
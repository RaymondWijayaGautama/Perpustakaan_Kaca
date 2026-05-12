<!DOCTYPE html>
<html>
<head>
    <title>Laporan Peminjaman Berdasarkan Kelas - {{ $periodeLabel }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 5px 0 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
        .footer { margin-top: 50px; float: right; text-align: center; width: 200px; }
        .total-row { font-weight: bold; background-color: #e5f6fd; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PERPUSTAKAAN KACA - SMK BODA</h2>
        <p>Laporan Peminjaman Buku Berdasarkan Kelas</p>
        <p>Periode: <strong>{{ $periodeLabel }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="60%">Nama Kelas</th>
                <th width="30%">Jumlah Peminjaman</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataLaporan as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>Kelas {{ $item->kelas }}</td>
                <td class="text-center">{{ $item->total }} Buku</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Tidak ada data peminjaman pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="text-center">TOTAL PEMINJAMAN KESELURUHAN</td>
                <td class="text-center">{{ $totalSemua }} Buku</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Boyolali, {{ date('d F Y') }}</p>
        <p>Pustakawan SMK BODA,</p>
        <br><br><br>
        <p><strong>( ................................. )</strong></p>
    </div>
</body>
</html>
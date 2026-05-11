<!DOCTYPE html>
<html>
<head>
    <title>Laporan Statistik Kunjungan {{ $tahun }}</title>
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
        .total-row { font-weight: bold; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PERPUSTAKAAN KACA - SMK BODA</h2>
        <p>Laporan Statistik Kunjungan Perpustakaan Bulanan</p>
        <p>Tahun Kalender: <strong>{{ $tahun }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="60%">Bulan</th>
                <th width="30%">Jumlah Kunjungan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataLaporan as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item['bulan'] }}</td>
                <td class="text-center">{{ $item['jumlah'] }} Siswa</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="text-center">TOTAL KUNJUNGAN SETAHUN</td>
                <td class="text-center">{{ $totalSetahun }} Siswa</td>
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
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Times New Roman', serif; }
        .header { background-color: #6B5B49; color: white; padding: 15px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 10px; text-align: center; }
        th { background-color: #A48366; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin:0;">STATISTIK TAHUNAN PERPUSTAKAAN</h2>
        <p style="margin:5px 0 0 0;">Laporan Kunjungan Bulanan - Tahun {{ $tahun }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Bulan</th>
                <th>Total Pengunjung</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporanKunjungan as $index => $data)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="text-align: left; text-transform: capitalize;">{{ $data->bulan }}</td>
                <td>{{ $data->total_kunjungan }} Orang</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
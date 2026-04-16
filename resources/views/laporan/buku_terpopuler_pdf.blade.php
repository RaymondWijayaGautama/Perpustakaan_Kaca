<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Times New Roman', Times, serif; }
        .text-center { text-align: center; }
        .header-title {
            background-color: #6B5B49; color: white; padding: 10px;
            display: inline-block; border-radius: 5px; font-weight: bold; margin-bottom: 20px;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 2px solid #000; padding: 10px; text-align: center; }
        th { background-color: #A48366; color: white; text-transform: uppercase; }
        tr:nth-child(even) { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="text-center">
        <div class="header-title">LAPORAN BUKU TERPOPULER</div>
        <h3>Daftar Buku Paling Sering Dipinjam Tahun {{ $tahun }}</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="50%">Judul Koleksi</th>
                <th width="20%">ISBN</th>
                <th width="20%">Total Pinjam</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporanBuku as $index => $data)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="text-align: left;">{{ $data->judul_koleksi }}</td>
                <td>{{ $data->ISBN }}</td>
                <td>{{ $data->total_dipinjam }} Kali</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top: 30px; text-align: right; font-size: 12px;">
        Dicetak pada: {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
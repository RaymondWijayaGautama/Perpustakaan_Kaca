<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Times New Roman', Times, serif; }
        .text-center { text-align: center; }
        
        .header-title {
            background-color: #6B5B49;
            color: white;
            padding: 10px;
            display: inline-block;
            border-radius: 5px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 2px solid #000;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #A48366;
            color: white;
            text-transform: uppercase;
        }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>

    <div class="text-center">
        <div class="header-title">STATISTIK KATEGORI PERPUSTAKAAN</div>
        <h3>Laporan Kategori Buku Paling Sering Dipinjam</h3>
        <p style="margin-top: -10px;">Tahun Rekapitulasi: {{ $tahun }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Peringkat</th>
                <th width="50%">Kategori Buku</th>
                <th width="35%">Total Dipinjam</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporanKategori as $index => $data)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="text-align: left; text-transform: capitalize; padding-left: 20px;">{{ $data->deskripsi }}</td>
                <td>{{ $data->total_dipinjam }} Kali</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; font-size: 12px;">
        Dicetak otomatis pada: {{ date('d/m/Y H:i') }}
    </div>

</body>
</html>
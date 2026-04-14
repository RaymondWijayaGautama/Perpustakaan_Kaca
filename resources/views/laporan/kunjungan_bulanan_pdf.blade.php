<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Times New Roman', Times, serif; }
        .text-center { text-align: center; }
        
        /* Gaya Judul Laporan */
        .header-title {
            background-color: #6B5B49; /* Cokelat Tua */
            color: white;
            padding: 10px;
            display: inline-block;
            border-radius: 5px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Gaya Tabel sesuai template */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 2px solid #000; /* Border Hitam Tebal */
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #A48366; /* Cokelat Terang */
            color: white;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

    <div class="text-center">
        <div class="header-title">STATISTIK KUNJUNGAN PERPUSTAKAAN</div>
        <h3>Laporan Jumlah Kunjungan Per Bulan</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">No</th>
                <th width="45%">Bulan</th>
                <th width="40%">Total Kunjungan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporanKunjungan as $index => $data)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="text-align: left; text-transform: capitalize;">{{ $data->bulan }}</td>
                <td>{{ $data->total_kunjungan }} Pengunjung</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; font-size: 12px;">
        Dicetak pada: {{ date('d/m/Y H:i') }}
    </div>

</body>
</html>
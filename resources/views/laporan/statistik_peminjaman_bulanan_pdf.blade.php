<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 28px 36px; }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #171717;
            background: #fff;
        }
        .label {
            display: inline-block;
            background: #666450;
            color: #fff;
            border-radius: 5px;
            padding: 5px 10px;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
            line-height: 1;
        }
        h2 {
            margin: 8px 0 18px 30px;
            font-size: 18px;
            letter-spacing: 2px;
        }
        table {
            width: 82%;
            margin: 0 auto;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th,
        td {
            border: 2px solid #171717;
            padding: 15px 12px;
            text-align: center;
            font-size: 16px;
            height: 24px;
        }
        th {
            background: #9A7952;
            color: #fff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="label">Statistik Peminjaman Buku</div>
    <h2>1. Jumlah Peminjaman Bulanan</h2>

    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Jumlah Peminjaman</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    <td>{{ $item['nama_bulan'] }}</td>
                    <td>{{ number_format($item['jumlah_peminjaman'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

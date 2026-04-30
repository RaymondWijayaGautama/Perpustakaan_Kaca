<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        table { border-collapse: collapse; width: 100%; font-family: 'Times New Roman', Times, serif; font-size: 11px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; vertical-align: middle; }
        th { font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th rowspan="2" width="5%">No. Induk</th>
                <th rowspan="2" width="8%">No. Kode</th>
                <th rowspan="2" width="12%">Pengarang,<br><i>Alih Bahasa, Penerjemah</i></th>
                <th rowspan="2" width="15%">Judul,<br><br><i>Judul Asli</i></th>
                <th rowspan="2" width="12%">Penerbit,<br>Kota, Tahun, Cet.</th>
                <th rowspan="2" width="8%">Tanggal<br>diterima</th>
                <th rowspan="2" width="5%">Jumlah<br>Eksemplar</th>
                <th rowspan="2" width="8%">Jumlah<br>Halaman<br>Romawi + Angka</th>
                <th>Tinggi/</th>
                <th rowspan="2" width="8%">Bibliografi</th>
                <th rowspan="2" width="5%">Indeks<br>Awal + Akhir</th>
                <th rowspan="2" width="8%">ISBN</th>
                <th rowspan="2" width="10%">Keterangan</th>
            </tr>
            <tr>
                <th>Ukuran<br>Buku<br>cm</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataBuku as $buku)
            <tr>
                <td>{{ $buku->no_induk ?? '-' }}</td>
                <td>{{ $buku->no_kode ?? '-' }}</td>
                <td style="text-align: left;">{{ $buku->pengarang ?? '-' }}</td>
                <td style="text-align: left;">{{ $buku->judul_koleksi ?? '-' }}</td>
                <td>
                    {{ $buku->penerbit ?? '-' }}<br>
                    {{ $buku->tahun ?? '-' }}
                </td>
                <td>{{ $buku->tgl_diterima ? date('d-m-Y', strtotime($buku->tgl_diterima)) : '-' }}</td>
                <td>{{ $buku->jumlah_eksemplar ?? '0' }}</td>
                <td>{{ $buku->jumlah_halaman ?? '-' }}</td>
                <td>{{ $buku->ukuran_buku ?? '-' }}</td>
                <td>{{ $buku->bibliografi ?? '-' }}</td>
                <td>{{ $buku->indeks ?? '-' }}</td>
                <td>{{ $buku->isbn ?? '-' }}</td>
                <td>{{ $buku->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
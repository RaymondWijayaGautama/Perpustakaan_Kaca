<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Buku KACA</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th>ISBN</th>
                <th>Judul Buku</th>
                <th>Pengarang</th>
                <th>Penerbit</th>
                <th>Tahun</th>
                <th>Kategori</th>
                <th>Rak</th>
                <th>Jumlah Eksemplar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dataBuku as $buku)
                <tr>
                    <td>{{ $buku->ISBN }}</td>
                    <td>{{ $buku->judul_koleksi }}</td>
                    <td>{{ $buku->pengarang }}</td>
                    <td>{{ $buku->penerbit }}</td>
                    <td>{{ $buku->tahun }}</td>
                    <td>{{ $buku->kategori ?? '-' }}</td>
                    <td>{{ $buku->no_rak_buku ?? '-' }}</td>
                    <td>{{ $buku->jumlah_ekslempar }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Tidak ada data buku untuk diexport.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

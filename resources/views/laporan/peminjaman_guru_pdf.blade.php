<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 28px 32px; }
        body { font-family: "Times New Roman", Times, serif; color: #111; font-size: 12px; }
        h1 { text-align: center; font-size: 18px; margin: 0; text-transform: uppercase; }
        .subtitle { text-align: center; margin: 4px 0 18px; font-size: 13px; }
        h2 { font-size: 14px; margin: 12px 0 6px; }
        p { margin: 0 0 7px; line-height: 1.45; text-align: justify; }
        ol { margin-top: 4px; padding-left: 18px; line-height: 1.45; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; table-layout: fixed; }
        th, td { border: 1px solid #111; padding: 6px 5px; vertical-align: top; }
        th { background: #9A7952; color: #fff; text-align: center; font-weight: bold; }
        .center { text-align: center; }
        .summary { margin-top: 8px; font-weight: bold; }
        .signatures { width: 100%; margin-top: 28px; }
        .signatures td { border: none; text-align: center; padding-top: 4px; }
        .space { height: 52px; }
    </style>
</head>
<body>
    <h1>Laporan Peminjaman Buku Guru</h1>
    <div class="subtitle">Perpustakaan WIGATY LIBRARY<br>Periode: {{ $filter['periode_label'] }}</div>

    <h2>Pendahuluan</h2>
    <p>Perpustakaan sekolah menyediakan layanan peminjaman buku kepada guru sebagai bahan referensi dalam proses pembelajaran, pengembangan materi ajar, serta peningkatan kompetensi profesional. Kegiatan peminjaman buku khusus guru dicatat sebagai bagian dari administrasi perpustakaan.</p>

    <h2>Tujuan</h2>
    <ol>
        <li>Mendukung kegiatan pembelajaran guru dengan menyediakan sumber referensi yang relevan.</li>
        <li>Meningkatkan pemanfaatan koleksi perpustakaan oleh guru.</li>
        <li>Mendokumentasikan kegiatan peminjaman buku oleh guru secara tertib dan sistematis.</li>
    </ol>

    <h2>Data Peminjaman Buku Guru</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 11%;">Tanggal Pinjam</th>
                <th style="width: 15%;">Nama Guru</th>
                <th>Judul Buku</th>
                <th style="width: 14%;">Pengarang</th>
                <th style="width: 14%;">No Inventaris / Barcode</th>
                <th style="width: 11%;">Tanggal Kembali</th>
                <th style="width: 11%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $item->tgl_peminjaman ? \Carbon\Carbon::parse($item->tgl_peminjaman)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $item->nama_guru }}</td>
                    <td>{{ $item->judul_koleksi }}</td>
                    <td>{{ $item->pengarang }}</td>
                    <td>{{ $item->ISBN }}</td>
                    <td class="center">{{ $item->tgl_kembali ? \Carbon\Carbon::parse($item->tgl_kembali)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $item->status_peminjaman }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">Tidak ada data peminjaman guru pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="summary">
        Jumlah Guru yang Meminjam: {{ number_format($summary['jumlah_guru'], 0, ',', '.') }} orang<br>
        Jumlah Buku Dipinjam: {{ number_format($summary['total_transaksi'], 0, ',', '.') }} eksemplar
    </div>

    <h2>Kesimpulan</h2>
    <p>Layanan peminjaman buku khusus guru berjalan dengan baik dan tertib. Pencatatan peminjaman dilakukan secara sistematis sehingga memudahkan pemantauan pengembalian buku serta pengelolaan koleksi perpustakaan.</p>

    <table class="signatures">
        <tr>
            <td>Mengetahui,<br>Kepala Sekolah<div class="space"></div><strong>Visca Veronica, M.Pd.</strong><br>NIY. 015 820 570</td>
            <td>Yogyakarta, {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}<br>Kepala Perpustakaan<div class="space"></div><strong>Dewi Wulansari, S.Pd.</strong><br>NIY. -</td>
        </tr>
    </table>
</body>
</html>

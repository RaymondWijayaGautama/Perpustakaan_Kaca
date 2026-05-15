<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 2.54cm 2.54cm; }
        body {
            font-family: "Times New Roman", Times, serif;
            color: #000;
            font-size: 12pt;
            line-height: 1.15;
        }
        .title-block {
            margin-top: 42px;
            margin-bottom: 50px;
            text-align: center;
        }
        h1 {
            margin: 0;
            font-size: 16pt;
            line-height: 1.15;
            text-transform: uppercase;
        }
        h2 {
            margin: 28px 0 10px;
            font-size: 12pt;
            font-weight: bold;
        }
        p {
            margin: 0 0 10px;
            line-height: 1.15;
            text-align: justify;
            text-indent: 36px;
        }
        ol {
            margin: 0 0 18px 18px;
            padding-left: 12px;
            line-height: 1.15;
        }
        li { margin-bottom: 6px; }
        table { border-collapse: collapse; }
        .identity {
            width: 70%;
            margin: 0 0 16px;
        }
        .identity td,
        .field-table td {
            border: none;
            padding: 2px 0;
            vertical-align: top;
        }
        .identity .label { width: 34%; }
        .field-table .label { width: 34%; }
        .identity .colon,
        .field-table .colon {
            width: 4%;
            text-align: center;
        }
        .field-table {
            width: 70%;
            margin: 0 0 20px;
        }
        .page-break { page-break-before: always; }
        .data-section { margin-top: 46px; }
        .data-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            font-size: 8.5pt;
            line-height: 1.05;
        }
        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 5px 3px;
            vertical-align: top;
            word-wrap: break-word;
        }
        .data-table th {
            background: #fff;
            color: #000;
            text-align: center;
            font-weight: bold;
        }
        .center { text-align: center; }
        .summary {
            margin-top: 14px;
            margin-bottom: 34px;
            font-size: 12pt;
        }
        .summary table { width: 62%; }
        .summary td {
            border: none;
            padding: 1px 0;
        }
        .summary .label { width: 48%; }
        .summary .colon { width: 4%; text-align: center; }
        .closing-page { margin-top: 72px; }
        .closing-page h2 { margin-top: 0; }
        .closing { margin-top: 12px; }
        .date-line {
            margin: 52px 0 26px;
            padding-right: 36px;
            text-align: right;
        }
        .signatures {
            width: 100%;
            margin-top: 0;
            font-size: 12pt;
        }
        .signatures td {
            width: 50%;
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .signature-right { padding-left: 58px !important; }
        .space { height: 82px; }
    </style>
</head>
<body>
    <div class="title-block">
        <h1>Laporan Peminjaman Buku Guru</h1>
    </div>

    <table class="identity">
        <tr>
            <td class="label">Perpustakaan</td>
            <td class="colon">:</td>
            <td><strong>WIGATY LIBRARY</strong></td>
        </tr>
        <tr>
            <td class="label">Periode</td>
            <td class="colon">:</td>
            <td>{{ $filter['periode_label'] }}</td>
        </tr>
    </table>

    <h2>A.Pendahuluan</h2>
    <p>Perpustakaan sekolah merupakan pusat sumber belajar yang menyediakan berbagai koleksi buku untuk mendukung kegiatan pembelajaran. Selain melayani siswa, perpustakaan juga memberikan layanan peminjaman buku kepada guru sebagai bahan referensi dalam proses pembelajaran, pengembangan materi ajar, serta peningkatan kompetensi profesional.</p>
    <p>Kegiatan peminjaman buku khusus guru ini dilakukan secara teratur dan dicatat sebagai bagian dari administrasi perpustakaan.</p>

    <h2>B.Tujuan</h2>
    <ol>
        <li>Mendukung kegiatan pembelajaran guru dengan menyediakan sumber referensi yang relevan.</li>
        <li>Meningkatkan pemanfaatan koleksi perpustakaan oleh guru.</li>
        <li>Mendokumentasikan kegiatan peminjaman buku oleh guru secara tertib dan sistematis.</li>
        <li>Mengetahui tingkat penggunaan koleksi perpustakaan oleh tenaga pendidik.</li>
    </ol>

    <h2>C.Waktu Pelaksanaan</h2>
    <table class="field-table">
        <tr>
            <td class="label">Periode laporan</td>
            <td class="colon">:</td>
            <td>{{ $filter['periode_label'] }}</td>
        </tr>
        <tr>
            <td class="label">Tempat</td>
            <td class="colon">:</td>
            <td>Perpustakaan <strong>WIGATY LIBRARY</strong></td>
        </tr>
    </table>

    <div class="page-break data-section">
        <h2>D. Data Peminjaman Buku Guru</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 11%;">Tanggal<br>Pinjam</th>
                    <th style="width: 11%;">Nama<br>Guru</th>
                    <th style="width: 12%;">Mata<br>Pelajaran</th>
                    <th style="width: 14%;">Judul<br>Buku</th>
                    <th style="width: 11%;">Pengarang</th>
                    <th style="width: 15%;">No Inventaris /<br>Barcode</th>
                    <th style="width: 11%;">Tanggal<br>Kembali</th>
                    <th style="width: 11%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="center">{{ $item->tgl_peminjaman ? \Carbon\Carbon::parse($item->tgl_peminjaman)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $item->nama_guru }}</td>
                        <td class="center">-</td>
                        <td>{{ $item->judul_koleksi }}</td>
                        <td>{{ $item->pengarang }}</td>
                        <td>{{ $item->ISBN }}</td>
                        <td class="center">{{ $item->tgl_kembali ? \Carbon\Carbon::parse($item->tgl_kembali)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $item->status_peminjaman }}</td>
                    </tr>
                @empty
                    @for($row = 1; $row <= 5; $row++)
                        <tr>
                            <td class="center">{{ $row }}</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor
                @endforelse
            </tbody>
        </table>

        <div class="summary">
            <table>
                <tr>
                    <td class="label">Jumlah Guru yang Meminjam</td>
                    <td class="colon">:</td>
                    <td>{{ number_format($summary['jumlah_guru'], 0, ',', '.') }} orang</td>
                </tr>
                <tr>
                    <td class="label">Jumlah Buku Dipinjam</td>
                    <td class="colon">:</td>
                    <td>{{ number_format($summary['total_transaksi'], 0, ',', '.') }} eksemplar</td>
                </tr>
            </table>
        </div>

        <h2>E.Hasil Kegiatan</h2>
        <p>Berdasarkan data peminjaman buku selama periode laporan, diketahui bahwa guru memanfaatkan koleksi perpustakaan sebagai sumber referensi dalam mendukung kegiatan pembelajaran di kelas. Buku yang dipinjam meliputi buku referensi mata pelajaran, buku pengembangan profesi guru, serta buku literasi umum.</p>
        <p>Kegiatan ini menunjukkan bahwa perpustakaan berperan aktif dalam mendukung peningkatan kualitas pembelajaran di sekolah.</p>

        <h2>F.Kesimpulan</h2>
        <p>Layanan peminjaman buku khusus guru berjalan dengan baik dan tertib. Pencatatan peminjaman dilakukan secara sistematis sehingga memudahkan dalam pemantauan pengembalian buku serta pengelolaan koleksi perpustakaan.</p>
    </div>

    <div class="page-break closing-page">
        <h2>G. Penutup</h2>
        <p class="closing">Demikian laporan peminjaman buku khusus guru ini dibuat sebagai dokumentasi kegiatan layanan perpustakaan dan sebagai bahan evaluasi dalam pengelolaan perpustakaan sekolah.</p>

        <div class="date-line">Yogyakarta, {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y') }}</div>
        <table class="signatures">
            <tr>
                <td>Mengetahui,<br>Kepala Sekolah<div class="space"></div>Visca Veronica, M.Pd.<br>NIY. 015 820 570</td>
                <td class="signature-right"><br>Kepala Perpustakaan<div class="space"></div>Dewi Wulansari, S.Pd.<br>NIY. -</td>
            </tr>
        </table>
    </div>
</body>
</html>

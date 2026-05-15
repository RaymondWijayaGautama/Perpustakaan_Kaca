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
            margin-top: 62px;
            margin-bottom: 46px;
            text-align: center;
        }
        h1 {
            font-size: 16pt;
            line-height: 1.15;
            margin: 0;
            text-transform: uppercase;
        }
        .identity {
            width: 64%;
            margin: 0 0 28px;
            border-collapse: collapse;
        }
        .identity td {
            border: none;
            padding: 2px 0;
            vertical-align: top;
        }
        .identity .label { width: 32%; }
        .identity .colon { width: 4%; text-align: center; }
        h2 {
            font-size: 12pt;
            margin: 22px 0 10px;
            font-weight: bold;
        }
        p {
            margin: 0 0 10px;
            line-height: 1.15;
            text-align: justify;
            text-indent: 36px;
        }
        ol {
            margin: 0 0 16px 18px;
            padding-left: 12px;
            line-height: 1.15;
        }
        li { margin-bottom: 3px; }
        .content-section {
            margin-bottom: 24px;
        }
        .activity-list { margin-top: 0; }
        .activity-list strong { display: block; }
        .activity-list li { margin-bottom: 2px; }
        .section-intro {
            text-indent: 0;
            margin-bottom: 12px;
        }
        .field-table {
            width: 72%;
            margin: 0 0 24px;
            border-collapse: collapse;
        }
        .field-table td {
            border: none;
            padding: 2px 0;
        }
        .field-table .label { width: 30%; }
        .field-table .colon { width: 4%; text-align: center; }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            table-layout: fixed;
            font-size: 8pt;
            line-height: 1.05;
        }
        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 4px 3px;
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
            margin-top: 16px;
            margin-bottom: 36px;
            font-size: 12pt;
        }
        .summary strong { font-weight: normal; }
        .closing { margin-top: 18px; }
        .date-line {
            margin: 52px 0 26px;
            padding-right: 38px;
            text-align: right;
        }
        .signatures {
            width: 100%;
            margin-top: 0;
            border-collapse: collapse;
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
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <div class="title-block">
        <h1>Laporan Inventarisasi dan<br>Katalogisasi Buku Baru</h1>
    </div>

    <table class="identity">
        <tr>
            <td class="label">Perpustakaan</td>
            <td class="colon">:</td>
            <td><strong>WIGATY LIBRARY</strong></td>
        </tr>
        <tr>
            <td class="label">Tahun/Periode</td>
            <td class="colon">:</td>
            <td>{{ $filter['periode_label'] }}</td>
        </tr>
    </table>

    <div class="content-section">
        <h2>A. &nbsp;Pendahuluan</h2>
        <p>Kegiatan inventarisasi dan katalogisasi buku baru dilakukan untuk mendata, mengolah, serta menata buku agar mudah ditemukan dan dimanfaatkan oleh pemustaka.</p>
    </div>

    <div class="content-section">
        <h2>B. &nbsp;Tujuan Kegiatan</h2>
        <ol>
            <li>Mendata buku baru yang masuk ke perpustakaan.</li>
            <li>Memberikan nomor inventaris pada setiap buku.</li>
            <li>Melakukan proses katalogisasi agar buku mudah dicari dalam sistem perpustakaan.</li>
            <li>Menata buku sesuai klasifikasi yang berlaku.</li>
            <li>Menambah koleksi bahan pustaka untuk mendukung kegiatan belajar.</li>
        </ol>
    </div>

    <div class="content-section">
        <h2>C. &nbsp;Waktu dan Tempat</h2>
        <table class="field-table">
            <tr>
                <td class="label">Hari/Tanggal</td>
                <td class="colon">:</td>
                <td>{{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Tempat</td>
                <td class="colon">:</td>
                <td>Perpustakaan <strong>WIGATY LIBRARY</strong></td>
            </tr>
        </table>
    </div>

    <h2 class="page-break">D. &nbsp;Kegiatan yang Dilakukan</h2>
    <p class="section-intro">Kegiatan inventarisasi dan katalogisasi buku baru meliputi beberapa tahap berikut:</p>
    <ol class="activity-list">
        <li><strong>Penerimaan Buku</strong>Buku baru diterima dari pembelian, hibah, atau sumbangan.</li>
        <li><strong>Pencatatan Inventaris</strong>Setiap buku dicatat dalam buku inventaris atau sistem perpustakaan dengan mencantumkan nomor inventaris.</li>
        <li><strong>Katalogisasi Buku</strong>Buku diolah dengan mencatat data bibliografi seperti judul, pengarang, penerbit, tahun terbit, dan klasifikasi.</li>
        <li><strong>Pemberian Label dan Barcode</strong>Buku diberi label nomor panggil serta barcode untuk memudahkan peminjaman.</li>
        <li><strong>Pelapisan dan Stempel Buku</strong>Buku diberi stempel perpustakaan dan dilapisi sampul pelindung.</li>
        <li><strong>Penataan Buku di Rak</strong>Buku ditempatkan di rak sesuai dengan klasifikasi.</li>
    </ol>

    <h2>E. &nbsp;Hasil Kegiatan</h2>
    <p class="section-intro">Berikut adalah tabel hasil inventarisasi buku baru.</p>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 11%;">Tanggal<br>Masuk</th>
                <th style="width: 14%;">No<br>Inventaris</th>
                <th style="width: 15%;">Judul<br>Buku</th>
                <th style="width: 11%;">Pengarang</th>
                <th style="width: 10%;">Penerbit</th>
                <th style="width: 7%;">Tahun</th>
                <th style="width: 11%;">Klasifikasi</th>
                <th style="width: 7%;">Jumlah</th>
                <th style="width: 10%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $item->tgl_masuk_koleksi ? \Carbon\Carbon::parse($item->tgl_masuk_koleksi)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $item->ISBN }}</td>
                    <td>{{ $item->judul_koleksi }}</td>
                    <td>{{ $item->pengarang }}</td>
                    <td>{{ $item->penerbit }}</td>
                    <td class="center">{{ $item->tahun }}</td>
                    <td>{{ $item->kategori }}</td>
                    <td class="center">{{ $item->jumlah_eksemplar }}</td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="center">Tidak ada data buku baru pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="summary">Total Buku Baru : <strong>{{ number_format($summary['total_eksemplar'], 0, ',', '.') }}</strong> Eksemplar</div>

    <h2>F. &nbsp;Kesimpulan</h2>
    <p>Kegiatan inventarisasi dan katalogisasi buku baru telah dilaksanakan dengan baik. Buku-buku yang diterima telah didata, diberi nomor inventaris, dikatalogkan, serta ditata di rak perpustakaan sehingga siap dimanfaatkan oleh pemustaka untuk mendukung kegiatan pembelajaran dan literasi di sekolah.</p>

    <h2>G. &nbsp;Penutup</h2>
    <p class="closing">Demikian laporan kegiatan inventarisasi dan katalogisasi buku baru ini dibuat sebagai dokumentasi kegiatan pengelolaan perpustakaan.</p>

    <div class="date-line">Yogyakarta, {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->translatedFormat('d F Y') }}</div>
    <table class="signatures">
        <tr>
            <td>Mengetahui,<br>Kepala Sekolah<div class="space"></div>Visca Veronica, M.Pd.<br>NIY. 015 820 570</td>
            <td class="signature-right"><br>Kepala Perpustakaan<div class="space"></div>Dewi Wulansari, S.Pd.<br>NIY. -</td>
        </tr>
    </table>
</body>
</html>

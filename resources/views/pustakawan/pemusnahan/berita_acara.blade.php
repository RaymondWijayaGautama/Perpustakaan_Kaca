<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Pemusnahan Buku</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            color: #111827;
            margin: 0;
            background: #f8fafc;
        }

        .page {
            max-width: 900px;
            margin: 24px auto;
            background: #ffffff;
            padding: 48px;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
        }

        .header {
            text-align: center;
            margin-bottom: 32px;
        }

        .header h1 {
            font-size: 28px;
            margin: 0 0 8px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .header p {
            margin: 0;
            font-size: 15px;
        }

        .meta {
            margin: 24px 0;
            line-height: 1.7;
            font-size: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #1f2937;
            padding: 12px 14px;
            vertical-align: top;
            text-align: left;
            font-size: 14px;
        }

        th {
            width: 28%;
            background: #eff6ff;
        }

        .paragraph {
            margin-top: 28px;
            font-size: 15px;
            line-height: 1.8;
            text-align: justify;
        }

        .signature {
            margin-top: 56px;
            display: flex;
            justify-content: flex-end;
        }

        .signature-box {
            width: 280px;
            text-align: center;
            font-size: 15px;
        }

        .signature-space {
            height: 96px;
        }

        .actions {
            max-width: 900px;
            margin: 16px auto 0;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .actions button {
            border: none;
            background: #265f9c;
            color: #ffffff;
            padding: 12px 18px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
        }

        .actions button.secondary {
            background: #1f2937;
        }

        @media print {
            body {
                background: #ffffff;
            }

            .page {
                box-shadow: none;
                margin: 0;
                max-width: none;
                padding: 24px;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    @php
        \Carbon\Carbon::setLocale('id');
        $tanggalDokumen = $tanggalCetak->copy()->timezone('Asia/Jakarta');
    @endphp
    <div class="actions">
        <button onclick="window.print()">Cetak / Simpan PDF</button>
        <button class="secondary" onclick="window.close()">Tutup</button>
    </div>

    <div class="page">
        <div class="header">
            <h1>Berita Acara Pemusnahan Buku</h1>
            <p>Perpustakaan KACA</p>
        </div>

        <div class="meta">
            Pada hari ini, {{ $tanggalDokumen->translatedFormat('l, d F Y') }},
            telah dilakukan proses pemusnahan koleksi buku yang telah dinyatakan tidak layak pakai sesuai data berikut.
        </div>

        <table>
            <tbody>
                <tr>
                    <th>No. Berita Acara</th>
                    <td>BA-PM/{{ str_pad((string) $data->id, 4, '0', STR_PAD_LEFT) }}/{{ $tanggalDokumen->format('Y') }}</td>
                </tr>
                <tr>
                    <th>Tanggal Konfirmasi</th>
                    <td>{{ $tanggalDokumen->translatedFormat('d F Y H:i') }} WIB</td>
                </tr>
                <tr>
                    <th>ISBN</th>
                    <td>{{ $data->isbn }}</td>
                </tr>
                <tr>
                    <th>Judul Buku</th>
                    <td>{{ $data->judul_koleksi }}</td>
                </tr>
                <tr>
                    <th>Pengarang</th>
                    <td>{{ $data->pengarang }}</td>
                </tr>
                <tr>
                    <th>Penerbit</th>
                    <td>{{ $data->penerbit }}</td>
                </tr>
                <tr>
                    <th>Rak</th>
                    <td>{{ $data->no_rak_buku ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Status/Kondisi</th>
                    <td>{{ $data->keterangan_buku ?: 'Tidak dicatat' }}</td>
                </tr>
                <tr>
                    <th>Alasan Pemusnahan</th>
                    <td>{{ $data->alasan }}</td>
                </tr>
                <tr>
                    <th>Petugas Pustakawan</th>
                    <td>{{ $data->nama_petugas ?: $data->nip_karyawan }}</td>
                </tr>
                <tr>
                    <th>Status Persetujuan</th>
                    <td>{{ strtoupper($data->status) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="paragraph">
            Dokumen ini dibuat sebagai bukti administratif bahwa koleksi buku tersebut telah melalui proses verifikasi
            dan dinyatakan layak untuk dimusnahkan karena berada dalam kondisi rusak atau non-aktif, serta telah
            dicatat ke dalam log sistem perpustakaan.
        </div>

        <div class="signature">
            <div class="signature-box">
                <div>Yogyakarta, {{ $tanggalDokumen->translatedFormat('d F Y') }}</div>
                <div>Pustakawan</div>
                <div class="signature-space"></div>
                <div><strong>{{ $data->nama_petugas ?: $data->nip_karyawan }}</strong></div>
            </div>
        </div>
    </div>
</body>
</html>

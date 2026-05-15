@php
    \Carbon\Carbon::setLocale('id');

    $tanggalDokumen = $tanggalCetak->copy()->timezone('Asia/Jakarta');
    $nomorBa = 'BA-PM/' . str_pad((string) $data->id, 4, '0', STR_PAD_LEFT) . '/' . $tanggalDokumen->format('Y');
    $jumlah = 1;
    $noInventaris = $data->id_cp_koleksi
        ? $data->isbn . '/' . $data->id_cp_koleksi
        : ($data->nb_koleksi ?: $data->isbn);

    $rawAlasan = trim((string) ($data->alasan ?? ''));
    $kategoriPemusnahan = '';
    $alasanPemusnahan = $rawAlasan;

    if (preg_match('/^\[([^\]]+)\]\s*(.*)$/', $rawAlasan, $matches)) {
        $kategoriPemusnahan = trim($matches[1]);
        $alasanPemusnahan = trim($matches[2]) ?: $kategoriPemusnahan;
    }

    $kondisiBuku = $kategoriPemusnahan ?: ($data->keterangan_buku ?: $alasanPemusnahan ?: '-');
    $lampiranKosong = range(2, 3);
@endphp

<section class="document-page">
    <h1>Berita Acara Pemusnahan Buku<br>Perpustakaan</h1>
    <div class="doc-number">Nomor: {{ $nomorBa }}</div>

    <p>
        Pada hari ini <span class="dot-fill day-fill"></span>, tanggal <span class="dot-fill date-fill"></span>
        bulan <span class="dot-fill month-fill"></span> tahun <span class="dot-fill year-fill"></span>,
        kami yang bertanda tangan di bawah ini telah melaksanakan kegiatan pemusnahan koleksi buku
        perpustakaan yang sudah tidak layak pakai.
    </p>

    <p>
        Pemusnahan ini dilakukan berdasarkan hasil inventarisasi dan seleksi koleksi perpustakaan
        karena kondisi buku rusak berat, tidak lengkap, usang, atau tidak dapat digunakan lagi sebagai
        bahan pustaka.
    </p>

    <p>Adapun buku-buku yang dimusnahkan adalah sebagai berikut:</p>

    <table class="data-table main-table">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-title">Judul Buku</th>
                <th class="col-author">Pengarang</th>
                <th class="col-publisher">Penerbit</th>
                <th class="col-year">Tahun Terbit</th>
                <th class="col-inventory">No Inventaris</th>
                <th class="col-count">Jumlah</th>
                <th class="col-condition">Kondisi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center">1</td>
                <td>{{ $data->judul_koleksi }}</td>
                <td>{{ $data->pengarang ?: '-' }}</td>
                <td>{{ $data->penerbit ?: '-' }}</td>
                <td class="center">{{ $data->tahun ?: '-' }}</td>
                <td>{{ $noInventaris }}</td>
                <td class="center">{{ $jumlah }}</td>
                <td>{{ $kondisiBuku }}</td>
            </tr>
        </tbody>
    </table>

    <p>Jumlah buku yang dimusnahkan: {{ $jumlah }} eksemplar</p>

    <div class="method">
        Metode pemusnahan yang dilakukan adalah:<br>
        <span class="checkbox">&#9744;</span> Dihancurkan / dicacah<br>
        <span class="checkbox">&#9744;</span> Dibakar<br>
        <span class="checkbox">&#9744;</span> Didaur ulang<br>
        <span class="checkbox">&#9744;</span> Lainnya: <span class="dot-fill method-fill"></span>
    </div>

    <p>
        Demikian <strong>Berita Acara Pemusnahan Buku Perpustakaan</strong> ini dibuat dengan sebenarnya
        untuk dipergunakan sebagaimana mestinya.
    </p>
</section>

<section class="document-page page-break">
    <div class="date-block">
        <div>Yogyakarta, {{ $tanggalDokumen->translatedFormat('d F Y') }}</div>
        <div class="date-line"></div>
    </div>

    <table class="signature-table">
        <tr>
            <td>
                <strong>Mengetahui</strong><br>
                Kepala Sekolah
            </td>
            <td>
                <strong>Petugas Perpustakaan</strong><br>
                Kepala Perpustakaan
            </td>
            <td>
                <strong>Saksi</strong><br>
                1. ........................
            </td>
        </tr>
        <tr>
            <td class="signature-space"></td>
            <td class="signature-space"></td>
            <td class="signature-space"></td>
        </tr>
        <tr>
            <td>(................................)</td>
            <td>(................................)</td>
            <td>2. ........................</td>
        </tr>
    </table>

    <h2>Lampiran (Jika Ada)</h2>
    <p class="attachment-title"><strong>Daftar lengkap buku yang dimusnahkan</strong></p>

    <table class="data-table attachment-table">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-title">Judul Buku</th>
                <th class="col-author">Pengarang</th>
                <th class="col-year">Tahun</th>
                <th class="col-inventory">No Inventaris</th>
                <th class="col-count">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center">1</td>
                <td>{{ $data->judul_koleksi }}</td>
                <td>{{ $data->pengarang ?: '-' }}</td>
                <td class="center">{{ $data->tahun ?: '-' }}</td>
                <td>{{ $noInventaris }}</td>
                <td class="center">{{ $jumlah }}</td>
            </tr>
            @foreach ($lampiranKosong as $nomor)
                <tr>
                    <td class="center">{{ $nomor }}</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</section>

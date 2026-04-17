<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Siswa Terajin</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.5; margin: 20px 30px; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        h3, h4 { margin: 0; padding: 0; }
        
        /* Tabel Identitas */
        .table-identitas { width: 100%; border: none; margin-bottom: 15px; }
        .table-identitas td { padding: 2px 5px; vertical-align: top; }
        
        /* Tabel Data */
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 15px; margin-top: 10px; }
        .table-data th, .table-data td { border: 1px solid black; padding: 6px; text-align: center; }
        .table-data th { background-color: #e0e0e0; }
        
        /* Tanda Tangan */
        .table-ttd { width: 100%; margin-top: 40px; text-align: center; }
        .ttd-space { height: 80px; }
    </style>
</head>
<body>

    <div class="text-center" style="margin-bottom: 20px;">
        <h3>LAPORAN SISWA TERAJIN MEMINJAM BUKU</h3>
        <h4>PERPUSTAKAAN WIGATY LIBRARY</h4>
    </div>

    <div class="bold">A. Identitas Perpustakaan</div>
    <table class="table-identitas">
        <tr><td width="25%">Nama Perpustakaan</td><td width="2%">:</td><td>WIGATY LIBRARY</td></tr>
        <tr><td>Nama Sekolah</td><td>:</td><td>SMK BOPKRI 2 YOGYAKARTA</td></tr>
        <tr><td>Alamat Sekolah</td><td>:</td><td>Jalan Kapten Laut Samadikun 6 Wirogunan Mergangsan Yogyakarta 55151</td></tr>
        <tr><td>Tahun Ajaran</td><td>:</td><td>{{ $tahun_ajaran }}</td></tr>
        <tr><td>Periode Penilaian</td><td>:</td><td>{{ $periode }}</td></tr>
    </table>

    <div class="bold">B. Latar Belakang</div>
    <p style="text-align: justify; margin-top: 5px;">Program pemilihan siswa terajin meminjam buku merupakan salah satu upaya perpustakaan dalam meningkatkan minat baca siswa. Melalui kegiatan ini, perpustakaan memberikan apresiasi kepada siswa yang aktif memanfaatkan layanan peminjaman buku sebagai sumber belajar dan pengetahuan.</p>

    <div class="bold">C. Tujuan Kegiatan</div>
    <ol style="margin-top: 5px;">
        <li>Meningkatkan minat baca siswa.</li>
        <li>Mendorong siswa untuk lebih aktif memanfaatkan perpustakaan.</li>
        <li>Memberikan apresiasi kepada siswa yang rajin meminjam buku.</li>
        <li>Menumbuhkan budaya literasi di lingkungan sekolah.</li>
    </ol>

    <div class="bold">D. Kriteria Penilaian</div>
    <ol style="margin-top: 5px;">
        <li>Jumlah buku yang dipinjam dalam periode tertentu.</li>
        <li>Ketepatan waktu dalam pengembalian buku.</li>
        <li>Keaktifan dalam memanfaatkan layanan perpustakaan.</li>
    </ol>

    <div class="bold">E. Data Siswa Terajin Meminjam Buku</div>
    <table class="table-data">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>NIS</th>
                <th>Kelas</th>
                <th>Jumlah Dipinjam</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswaTerajin as $index => $siswa)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="text-align: left;">{{ $siswa->nama_siswa_tetap ?? 'Nama tidak ditemukan' }}</td>
                <td>{{ $siswa->nisn_siswa ?? '-' }}</td>
                <td>{{ $siswa->peminjaman_count }} Buku</td>
            </tr>
            @empty
            <tr>
                <td colspan="5">Belum ada data peminjaman.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="bold">F. Hasil Kegiatan</div>
    <p style="margin-top: 5px; margin-bottom: 5px;">Berdasarkan data peminjaman buku, siswa yang paling aktif meminjam buku adalah sebagai berikut:</p>
    <table style="border: none; width: 100%; margin-bottom: 15px;">
    <tr><td width="15%">Juara 1</td><td width="2%">:</td><td class="bold">{{ $juara1->nama_siswa_tetap ?? '-' }}</td></tr>
    <tr><td>Juara 2</td><td>:</td><td class="bold">{{ $juara2->nama_siswa_tetap ?? '-' }}</td></tr>
    <tr><td>Juara 3</td><td>:</td><td class="bold">{{ $juara3->nama_siswa_tetap ?? '-' }}</td></tr>
</table>

    <div class="bold">G. Kesimpulan</div>
    <p style="text-align: justify; margin-top: 5px;">Program siswa terajin meminjam buku dapat menjadi salah satu strategi untuk meningkatkan budaya membaca di sekolah. Melalui kegiatan ini, siswa lebih termotivasi untuk mengunjungi perpustakaan dan memanfaatkan koleksi buku yang tersedia.</p>

    <div class="bold">H. Rekomendasi</div>
    <ul style="margin-top: 5px;">
        <li>Program ini dapat dilaksanakan secara rutin setiap bulan atau semester.</li>
        <li>Memberikan penghargaan berupa sertifikat atau hadiah kepada siswa terpilih.</li>
        <li>Mengumumkan siswa terajin melalui papan pengumuman atau media sekolah.</li>
    </ul>

    <div class="bold">I. Penutup</div>
    <p style="text-align: justify; margin-top: 5px;">Demikian laporan siswa terajin meminjam buku ini dibuat sebagai bentuk dokumentasi kegiatan perpustakaan.</p>

    <table class="table-ttd">
        <tr>
            <td width="50%"></td>
            <td width="50%">Yogyakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>Mengetahui,<br>Kepala Sekolah</td>
            <td><br>Kepala Perpustakaan</td>
        </tr>
        <tr>
            <td class="ttd-space"></td>
            <td class="ttd-space"></td>
        </tr>
        <tr>
            <td class="bold">Visca Veronica, M.Pd.<br><span style="font-weight: normal;">NIY. 015 820 570</span></td>
            <td class="bold">Dewi Wulansari, S.Pd.<br><span style="font-weight: normal;">NIY. -</span></td>
        </tr>
    </table>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
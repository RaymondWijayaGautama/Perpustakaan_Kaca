<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. REFERENSI (WAJIB PERTAMA) ---
        DB::table('ref_koleksi')->insert([
            ['id_ref_koleksi' => 1, 'deskripsi' => 'Buku Pelajaran', 'is_delete' => 0],
            ['id_ref_koleksi' => 2, 'deskripsi' => 'Novel', 'is_delete' => 0],
            ['id_ref_koleksi' => 3, 'deskripsi' => 'Teknologi', 'is_delete' => 0],
        ]);

        DB::table('mst_koleksi_laporan')->insert([
            ['id_mst_laporan' => 1, 'is_delete' => 0],
            ['id_mst_laporan' => 2, 'is_delete' => 0],
        ]);

        // --- 2. DATA KARYAWAN (5 DATA) ---
        DB::table('mst_karyawan')->insert([
            [
                'nip_karyawan' => '19850101201001', 'nama_karyawan' => 'Sari Wijaya', 'nama_lengkap_gelar' => 'Sari Wijaya, S.I.Pust.',
                'golongan_karyawan' => 'III/b', 'jabatan_fungsional' => 'Pustakawan', 'tanggal_masuk' => '2010-01-01',
                'status_kepegawaian' => 'Tetap', 'nik_karyawan' => '3404010101850001', 'tempat_lahir_karyawan' => 'Yogyakarta',
                'gender_karyawan' => 'Perempuan', 'tgl_lahir_karyawan' => '1985-01-01', 'alamat_karyawan' => 'Sleman, DIY',
                'no_hp_karyawan' => '081222333444', 'email_karyawan' => 'admin@kitabaca.com', 'password_karyawan' => Hash::make('admin123'),
                'pend_terakhir_karyawan' => 'S1 Perpustakaan', 'prodi_karyawan' => 'Ilmu Perpustakaan', 'sertifikat_pendidik' => 'Ada',
                'link_foto_karyawan' => 'admin.jpg', 'is_delete' => 0
            ],
            [
                'nip_karyawan' => '19900505201502', 'nama_karyawan' => 'Budi Santoso', 'nama_lengkap_gelar' => 'Budi Santoso, S.Pd.',
                'golongan_karyawan' => 'III/a', 'jabatan_fungsional' => 'Guru', 'tanggal_masuk' => '2015-05-05',
                'status_kepegawaian' => 'Kontrak', 'nik_karyawan' => '3404010505900002', 'tempat_lahir_karyawan' => 'Bantul',
                'gender_karyawan' => 'Laki-laki', 'tgl_lahir_karyawan' => '1990-05-05', 'alamat_karyawan' => 'Bantul, DIY',
                'no_hp_karyawan' => '081333444555', 'email_karyawan' => 'budi@kitabaca.com', 'password_karyawan' => Hash::make('password123'),
                'pend_terakhir_karyawan' => 'S1 Pendidikan', 'prodi_karyawan' => 'Matematika', 'sertifikat_pendidik' => 'Ada',
                'link_foto_karyawan' => 'budi.jpg', 'is_delete' => 0
            ],
            [
                'nip_karyawan' => '19920808201803', 'nama_karyawan' => 'Ani Lestari', 'nama_lengkap_gelar' => 'Ani Lestari, M.Kom.',
                'golongan_karyawan' => 'III/a', 'jabatan_fungsional' => 'Guru', 'tanggal_masuk' => '2018-08-08',
                'status_kepegawaian' => 'Tetap', 'nik_karyawan' => '3404010808920003', 'tempat_lahir_karyawan' => 'Gunungkidul',
                'gender_karyawan' => 'Perempuan', 'tgl_lahir_karyawan' => '1992-08-08', 'alamat_karyawan' => 'Gunungkidul, DIY',
                'no_hp_karyawan' => '081444555666', 'email_karyawan' => 'ani@kitabaca.com', 'password_karyawan' => Hash::make('password123'),
                'pend_terakhir_karyawan' => 'S2 Informatika', 'prodi_karyawan' => 'Teknik Informatika', 'sertifikat_pendidik' => 'Tidak',
                'link_foto_karyawan' => 'ani.jpg', 'is_delete' => 0
            ],
            [
                'nip_karyawan' => '19950312202004', 'nama_karyawan' => 'Dian Pratama', 'nama_lengkap_gelar' => 'Dian Pratama, S.Kom.',
                'golongan_karyawan' => 'III/a', 'jabatan_fungsional' => 'Staff IT', 'tanggal_masuk' => '2020-03-12',
                'status_kepegawaian' => 'Tetap', 'nik_karyawan' => '3404011203950004', 'tempat_lahir_karyawan' => 'Sleman',
                'gender_karyawan' => 'Laki-laki', 'tgl_lahir_karyawan' => '1995-03-12', 'alamat_karyawan' => 'Gamping, Sleman',
                'no_hp_karyawan' => '081555666777', 'email_karyawan' => 'dian@kitabaca.com', 'password_karyawan' => Hash::make('password123'),
                'pend_terakhir_karyawan' => 'S1 Informatika', 'prodi_karyawan' => 'Sistem Informasi', 'sertifikat_pendidik' => 'Tidak',
                'link_foto_karyawan' => 'dian.jpg', 'is_delete' => 0
            ],
            [
                'nip_karyawan' => '19881120201205', 'nama_karyawan' => 'Eka Puteri', 'nama_lengkap_gelar' => 'Eka Puteri, M.Pd.',
                'golongan_karyawan' => 'III/c', 'jabatan_fungsional' => 'Guru', 'tanggal_masuk' => '2012-11-20',
                'status_kepegawaian' => 'Tetap', 'nik_karyawan' => '3404012011880005', 'tempat_lahir_karyawan' => 'Kulon Progo',
                'gender_karyawan' => 'Perempuan', 'tgl_lahir_karyawan' => '1988-11-20', 'alamat_karyawan' => 'Wates, Kulon Progo',
                'no_hp_karyawan' => '081666777888', 'email_karyawan' => 'eka@kitabaca.com', 'password_karyawan' => Hash::make('password123'),
                'pend_terakhir_karyawan' => 'S2 Pendidikan', 'prodi_karyawan' => 'Bahasa Indonesia', 'sertifikat_pendidik' => 'Ada',
                'link_foto_karyawan' => 'eka.jpg', 'is_delete' => 0
            ],
        ]);

        // --- 3. DATA SISWA (8 DATA) ---
        DB::table('mst_siswa')->insert([
            [
                'nisn_siswa' => '0056781234', 'nama_siswa_tetap' => 'Rizky Pratama', 'tgl_lahir_siswa' => '2005-03-12', 'tempat_lahir_siswa' => 'Yogyakarta', 'gender_siswa' => 'Laki-laki', 'goldar_siswa' => 'O', 'no_hp_siswa' => '085700001111', 'alamat_jalan_siswa' => 'Jl. Magelang KM 5', 'rt_siswa' => '01', 'rw_siswa' => '02', 'kelurahan_siswa' => 1, 'kecamatan_siswa' => 'Mlati', 'kota_kab_siswa' => 'Sleman', 'provinsi_siswa' => 'DIY', 'kode_pos_siswa' => '55284', 'nik_siswa' => '3404123456780001', 'nama_ortu_siswa' => 'Herman', 'nik_ortu_siswa' => '3404123456789999', 'peran_ortu_siswa' => 'Ayah', 'tahun_lulus' => '2024', 'password_siswa' => Hash::make('siswa123'), 'is_delete' => 0, 'kode_calon_siswa' => ''
            ],
            [
                'nisn_siswa' => '0067892345', 'nama_siswa_tetap' => 'Siti Aminah', 'tgl_lahir_siswa' => '2006-07-20', 'tempat_lahir_siswa' => 'Bantul', 'gender_siswa' => 'Perempuan', 'goldar_siswa' => 'A', 'no_hp_siswa' => '085711112222', 'alamat_jalan_siswa' => 'Jl. Imogiri Barat', 'rt_siswa' => '05', 'rw_siswa' => '01', 'kelurahan_siswa' => 2, 'kecamatan_siswa' => 'Sewon', 'kota_kab_siswa' => 'Bantul', 'provinsi_siswa' => 'DIY', 'kode_pos_siswa' => '55188', 'nik_siswa' => '3402123456780002', 'nama_ortu_siswa' => 'Fatimah', 'nik_ortu_siswa' => '3402123456789998', 'peran_ortu_siswa' => 'Ibu', 'tahun_lulus' => '2025', 'password_siswa' => Hash::make('siswa123'), 'is_delete' => 0, 'kode_calon_siswa' => ''
            ],
            [
                'nisn_siswa' => '0078903456', 'nama_siswa_tetap' => 'Kevin Sanjaya', 'tgl_lahir_siswa' => '2007-11-30', 'tempat_lahir_siswa' => 'Kulon Progo', 'gender_siswa' => 'Laki-laki', 'goldar_siswa' => 'B', 'no_hp_siswa' => '085722223333', 'alamat_jalan_siswa' => 'Jl. Wates KM 10', 'rt_siswa' => '10', 'rw_siswa' => '04', 'kelurahan_siswa' => 3, 'kecamatan_siswa' => 'Sentolo', 'kota_kab_siswa' => 'Kulon Progo', 'provinsi_siswa' => 'DIY', 'kode_pos_siswa' => '55664', 'nik_siswa' => '3401123456780003', 'nama_ortu_siswa' => 'Agus', 'nik_ortu_siswa' => '3401123456789997', 'peran_ortu_siswa' => 'Ayah', 'tahun_lulus' => '2026', 'password_siswa' => Hash::make('siswa123'), 'is_delete' => 0, 'kode_calon_siswa' => ''
            ],
            [
                'nisn_siswa' => '883726', 'nama_siswa_tetap' => 'Yu Ji-Min', 'tgl_lahir_siswa' => '2006-04-11', 'tempat_lahir_siswa' => 'Seoul', 'gender_siswa' => 'Perempuan', 'goldar_siswa' => 'B', 'no_hp_siswa' => '085733334444', 'alamat_jalan_siswa' => 'Apartment Karita', 'rt_siswa' => '01', 'rw_siswa' => '01', 'kelurahan_siswa' => 4, 'kecamatan_siswa' => 'Seoul', 'kota_kab_siswa' => 'Seoul', 'provinsi_siswa' => 'Seoul', 'kode_pos_siswa' => '12345', 'nik_siswa' => '3400123456780004', 'nama_ortu_siswa' => 'Karin', 'nik_ortu_siswa' => '3400123456789996', 'peran_ortu_siswa' => 'Ibu', 'tahun_lulus' => '2025', 'password_siswa' => Hash::make('siswa123'), 'is_delete' => 0, 'kode_calon_siswa' => ''
            ],
            [
                'nisn_siswa' => '0081234567', 'nama_siswa_tetap' => 'Bambang Pamungkas', 'tgl_lahir_siswa' => '2008-05-10', 'tempat_lahir_siswa' => 'Solo', 'gender_siswa' => 'Laki-laki', 'goldar_siswa' => 'AB', 'no_hp_siswa' => '085744445555', 'alamat_jalan_siswa' => 'Jl. Kaliurang KM 12', 'rt_siswa' => '02', 'rw_siswa' => '05', 'kelurahan_siswa' => 5, 'kecamatan_siswa' => 'Ngaglik', 'kota_kab_siswa' => 'Sleman', 'provinsi_siswa' => 'DIY', 'kode_pos_siswa' => '55581', 'nik_siswa' => '3404100508000005', 'nama_ortu_siswa' => 'Sutrisno', 'nik_ortu_siswa' => '3404100508009995', 'peran_ortu_siswa' => 'Ayah', 'tahun_lulus' => '2027', 'password_siswa' => Hash::make('siswa123'), 'is_delete' => 0, 'kode_calon_siswa' => ''
            ],
            [
                'nisn_siswa' => '0092345678', 'nama_siswa_tetap' => 'Indah Permata', 'tgl_lahir_siswa' => '2009-09-09', 'tempat_lahir_siswa' => 'Magelang', 'gender_siswa' => 'Perempuan', 'goldar_siswa' => 'O', 'no_hp_siswa' => '085755556666', 'alamat_jalan_siswa' => 'Perum Candi Indah', 'rt_siswa' => '04', 'rw_siswa' => '02', 'kelurahan_siswa' => 6, 'kecamatan_siswa' => 'Kalasan', 'kota_kab_siswa' => 'Sleman', 'provinsi_siswa' => 'DIY', 'kode_pos_siswa' => '55571', 'nik_siswa' => '3404090909000006', 'nama_ortu_siswa' => 'Ratna', 'nik_ortu_siswa' => '3404090909009994', 'peran_ortu_siswa' => 'Ibu', 'tahun_lulus' => '2028', 'password_siswa' => Hash::make('siswa123'), 'is_delete' => 0, 'kode_calon_siswa' => ''
            ],
            [
                'nisn_siswa' => '0103456789', 'nama_siswa_tetap' => 'Lee Jeno', 'tgl_lahir_siswa' => '2010-04-23', 'tempat_lahir_siswa' => 'Incheon', 'gender_siswa' => 'Laki-laki', 'goldar_siswa' => 'A', 'no_hp_siswa' => '085766667777', 'alamat_jalan_siswa' => 'Jl. Gejayan No. 10', 'rt_siswa' => '01', 'rw_siswa' => '01', 'kelurahan_siswa' => 7, 'kecamatan_siswa' => 'Depok', 'kota_kab_siswa' => 'Sleman', 'provinsi_siswa' => 'DIY', 'kode_pos_siswa' => '55281', 'nik_siswa' => '3404230410000007', 'nama_ortu_siswa' => 'Donghae', 'nik_ortu_siswa' => '3404230410009993', 'peran_ortu_siswa' => 'Ayah', 'tahun_lulus' => '2029', 'password_siswa' => Hash::make('siswa123'), 'is_delete' => 0, 'kode_calon_siswa' => ''
            ],
            [
                'nisn_siswa' => '0114567890', 'nama_siswa_tetap' => 'Putri Ariani', 'tgl_lahir_siswa' => '2005-12-31', 'tempat_lahir_siswa' => 'Kampar', 'gender_siswa' => 'Perempuan', 'goldar_siswa' => 'B', 'no_hp_siswa' => '085777778888', 'alamat_jalan_siswa' => 'Jl. Kusumanegara', 'rt_siswa' => '03', 'rw_siswa' => '03', 'kelurahan_siswa' => 8, 'kecamatan_siswa' => 'Umbulharjo', 'kota_kab_siswa' => 'Yogyakarta', 'provinsi_siswa' => 'DIY', 'kode_pos_siswa' => '55167', 'nik_siswa' => '3471311205000008', 'nama_ortu_siswa' => 'Ismawan', 'nik_ortu_siswa' => '3471311205009992', 'peran_ortu_siswa' => 'Ayah', 'tahun_lulus' => '2024', 'password_siswa' => Hash::make('siswa123'), 'is_delete' => 0, 'kode_calon_siswa' => ''
            ],
        ]);

        // --- 4. DATA BUKU (7 DATA) ---
        DB::table('mst_koleksi_buku')->insert([
            [
                'ISBN' => '978-602-03-1234-1', 'judul_koleksi' => 'Matematika SMK X', 'pengarang' => 'Dr. Abdurrahman', 'penerbit' => 'Erlangga', 'tahun' => '2022', 'nb_koleksi' => 101, 'tgl_masuk_koleksi' => '2022-01-15', 'jumlah_ekslempar' => 50, 'jumlah_halaman' => 240, 'ukuran_buku' => '21x29 cm', 'bibliografi' => '-', 'indeks_awal_akhir' => 0, 'keterangan_buku' => 'Buku wajib kelas X', 'no_rak_buku' => 'A-01', 'id_ref_koleksi' => 1, 'is_delete' => 0
            ],
            [
                'ISBN' => '978-602-03-5678-2', 'judul_koleksi' => 'Laskar Pelangi', 'pengarang' => 'Andrea Hirata', 'penerbit' => 'Bentang Pustaka', 'tahun' => '2005', 'nb_koleksi' => 202, 'tgl_masuk_koleksi' => '2023-02-10', 'jumlah_ekslempar' => 10, 'jumlah_halaman' => 529, 'ukuran_buku' => '13x20 cm', 'bibliografi' => '-', 'indeks_awal_akhir' => 0, 'keterangan_buku' => 'Koleksi fiksi populer', 'no_rak_buku' => 'F-12', 'id_ref_koleksi' => 2, 'is_delete' => 0
            ],
            [
                'ISBN' => '978-602-03-9999-3', 'judul_koleksi' => 'Web Design Guide', 'pengarang' => 'John Doe', 'penerbit' => 'Informatika', 'tahun' => '2023', 'nb_koleksi' => 303, 'tgl_masuk_koleksi' => '2024-01-01', 'jumlah_ekslempar' => 5, 'jumlah_halaman' => 150, 'ukuran_buku' => '15x23 cm', 'bibliografi' => 'Ada', 'indeks_awal_akhir' => 1, 'keterangan_buku' => 'Panduan praktik TKJ', 'no_rak_buku' => 'T-05', 'id_ref_koleksi' => 3, 'is_delete' => 0
            ],
            [
                'ISBN' => '978-602-03-2222-4', 'judul_koleksi' => 'Fisika Dasar XI', 'pengarang' => 'Bambang Ruwanto', 'penerbit' => 'Yudhistira', 'tahun' => '2021', 'nb_koleksi' => 102, 'tgl_masuk_koleksi' => '2021-06-10', 'jumlah_ekslempar' => 45, 'jumlah_halaman' => 310, 'ukuran_buku' => '21x29 cm', 'bibliografi' => '-', 'indeks_awal_akhir' => 0, 'keterangan_buku' => 'Buku wajib kelas XI', 'no_rak_buku' => 'A-02', 'id_ref_koleksi' => 1, 'is_delete' => 0
            ],
            [
                'ISBN' => '978-602-03-3333-5', 'judul_koleksi' => 'Bumi', 'pengarang' => 'Tere Liye', 'penerbit' => 'Gramedia', 'tahun' => '2014', 'nb_koleksi' => 203, 'tgl_masuk_koleksi' => '2023-05-05', 'jumlah_ekslempar' => 15, 'jumlah_halaman' => 440, 'ukuran_buku' => '13x20 cm', 'bibliografi' => '-', 'indeks_awal_akhir' => 0, 'keterangan_buku' => 'Novel seri petualangan', 'no_rak_buku' => 'F-13', 'id_ref_koleksi' => 2, 'is_delete' => 0
            ],
            [
                'ISBN' => '978-602-03-4444-6', 'judul_koleksi' => 'Pemrograman Laravel 11', 'pengarang' => 'Budi Raharjo', 'penerbit' => 'Informatika', 'tahun' => '2024', 'nb_koleksi' => 304, 'tgl_masuk_koleksi' => '2024-02-01', 'jumlah_ekslempar' => 20, 'jumlah_halaman' => 350, 'ukuran_buku' => '15x23 cm', 'bibliografi' => 'Ada', 'indeks_awal_akhir' => 1, 'keterangan_buku' => 'Panduan Backend Modern', 'no_rak_buku' => 'T-06', 'id_ref_koleksi' => 3, 'is_delete' => 0
            ],
            [
                'ISBN' => '978-602-03-5555-7', 'judul_koleksi' => 'Sejarah Dunia', 'pengarang' => 'H.G. Wells', 'penerbit' => 'Indoliterasi', 'tahun' => '2018', 'nb_koleksi' => 401, 'tgl_masuk_koleksi' => '2022-12-12', 'jumlah_ekslempar' => 8, 'jumlah_halaman' => 600, 'ukuran_buku' => '14x21 cm', 'bibliografi' => 'Ada', 'indeks_awal_akhir' => 1, 'keterangan_buku' => 'Koleksi referensi umum', 'no_rak_buku' => 'U-01', 'id_ref_koleksi' => 2, 'is_delete' => 0
            ],
        ]);

        DB::table('ref_koleksi')->insertOrIgnore([
            ['id_ref_koleksi' => 4, 'deskripsi' => 'Laporan PKL', 'is_delete' => 0],
        ]);

        // --- 2. Isi data master laporan ---
        DB::table('mst_koleksi_laporan')->insertOrIgnore([
            ['id_mst_laporan' => 10, 'is_delete' => 0],
            ['id_mst_laporan' => 11, 'is_delete' => 0],
            ['id_mst_laporan' => 12, 'is_delete' => 0],
        ]);

        // --- 3. Tambahkan Judul Laporan PKL ke mst_koleksi_buku ---
        // (Kita masukkan ke sini karena struktur tabel Anda menggunakan ISBN sebagai foreign key utama)
        DB::table('mst_koleksi_buku')->insert([
            [
                'ISBN' => 'PKL-2026-001',
                'judul_koleksi' => 'Sistem Informasi Kasir Berbasis Web',
                'pengarang' => 'Andi Wijaya',
                'penerbit' => 'SMK Negeri 1 Yogyakarta',
                'tahun' => '2026',
                'nb_koleksi' => 901,
                'tgl_masuk_koleksi' => '2026-01-10',
                'jumlah_ekslempar' => 1,
                'jumlah_halaman' => 80,
                'ukuran_buku' => 'A4',
                'bibliografi' => 'Ada',
                'indeks_awal_akhir' => 0,
                'keterangan_buku' => 'Laporan PKL di PT. Techno',
                'no_rak_buku' => 'RAK-PKL-01',
                'id_ref_koleksi' => 4, // Laporan PKL
                'is_delete' => 0
            ],
            [
                'ISBN' => 'PKL-2026-002',
                'judul_koleksi' => 'Rancang Bangun Jaringan LAN',
                'pengarang' => 'Bagas Saputra',
                'penerbit' => 'SMK Negeri 1 Yogyakarta',
                'tahun' => '2025',
                'nb_koleksi' => 902,
                'tgl_masuk_koleksi' => '2026-01-15',
                'jumlah_ekslempar' => 1,
                'jumlah_halaman' => 120,
                'ukuran_buku' => 'A4',
                'bibliografi' => 'Ada',
                'indeks_awal_akhir' => 0,
                'keterangan_buku' => 'Laporan PKL di Telkom',
                'no_rak_buku' => 'RAK-PKL-01',
                'id_ref_koleksi' => 4,
                'is_delete' => 0
            ],
            [
                'ISBN' => 'PKL-2026-003',
                'judul_koleksi' => 'Analisis Keamanan Server Linux',
                'pengarang' => 'Citra Lestari',
                'penerbit' => 'SMK Negeri 1 Yogyakarta',
                'tahun' => '2026',
                'nb_koleksi' => 903,
                'tgl_masuk_koleksi' => '2026-02-01',
                'jumlah_ekslempar' => 1,
                'jumlah_halaman' => 95,
                'ukuran_buku' => 'A4',
                'bibliografi' => 'Ada',
                'indeks_awal_akhir' => 0,
                'keterangan_buku' => 'Laporan PKL di Kominfo',
                'no_rak_buku' => 'RAK-PKL-02',
                'id_ref_koleksi' => 4,
                'is_delete' => 0
            ],
        ]);

        // --- 4. Hubungkan ke cp_koleksi ---
        DB::table('cp_koleksi')->insert([
            ['id_cp_koleksi' => 101, 'status_buku' => 'Tersedia', 'ISBN' => 'PKL-2026-001', 'id_mst_laporan' => 10],
            ['id_cp_koleksi' => 102, 'status_buku' => 'Tersedia', 'ISBN' => 'PKL-2026-002', 'id_mst_laporan' => 11],
            ['id_cp_koleksi' => 103, 'status_buku' => 'Tersedia', 'ISBN' => 'PKL-2026-003', 'id_mst_laporan' => 12],
        ]);

        

    }
}
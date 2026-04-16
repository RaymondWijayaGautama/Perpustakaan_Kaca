<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Matikan proteksi FK sementara agar insert tidak terblokir
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // =============================================================
        // 1. DATA REFERENSI KATEGORI (10 Data)
        // =============================================================
        $kategori = [
            ['id_ref_koleksi' => 1, 'deskripsi' => 'Buku Pelajaran', 'is_delete' => 0],
            ['id_ref_koleksi' => 2, 'deskripsi' => 'Novel & Fiksi', 'is_delete' => 0],
            ['id_ref_koleksi' => 3, 'deskripsi' => 'Teknologi Informasi', 'is_delete' => 0],
            ['id_ref_koleksi' => 4, 'deskripsi' => 'Laporan PKL', 'is_delete' => 0],
            ['id_ref_koleksi' => 5, 'deskripsi' => 'Sains & Alam', 'is_delete' => 0],
            ['id_ref_koleksi' => 6, 'deskripsi' => 'Sejarah & Budaya', 'is_delete' => 0],
            ['id_ref_koleksi' => 7, 'deskripsi' => 'Seni & Desain', 'is_delete' => 0],
            ['id_ref_koleksi' => 8, 'deskripsi' => 'Biografi', 'is_delete' => 0],
            ['id_ref_koleksi' => 9, 'deskripsi' => 'Komik & Manga', 'is_delete' => 0],
            ['id_ref_koleksi' => 10, 'deskripsi' => 'Ensiklopedia', 'is_delete' => 0],
        ];
        foreach ($kategori as $k) {
            DB::table('ref_koleksi')->updateOrInsert(['id_ref_koleksi' => $k['id_ref_koleksi']], $k);
        }

        // =============================================================
        // 2. MASTER LAPORAN PKL (10 Data)
        // =============================================================
        for ($i = 1; $i <= 10; $i++) {
            DB::table('mst_koleksi_laporan')->updateOrInsert(
                ['id_mst_laporan' => $i],
                ['is_delete' => 0]
            );
        }

        // =============================================================
        // 3. ANGGOTA KARYAWAN (10 Data)
        // =============================================================
        $karyawan = [
            ['nip_karyawan' => '19850101201001', 'nama_karyawan' => 'Sari Wijaya', 'nama_lengkap_gelar' => 'Sari Wijaya, S.I.Pust.', 'golongan_karyawan' => 'III/b', 'jabatan_fungsional' => 'Pustakawan', 'tanggal_masuk' => '2010-01-01', 'status_kepegawaian' => 'Tetap', 'nik_karyawan' => '3404010101850001', 'tempat_lahir_karyawan' => 'Yogyakarta', 'gender_karyawan' => 'Perempuan', 'tgl_lahir_karyawan' => '1985-01-01', 'alamat_karyawan' => 'Sleman, DIY', 'no_hp_karyawan' => '081222333444', 'email_karyawan' => 'admin@kitabaca.com', 'password_karyawan' => Hash::make('admin123'), 'pend_terakhir_karyawan' => 'S1 Perpustakaan', 'prodi_karyawan' => 'Ilmu Perpustakaan', 'sertifikat_pendidik' => 'Ada', 'link_foto_karyawan' => 'admin.jpg', 'is_delete' => 0],
            ['nip_karyawan' => '19900101', 'nama_karyawan' => 'Budi', 'nama_lengkap_gelar' => 'Budi, S.T.', 'golongan_karyawan' => 'III/a', 'jabatan_fungsional' => 'Staf', 'tanggal_masuk' => '2015-01-01', 'status_kepegawaian' => 'Kontrak', 'nik_karyawan' => '101', 'tempat_lahir_karyawan' => 'Bantul', 'gender_karyawan' => 'Laki-laki', 'tgl_lahir_karyawan' => '1990-01-01', 'alamat_karyawan' => 'Bantul', 'no_hp_karyawan' => '081', 'email_karyawan' => 'budi@kaca.com', 'password_karyawan' => Hash::make('123'), 'pend_terakhir_karyawan' => 'S1', 'prodi_karyawan' => 'TI', 'sertifikat_pendidik' => 'Tidak', 'link_foto_karyawan' => '2.jpg', 'is_delete' => 0],
            ['nip_karyawan' => '19900102', 'nama_karyawan' => 'Ani', 'nama_lengkap_gelar' => 'Ani, S.Pd.', 'golongan_karyawan' => 'III/a', 'jabatan_fungsional' => 'Staf', 'tanggal_masuk' => '2016-01-01', 'status_kepegawaian' => 'Tetap', 'nik_karyawan' => '102', 'tempat_lahir_karyawan' => 'Sleman', 'gender_karyawan' => 'Perempuan', 'tgl_lahir_karyawan' => '1991-01-01', 'alamat_karyawan' => 'Sleman', 'no_hp_karyawan' => '082', 'email_karyawan' => 'ani@kaca.com', 'password_karyawan' => Hash::make('123'), 'pend_terakhir_karyawan' => 'S1', 'prodi_karyawan' => 'Pend', 'sertifikat_pendidik' => 'Ada', 'link_foto_karyawan' => '3.jpg', 'is_delete' => 0],
            ['nip_karyawan' => '19900103', 'nama_karyawan' => 'Dedi', 'nama_lengkap_gelar' => 'Dedi, M.Kom.', 'golongan_karyawan' => 'III/b', 'jabatan_fungsional' => 'IT', 'tanggal_masuk' => '2017-01-01', 'status_kepegawaian' => 'Tetap', 'nik_karyawan' => '103', 'tempat_lahir_karyawan' => 'Kota', 'gender_karyawan' => 'Laki-laki', 'tgl_lahir_karyawan' => '1989-01-01', 'alamat_karyawan' => 'Kota', 'no_hp_karyawan' => '083', 'email_karyawan' => 'dedi@kaca.com', 'password_karyawan' => Hash::make('123'), 'pend_terakhir_karyawan' => 'S2', 'prodi_karyawan' => 'TI', 'sertifikat_pendidik' => 'Tidak', 'link_foto_karyawan' => '4.jpg', 'is_delete' => 0],
            ['nip_karyawan' => '19900104', 'nama_karyawan' => 'Eka', 'nama_lengkap_gelar' => 'Eka, S.E.', 'golongan_karyawan' => 'III/a', 'jabatan_fungsional' => 'Staf', 'tanggal_masuk' => '2018-01-01', 'status_kepegawaian' => 'Kontrak', 'nik_karyawan' => '104', 'tempat_lahir_karyawan' => 'KP', 'gender_karyawan' => 'Perempuan', 'tgl_lahir_karyawan' => '1992-01-01', 'alamat_karyawan' => 'KP', 'no_hp_karyawan' => '084', 'email_karyawan' => 'eka@kaca.com', 'password_karyawan' => Hash::make('123'), 'pend_terakhir_karyawan' => 'S1', 'prodi_karyawan' => 'Eko', 'sertifikat_pendidik' => 'Tidak', 'link_foto_karyawan' => '5.jpg', 'is_delete' => 0],
            ['nip_karyawan' => '19900105', 'nama_karyawan' => 'Feri', 'nama_lengkap_gelar' => 'Feri, S.H.', 'golongan_karyawan' => 'III/a', 'jabatan_fungsional' => 'Staf', 'tanggal_masuk' => '2019-01-01', 'status_kepegawaian' => 'Tetap', 'nik_karyawan' => '105', 'tempat_lahir_karyawan' => 'Solo', 'gender_karyawan' => 'Laki-laki', 'tgl_lahir_karyawan' => '1993-01-01', 'alamat_karyawan' => 'Solo', 'no_hp_karyawan' => '085', 'email_karyawan' => 'feri@kaca.com', 'password_karyawan' => Hash::make('123'), 'pend_terakhir_karyawan' => 'S1', 'prodi_karyawan' => 'Hukum', 'sertifikat_pendidik' => 'Tidak', 'link_foto_karyawan' => '6.jpg', 'is_delete' => 0],
            ['nip_karyawan' => '19900106', 'nama_karyawan' => 'Gita', 'nama_lengkap_gelar' => 'Gita, S.Si.', 'golongan_karyawan' => 'III/a', 'jabatan_fungsional' => 'Staf', 'tanggal_masuk' => '2020-01-01', 'status_kepegawaian' => 'Kontrak', 'nik_karyawan' => '106', 'tempat_lahir_karyawan' => 'Klaten', 'gender_karyawan' => 'Perempuan', 'tgl_lahir_karyawan' => '1994-01-01', 'alamat_karyawan' => 'Klaten', 'no_hp_karyawan' => '086', 'email_karyawan' => 'gita@kaca.com', 'password_karyawan' => Hash::make('123'), 'pend_terakhir_karyawan' => 'S1', 'prodi_karyawan' => 'Sains', 'sertifikat_pendidik' => 'Tidak', 'link_foto_karyawan' => '7.jpg', 'is_delete' => 0],
            ['nip_karyawan' => '19900107', 'nama_karyawan' => 'Hadi', 'nama_lengkap_gelar' => 'Hadi, S.Kom.', 'golongan_karyawan' => 'III/a', 'jabatan_fungsional' => 'Staf', 'tanggal_masuk' => '2021-01-01', 'status_kepegawaian' => 'Tetap', 'nik_karyawan' => '107', 'tempat_lahir_karyawan' => 'Sragen', 'gender_karyawan' => 'Laki-laki', 'tgl_lahir_karyawan' => '1995-01-01', 'alamat_karyawan' => 'Sragen', 'no_hp_karyawan' => '087', 'email_karyawan' => 'hadi@kaca.com', 'password_karyawan' => Hash::make('123'), 'pend_terakhir_karyawan' => 'S1', 'prodi_karyawan' => 'TI', 'sertifikat_pendidik' => 'Tidak', 'link_foto_karyawan' => '8.jpg', 'is_delete' => 0],
            ['nip_karyawan' => '19900108', 'nama_karyawan' => 'Indah', 'nama_lengkap_gelar' => 'Indah, M.Pd.', 'golongan_karyawan' => 'III/c', 'jabatan_fungsional' => 'Pustakawan', 'tanggal_masuk' => '2012-01-01', 'status_kepegawaian' => 'Tetap', 'nik_karyawan' => '108', 'tempat_lahir_karyawan' => 'Magelang', 'gender_karyawan' => 'Perempuan', 'tgl_lahir_karyawan' => '1987-01-01', 'alamat_karyawan' => 'Magelang', 'no_hp_karyawan' => '088', 'email_karyawan' => 'indah@kaca.com', 'password_karyawan' => Hash::make('123'), 'pend_terakhir_karyawan' => 'S2', 'prodi_karyawan' => 'Pend', 'sertifikat_pendidik' => 'Ada', 'link_foto_karyawan' => '9.jpg', 'is_delete' => 0],
            ['nip_karyawan' => '19900109', 'nama_karyawan' => 'Joko', 'nama_lengkap_gelar' => 'Joko, A.Md.', 'golongan_karyawan' => 'II/c', 'jabatan_fungsional' => 'Staf', 'tanggal_masuk' => '2022-01-01', 'status_kepegawaian' => 'Kontrak', 'nik_karyawan' => '109', 'tempat_lahir_karyawan' => 'Kebumen', 'gender_karyawan' => 'Laki-laki', 'tgl_lahir_karyawan' => '1996-01-01', 'alamat_karyawan' => 'Kebumen', 'no_hp_karyawan' => '089', 'email_karyawan' => 'joko@kaca.com', 'password_karyawan' => Hash::make('123'), 'pend_terakhir_karyawan' => 'D3', 'prodi_karyawan' => 'Arsip', 'sertifikat_pendidik' => 'Tidak', 'link_foto_karyawan' => '10.jpg', 'is_delete' => 0],
        ];
        foreach ($karyawan as $k) {
            DB::table('mst_karyawan')->updateOrInsert(['nip_karyawan' => $k['nip_karyawan']], $k);
        }

        // =============================================================
        // 4. ANGGOTA SISWA (10 Data)
        // =============================================================
        $siswa = [
            ['id_siswa_tetap' => 1, 'nama_siswa_tetap' => 'Rizky Pratama', 'nisn_siswa' => '001', 'kode_calon_siswa' => 'REG-1', 'tgl_lahir_siswa' => '2005-01-01', 'tempat_lahir_siswa' => 'Jogja', 'gender_siswa' => 'Laki-laki', 'goldar_siswa' => 'O', 'no_hp_siswa' => '0851', 'alamat_jalan_siswa' => 'Jl. A', 'rt_siswa' => '01', 'rw_siswa' => '01', 'kelurahan_siswa' => 1, 'kecamatan_siswa' => 'A', 'kota_kab_siswa' => 'Sleman', 'provinsi_siswa' => 'DIY', 'kode_pos_siswa' => '55', 'nik_siswa' => '1', 'nama_ortu_siswa' => 'Ortu 1', 'nik_ortu_siswa' => '1', 'peran_ortu_siswa' => 'Ayah', 'tahun_lulus' => '2024', 'password_siswa' => Hash::make('siswa123'), 'is_delete' => 0],
            ['id_siswa_tetap' => 2, 'nama_siswa_tetap' => 'Siti Aminah', 'nisn_siswa' => '002', 'kode_calon_siswa' => 'REG-2', 'tgl_lahir_siswa' => '2005-02-01', 'tempat_lahir_siswa' => 'Bantul', 'gender_siswa' => 'Perempuan', 'goldar_siswa' => 'A', 'no_hp_siswa' => '0852', 'alamat_jalan_siswa' => 'Jl. B', 'rt_siswa' => '02', 'rw_siswa' => '02', 'kelurahan_siswa' => 2, 'kecamatan_siswa' => 'B', 'kota_kab_siswa' => 'Bantul', 'provinsi_siswa' => 'DIY', 'kode_pos_siswa' => '55', 'nik_siswa' => '2', 'nama_ortu_siswa' => 'Ortu 2', 'nik_ortu_siswa' => '2', 'peran_ortu_siswa' => 'Ibu', 'tahun_lulus' => '2024', 'password_siswa' => Hash::make('siswa123'), 'is_delete' => 0],
            ['id_siswa_tetap' => 3, 'nama_siswa_tetap' => 'Kevin Sanjaya', 'nisn_siswa' => '003', 'kode_calon_siswa' => 'REG-3', 'tgl_lahir_siswa' => '2005-03-01', 'tempat_lahir_siswa' => 'Wates', 'gender_siswa' => 'Laki-laki', 'goldar_siswa' => 'B', 'no_hp_siswa' => '0853', 'alamat_jalan_siswa' => 'Jl. C', 'rt_siswa' => '03', 'rw_siswa' => '03', 'kelurahan_siswa' => 3, 'kecamatan_siswa' => 'C', 'kota_kab_siswa' => 'KP', 'provinsi_siswa' => 'DIY', 'kode_pos_siswa' => '55', 'nik_siswa' => '3', 'nama_ortu_siswa' => 'Ortu 3', 'nik_ortu_siswa' => '3', 'peran_ortu_siswa' => 'Ayah', 'tahun_lulus' => '2024', 'password_siswa' => Hash::make('siswa123'), 'is_delete' => 0],
            ['id_siswa_tetap' => 4, 'nama_siswa_tetap' => 'Yu Ji-Min', 'nisn_siswa' => '004', 'kode_calon_siswa' => 'REG-4', 'tgl_lahir_siswa' => '2005-04-01', 'tempat_lahir_siswa' => 'Seoul', 'gender_siswa' => 'Perempuan', 'goldar_siswa' => 'AB', 'no_hp_siswa' => '0854', 'alamat_jalan_siswa' => 'Jl. D', 'rt_siswa' => '04', 'rw_siswa' => '04', 'kelurahan_siswa' => 4, 'kecamatan_siswa' => 'D', 'kota_kab_siswa' => 'Sleman', 'provinsi_siswa' => 'DIY', 'kode_pos_siswa' => '55', 'nik_siswa' => '4', 'nama_ortu_siswa' => 'Ortu 4', 'nik_ortu_siswa' => '4', 'peran_ortu_siswa' => 'Ibu', 'tahun_lulus' => '2024', 'password_siswa' => Hash::make('siswa123'), 'is_delete' => 0],
            ['id_siswa_tetap' => 5, 'nama_siswa_tetap' => 'Bambang P', 'nisn_siswa' => '005', 'kode_calon_siswa' => 'REG-5', 'tgl_lahir_siswa' => '2005-05-01', 'tempat_lahir_siswa' => 'Solo', 'gender_siswa' => 'Laki-laki', 'goldar_siswa' => 'O', 'no_hp_siswa' => '0855', 'alamat_jalan_siswa' => 'Jl. E', 'rt_siswa' => '05', 'rw_siswa' => '05', 'kelurahan_siswa' => 5, 'kecamatan_siswa' => 'E', 'kota_kab_siswa' => 'Solo', 'provinsi_siswa' => 'Jateng', 'kode_pos_siswa' => '57', 'nik_siswa' => '5', 'nama_ortu_siswa' => 'Ortu 5', 'nik_ortu_siswa' => '5', 'peran_ortu_siswa' => 'Ayah', 'tahun_lulus' => '2024', 'password_siswa' => Hash::make('siswa123'), 'is_delete' => 0],
            ['id_siswa_tetap' => 6, 'nama_siswa_tetap' => 'Indah P', 'nisn_siswa' => '006', 'kode_calon_siswa' => 'REG-6', 'tgl_lahir_siswa' => '2005-06-01', 'tempat_lahir_siswa' => 'Klaten', 'gender_siswa' => 'Perempuan', 'goldar_siswa' => 'A', 'no_hp_siswa' => '0856', 'alamat_jalan_siswa' => 'Jl. F', 'rt_siswa' => '06', 'rw_siswa' => '06', 'kelurahan_siswa' => 6, 'kecamatan_siswa' => 'F', 'kota_kab_siswa' => 'Klaten', 'provinsi_siswa' => 'Jateng', 'kode_pos_siswa' => '57', 'nik_siswa' => '6', 'nama_ortu_siswa' => 'Ortu 6', 'nik_ortu_siswa' => '6', 'peran_ortu_siswa' => 'Ibu', 'tahun_lulus' => '2024', 'password_siswa' => Hash::make('siswa123'), 'is_delete' => 0],
            ['id_siswa_tetap' => 7, 'nama_siswa_tetap' => 'Lee Jeno', 'nisn_siswa' => '007', 'kode_calon_siswa' => 'REG-7', 'tgl_lahir_siswa' => '2005-07-01', 'tempat_lahir_siswa' => 'Incheon', 'gender_siswa' => 'Laki-laki', 'goldar_siswa' => 'B', 'no_hp_siswa' => '0857', 'alamat_jalan_siswa' => 'Jl. G', 'rt_siswa' => '07', 'rw_siswa' => '07', 'kelurahan_siswa' => 7, 'kecamatan_siswa' => 'G', 'kota_kab_siswa' => 'Sleman', 'provinsi_siswa' => 'DIY', 'kode_pos_siswa' => '55', 'nik_siswa' => '7', 'nama_ortu_siswa' => 'Ortu 7', 'nik_ortu_siswa' => '7', 'peran_ortu_siswa' => 'Ayah', 'tahun_lulus' => '2024', 'password_siswa' => Hash::make('siswa123'), 'is_delete' => 0],
            ['id_siswa_tetap' => 8, 'nama_siswa_tetap' => 'Putri A', 'nisn_siswa' => '008', 'kode_calon_siswa' => 'REG-8', 'tgl_lahir_siswa' => '2005-08-01', 'tempat_lahir_siswa' => 'Kampar', 'gender_siswa' => 'Perempuan', 'goldar_siswa' => 'O', 'no_hp_siswa' => '0858', 'alamat_jalan_siswa' => 'Jl. H', 'rt_siswa' => '08', 'rw_siswa' => '08', 'kelurahan_siswa' => 8, 'kecamatan_siswa' => 'H', 'kota_kab_siswa' => 'Riau', 'provinsi_siswa' => 'Riau', 'kode_pos_siswa' => '28', 'nik_siswa' => '8', 'nama_ortu_siswa' => 'Ortu 8', 'nik_ortu_siswa' => '8', 'peran_ortu_siswa' => 'Ayah', 'tahun_lulus' => '2024', 'password_siswa' => Hash::make('siswa123'), 'is_delete' => 0],
            ['id_siswa_tetap' => 9, 'nama_siswa_tetap' => 'Gibran R', 'nisn_siswa' => '009', 'kode_calon_siswa' => 'REG-9', 'tgl_lahir_siswa' => '2005-09-01', 'tempat_lahir_siswa' => 'Solo', 'gender_siswa' => 'Laki-laki', 'goldar_siswa' => 'A', 'no_hp_siswa' => '0859', 'alamat_jalan_siswa' => 'Jl. I', 'rt_siswa' => '09', 'rw_siswa' => '09', 'kelurahan_siswa' => 9, 'kecamatan_siswa' => 'I', 'kota_kab_siswa' => 'Solo', 'provinsi_siswa' => 'Jateng', 'kode_pos_siswa' => '57', 'nik_siswa' => '9', 'nama_ortu_siswa' => 'Ortu 9', 'nik_ortu_siswa' => '9', 'peran_ortu_siswa' => 'Ayah', 'tahun_lulus' => '2024', 'password_siswa' => Hash::make('siswa123'), 'is_delete' => 0],
            ['id_siswa_tetap' => 10, 'nama_siswa_tetap' => 'Kaesang P', 'nisn_siswa' => '010', 'kode_calon_siswa' => 'REG-10', 'tgl_lahir_siswa' => '2005-10-01', 'tempat_lahir_siswa' => 'Solo', 'gender_siswa' => 'Laki-laki', 'goldar_siswa' => 'B', 'no_hp_siswa' => '0860', 'alamat_jalan_siswa' => 'Jl. J', 'rt_siswa' => '10', 'rw_siswa' => '10', 'kelurahan_siswa' => 10, 'kecamatan_siswa' => 'J', 'kota_kab_siswa' => 'Solo', 'provinsi_siswa' => 'Jateng', 'kode_pos_siswa' => '57', 'nik_siswa' => '10', 'nama_ortu_siswa' => 'Ortu 10', 'nik_ortu_siswa' => '10', 'peran_ortu_siswa' => 'Ayah', 'tahun_lulus' => '2024', 'password_siswa' => Hash::make('siswa123'), 'is_delete' => 0],
        ];
        foreach ($siswa as $s) {
            DB::table('mst_siswa')->updateOrInsert(['id_siswa_tetap' => $s['id_siswa_tetap']], $s);
        }

        // =============================================================
        // 5. MASTER BUKU & LAPORAN PKL (10 Data)
        // =============================================================
        $buku = [
            ['ISBN' => '978-01', 'judul_koleksi' => 'Matematika Dasar', 'pengarang' => 'Dr. Abdurrahman', 'penerbit' => 'Erlangga', 'tahun' => '2022', 'nb_koleksi' => 101, 'tgl_masuk_koleksi' => '2024-01-01', 'jumlah_ekslempar' => 10, 'jumlah_halaman' => 200, 'ukuran_buku' => 'A5', 'bibliografi' => '-', 'indeks_awal_akhir' => 0, 'keterangan_buku' => 'Wajib X', 'no_rak_buku' => 'A1', 'id_ref_koleksi' => 1, 'is_delete' => 0],
            ['ISBN' => '978-02', 'judul_koleksi' => 'Laskar Pelangi', 'pengarang' => 'Andrea Hirata', 'penerbit' => 'Bentang', 'tahun' => '2005', 'nb_koleksi' => 202, 'tgl_masuk_koleksi' => '2024-01-01', 'jumlah_ekslempar' => 10, 'jumlah_halaman' => 500, 'ukuran_buku' => 'A5', 'bibliografi' => '-', 'indeks_awal_akhir' => 0, 'keterangan_buku' => 'Populer', 'no_rak_buku' => 'B1', 'id_ref_koleksi' => 2, 'is_delete' => 0],
            ['ISBN' => '978-03', 'judul_koleksi' => 'Laravel 11 Guide', 'pengarang' => 'Taylor Otwell', 'penerbit' => 'OReilly', 'tahun' => '2024', 'nb_koleksi' => 303, 'tgl_masuk_koleksi' => '2024-01-01', 'jumlah_ekslempar' => 10, 'jumlah_halaman' => 350, 'ukuran_buku' => 'A5', 'bibliografi' => 'Ada', 'indeks_awal_akhir' => 1, 'keterangan_buku' => 'Tech', 'no_rak_buku' => 'C1', 'id_ref_koleksi' => 3, 'is_delete' => 0],
            ['ISBN' => 'PKL-04', 'judul_koleksi' => 'Laporan Jaringan LAN', 'pengarang' => 'Siswa KitaBaca', 'penerbit' => 'SMKN 1', 'tahun' => '2023', 'nb_koleksi' => 404, 'tgl_masuk_koleksi' => '2024-01-01', 'jumlah_ekslempar' => 10, 'jumlah_halaman' => 80, 'ukuran_buku' => 'A4', 'bibliografi' => 'Ada', 'indeks_awal_akhir' => 0, 'keterangan_buku' => 'PKL', 'no_rak_buku' => 'D1', 'id_ref_koleksi' => 4, 'is_delete' => 0],
            ['ISBN' => '978-05', 'judul_koleksi' => 'Fisika Kuantum', 'pengarang' => 'Einstein', 'penerbit' => 'Global', 'tahun' => '2021', 'nb_koleksi' => 505, 'tgl_masuk_koleksi' => '2024-01-01', 'jumlah_ekslempar' => 10, 'jumlah_halaman' => 400, 'ukuran_buku' => 'A5', 'bibliografi' => '-', 'indeks_awal_akhir' => 1, 'keterangan_buku' => 'Sains', 'no_rak_buku' => 'E1', 'id_ref_koleksi' => 5, 'is_delete' => 0],
            ['ISBN' => '978-06', 'judul_koleksi' => 'Sejarah Majapahit', 'pengarang' => 'Pramoedya', 'penerbit' => 'Nusantara', 'tahun' => '2019', 'nb_koleksi' => 606, 'tgl_masuk_koleksi' => '2024-01-01', 'jumlah_ekslempar' => 10, 'jumlah_halaman' => 600, 'ukuran_buku' => 'A5', 'bibliografi' => '-', 'indeks_awal_akhir' => 0, 'keterangan_buku' => 'History', 'no_rak_buku' => 'F1', 'id_ref_koleksi' => 6, 'is_delete' => 0],
            ['ISBN' => '978-07', 'judul_koleksi' => 'Teori Warna', 'pengarang' => 'Da Vinci', 'penerbit' => 'ArtBook', 'tahun' => '2020', 'nb_koleksi' => 707, 'tgl_masuk_koleksi' => '2024-01-01', 'jumlah_ekslempar' => 10, 'jumlah_halaman' => 150, 'ukuran_buku' => 'A5', 'bibliografi' => '-', 'indeks_awal_akhir' => 0, 'keterangan_buku' => 'Art', 'no_rak_buku' => 'G1', 'id_ref_koleksi' => 7, 'is_delete' => 0],
            ['ISBN' => '978-08', 'judul_koleksi' => 'Steve Jobs', 'pengarang' => 'Walter I', 'penerbit' => 'Simon', 'tahun' => '2011', 'nb_koleksi' => 808, 'tgl_masuk_koleksi' => '2024-01-01', 'jumlah_ekslempar' => 10, 'jumlah_halaman' => 700, 'ukuran_buku' => 'A5', 'bibliografi' => 'Ada', 'indeks_awal_akhir' => 1, 'keterangan_buku' => 'Bio', 'no_rak_buku' => 'H1', 'id_ref_koleksi' => 8, 'is_delete' => 0],
            ['ISBN' => '978-09', 'judul_koleksi' => 'Naruto Vol 1', 'pengarang' => 'Masashi K', 'penerbit' => 'Shonen', 'tahun' => '1999', 'nb_koleksi' => 909, 'tgl_masuk_koleksi' => '2024-01-01', 'jumlah_ekslempar' => 10, 'jumlah_halaman' => 180, 'ukuran_buku' => 'B6', 'bibliografi' => '-', 'indeks_awal_akhir' => 0, 'keterangan_buku' => 'Manga', 'no_rak_buku' => 'I1', 'id_ref_koleksi' => 9, 'is_delete' => 0],
            ['ISBN' => '978-10', 'judul_koleksi' => 'Dunia Binatang', 'pengarang' => 'Nat Geo', 'penerbit' => 'NatGeo', 'tahun' => '2023', 'nb_koleksi' => 1010, 'tgl_masuk_koleksi' => '2024-01-01', 'jumlah_ekslempar' => 10, 'jumlah_halaman' => 300, 'ukuran_buku' => 'A4', 'bibliografi' => 'Ada', 'indeks_awal_akhir' => 1, 'keterangan_buku' => 'Ensiklopedia', 'no_rak_buku' => 'J1', 'id_ref_koleksi' => 10, 'is_delete' => 0],
        ];
        foreach ($buku as $b) {
            DB::table('mst_koleksi_buku')->updateOrInsert(['ISBN' => $b['ISBN']], $b);
        }

        // =============================================================
        // 6. FISIK BUKU (10 Data)
        // =============================================================
        $cp = [
            ['id_cp_koleksi' => 101, 'status_buku' => 'Dipinjam', 'ISBN' => '978-01', 'id_mst_laporan' => null],
            ['id_cp_koleksi' => 102, 'status_buku' => 'Tersedia', 'ISBN' => '978-02', 'id_mst_laporan' => null],
            ['id_cp_koleksi' => 103, 'status_buku' => 'Dipinjam', 'ISBN' => '978-03', 'id_mst_laporan' => null],
            ['id_cp_koleksi' => 104, 'status_buku' => 'Tersedia', 'ISBN' => 'PKL-04', 'id_mst_laporan' => 1],
            ['id_cp_koleksi' => 105, 'status_buku' => 'Dipinjam', 'ISBN' => '978-05', 'id_mst_laporan' => null],
            ['id_cp_koleksi' => 106, 'status_buku' => 'Tersedia', 'ISBN' => '978-06', 'id_mst_laporan' => null],
            ['id_cp_koleksi' => 107, 'status_buku' => 'Dipinjam', 'ISBN' => '978-07', 'id_mst_laporan' => null],
            ['id_cp_koleksi' => 108, 'status_buku' => 'Tersedia', 'ISBN' => '978-08', 'id_mst_laporan' => null],
            ['id_cp_koleksi' => 109, 'status_buku' => 'Dipinjam', 'ISBN' => '978-09', 'id_mst_laporan' => null],
            ['id_cp_koleksi' => 110, 'status_buku' => 'Tersedia', 'ISBN' => '978-10', 'id_mst_laporan' => null],
        ];
        foreach ($cp as $c) {
            DB::table('cp_koleksi')->updateOrInsert(['id_cp_koleksi' => $c['id_cp_koleksi']], $c);
        }

        // =============================================================
        // 7. TRANSAKSI PEMINJAMAN (10 Data)
        // =============================================================
        $adminNip = '19850101201001';
        $peminjaman = [
            ['id_peminjaman' => 1001, 'tgl_peminjaman' => '2024-04-01', 'tgl_harus_kembali' => '2024-04-08', 'tgl_kembali' => null, 'status_peminjaman' => 'Dipinjam', 'kondisi_buku' => 'Baik', 'keterangan_peminjaman' => 'Test', 'denda_peminjaman' => 0, 'id_cp_koleksi' => 101, 'id_siswa_tetap' => 1, 'nip_karyawan' => $adminNip],
            ['id_peminjaman' => 1002, 'tgl_peminjaman' => '2024-04-02', 'tgl_harus_kembali' => '2024-04-09', 'tgl_kembali' => '2024-04-09', 'status_peminjaman' => 'Selesai', 'kondisi_buku' => 'Baik', 'keterangan_peminjaman' => 'Test', 'denda_peminjaman' => 0, 'id_cp_koleksi' => 102, 'id_siswa_tetap' => 2, 'nip_karyawan' => $adminNip],
            ['id_peminjaman' => 1003, 'tgl_peminjaman' => '2024-04-03', 'tgl_harus_kembali' => '2024-04-10', 'tgl_kembali' => null, 'status_peminjaman' => 'Dipinjam', 'kondisi_buku' => 'Baik', 'keterangan_peminjaman' => 'Test', 'denda_peminjaman' => 0, 'id_cp_koleksi' => 103, 'id_siswa_tetap' => 3, 'nip_karyawan' => $adminNip],
            ['id_peminjaman' => 1004, 'tgl_peminjaman' => '2024-04-04', 'tgl_harus_kembali' => '2024-04-11', 'tgl_kembali' => '2024-04-11', 'status_peminjaman' => 'Selesai', 'kondisi_buku' => 'Baik', 'keterangan_peminjaman' => 'Test', 'denda_peminjaman' => 0, 'id_cp_koleksi' => 104, 'id_siswa_tetap' => 4, 'nip_karyawan' => $adminNip],
            ['id_peminjaman' => 1005, 'tgl_peminjaman' => '2024-04-05', 'tgl_harus_kembali' => '2024-04-12', 'tgl_kembali' => null, 'status_peminjaman' => 'Dipinjam', 'kondisi_buku' => 'Baik', 'keterangan_peminjaman' => 'Test', 'denda_peminjaman' => 0, 'id_cp_koleksi' => 105, 'id_siswa_tetap' => 5, 'nip_karyawan' => $adminNip],
            ['id_peminjaman' => 1006, 'tgl_peminjaman' => '2024-04-06', 'tgl_harus_kembali' => '2024-04-13', 'tgl_kembali' => '2024-04-13', 'status_peminjaman' => 'Selesai', 'kondisi_buku' => 'Baik', 'keterangan_peminjaman' => 'Test', 'denda_peminjaman' => 0, 'id_cp_koleksi' => 106, 'id_siswa_tetap' => 6, 'nip_karyawan' => $adminNip],
            ['id_peminjaman' => 1007, 'tgl_peminjaman' => '2024-04-07', 'tgl_harus_kembali' => '2024-04-14', 'tgl_kembali' => null, 'status_peminjaman' => 'Dipinjam', 'kondisi_buku' => 'Baik', 'keterangan_peminjaman' => 'Test', 'denda_peminjaman' => 0, 'id_cp_koleksi' => 107, 'id_siswa_tetap' => 7, 'nip_karyawan' => $adminNip],
            ['id_peminjaman' => 1008, 'tgl_peminjaman' => '2024-04-08', 'tgl_harus_kembali' => '2024-04-15', 'tgl_kembali' => '2024-04-15', 'status_peminjaman' => 'Selesai', 'kondisi_buku' => 'Baik', 'keterangan_peminjaman' => 'Test', 'denda_peminjaman' => 0, 'id_cp_koleksi' => 108, 'id_siswa_tetap' => 8, 'nip_karyawan' => $adminNip],
            ['id_peminjaman' => 1009, 'tgl_peminjaman' => '2024-04-09', 'tgl_harus_kembali' => '2024-04-16', 'tgl_kembali' => null, 'status_peminjaman' => 'Dipinjam', 'kondisi_buku' => 'Baik', 'keterangan_peminjaman' => 'Test', 'denda_peminjaman' => 0, 'id_cp_koleksi' => 109, 'id_siswa_tetap' => 9, 'nip_karyawan' => $adminNip],
            ['id_peminjaman' => 1010, 'tgl_peminjaman' => '2024-04-10', 'tgl_harus_kembali' => '2024-04-17', 'tgl_kembali' => '2024-04-17', 'status_peminjaman' => 'Selesai', 'kondisi_buku' => 'Baik', 'keterangan_peminjaman' => 'Test', 'denda_peminjaman' => 0, 'id_cp_koleksi' => 110, 'id_siswa_tetap' => 10, 'nip_karyawan' => $adminNip],
        ];
        foreach ($peminjaman as $p) {
            DB::table('tr_peminjaman')->updateOrInsert(['id_peminjaman' => $p['id_peminjaman']], $p);
        }

        // Nyalakan kembali proteksi FK
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
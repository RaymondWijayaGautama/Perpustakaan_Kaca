<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. DATA KARYAWAN (3 Data) ---
        DB::table('mst_karyawan')->insert([
            [
                'nip_karyawan' => '19850101201001',
                'nama_karyawan' => 'Sari Wijaya',
                'nama_lengkap_gelar' => 'Sari Wijaya, S.I.Pust.',
                'golongan_karyawan' => 'III/b',
                'jabatan_fungsional' => 'Pustakawan', // ADMIN
                'tanggal_masuk' => '2010-01-01',
                'status_kepegawaian' => 'Tetap',
                'nik_karyawan' => '3404010101850001',
                'tempat_lahir_karyawan' => 'Yogyakarta',
                'gender_karyawan' => 'Perempuan',
                'tgl_lahir_karyawan' => '1985-01-01',
                'alamat_karyawan' => 'Sleman, DIY',
                'no_hp_karyawan' => '081222333444',
                'email_karyawan' => 'admin@kitabaca.com',
                'password_karyawan' => Hash::make('admin123'),
                'pend_terakhir_karyawan' => 'S1 Perpustakaan',
                'prodi_karyawan' => 'Ilmu Perpustakaan',
                'sertifikat_pendidik' => 'Ada',
                'link_foto_karyawan' => 'admin.jpg',
                'is_delete' => 0
            ],
            [
                'nip_karyawan' => '19900505201502',
                'nama_karyawan' => 'Budi Santoso',
                'nama_lengkap_gelar' => 'Budi Santoso, S.Pd.',
                'golongan_karyawan' => 'III/a',
                'jabatan_fungsional' => 'Guru', // ANGGOTA
                'tanggal_masuk' => '2015-05-05',
                'status_kepegawaian' => 'Kontrak',
                'nik_karyawan' => '3404010505900002',
                'tempat_lahir_karyawan' => 'Bantul',
                'gender_karyawan' => 'Laki-laki',
                'tgl_lahir_karyawan' => '1990-05-05',
                'alamat_karyawan' => 'Bantul, DIY',
                'no_hp_karyawan' => '081333444555',
                'email_karyawan' => 'budi@kitabaca.com',
                'password_karyawan' => Hash::make('password123'),
                'pend_terakhir_karyawan' => 'S1 Pendidikan',
                'prodi_karyawan' => 'Matematika',
                'sertifikat_pendidik' => 'Ada',
                'link_foto_karyawan' => 'budi.jpg',
                'is_delete' => 0
            ],
            [
                'nip_karyawan' => '19920808201803',
                'nama_karyawan' => 'Ani Lestari',
                'nama_lengkap_gelar' => 'Ani Lestari, M.Kom.',
                'golongan_karyawan' => 'III/a',
                'jabatan_fungsional' => 'Guru', // ANGGOTA
                'tanggal_masuk' => '2018-08-08',
                'status_kepegawaian' => 'Tetap',
                'nik_karyawan' => '3404010808920003',
                'tempat_lahir_karyawan' => 'Gunungkidul',
                'gender_karyawan' => 'Perempuan',
                'tgl_lahir_karyawan' => '1992-08-08',
                'alamat_karyawan' => 'Gunungkidul, DIY',
                'no_hp_karyawan' => '081444555666',
                'email_karyawan' => 'ani@kitabaca.com',
                'password_karyawan' => Hash::make('password123'),
                'pend_terakhir_karyawan' => 'S2 Informatika',
                'prodi_karyawan' => 'Teknik Informatika',
                'sertifikat_pendidik' => 'Tidak',
                'link_foto_karyawan' => 'ani.jpg',
                'is_delete' => 0
            ],
        ]);

        // --- 2. DATA SISWA (4 Data) ---
        DB::table('mst_siswa')->insert([
            [
                'nisn_siswa' => '0056781234',
                'nama_siswa_tetap' => 'Rizky Pratama',
                'tgl_lahir_siswa' => '2005-03-12',
                'tempat_lahir_siswa' => 'Yogyakarta',
                'gender_siswa' => 'Laki-laki',
                'goldar_siswa' => 'O',
                'no_hp_siswa' => '085700001111',
                'alamat_jalan_siswa' => 'Jl. Magelang KM 5',
                'rt_siswa' => '01', 'rw_siswa' => '02',
                'kelurahan_siswa' => 1, 'kecamatan_siswa' => 'Mlati',
                'kota_kab_siswa' => 'Sleman', 'provinsi_siswa' => 'DIY',
                'kode_pos_siswa' => '55284',
                'nik_siswa' => '3404123456780001',
                'nama_ortu_siswa' => 'Herman',
                'nik_ortu_siswa' => '3404123456789999',
                'peran_ortu_siswa' => 'Ayah',
                'tahun_lulus' => '2024',
                'password_siswa' => Hash::make('siswa123'),
                'is_delete' => 0, 'kode_calon_siswa' => ''
            ],
            [
                'nisn_siswa' => '0067892345',
                'nama_siswa_tetap' => 'Siti Aminah',
                'tgl_lahir_siswa' => '2006-07-20',
                'tempat_lahir_siswa' => 'Bantul',
                'gender_siswa' => 'Perempuan',
                'goldar_siswa' => 'A',
                'no_hp_siswa' => '085711112222',
                'alamat_jalan_siswa' => 'Jl. Imogiri Barat',
                'rt_siswa' => '05', 'rw_siswa' => '01',
                'kelurahan_siswa' => 2, 'kecamatan_siswa' => 'Sewon',
                'kota_kab_siswa' => 'Bantul', 'provinsi_siswa' => 'DIY',
                'kode_pos_siswa' => '55188',
                'nik_siswa' => '3402123456780002',
                'nama_ortu_siswa' => 'Fatimah',
                'nik_ortu_siswa' => '3402123456789998',
                'peran_ortu_siswa' => 'Ibu',
                'tahun_lulus' => '2025',
                'password_siswa' => Hash::make('siswa123'),
                'is_delete' => 0, 'kode_calon_siswa' => ''
            ],
            [
                'nisn_siswa' => '0078903456',
                'nama_siswa_tetap' => 'Kevin Sanjaya',
                'tgl_lahir_siswa' => '2007-11-30',
                'tempat_lahir_siswa' => 'Kulon Progo',
                'gender_siswa' => 'Laki-laki',
                'goldar_siswa' => 'B',
                'no_hp_siswa' => '085722223333',
                'alamat_jalan_siswa' => 'Jl. Wates KM 10',
                'rt_siswa' => '10', 'rw_siswa' => '04',
                'kelurahan_siswa' => 3, 'kecamatan_siswa' => 'Sentolo',
                'kota_kab_siswa' => 'Kulon Progo', 'provinsi_siswa' => 'DIY',
                'kode_pos_siswa' => '55664',
                'nik_siswa' => '3401123456780003',
                'nama_ortu_siswa' => 'Agus',
                'nik_ortu_siswa' => '3401123456789997',
                'peran_ortu_siswa' => 'Ayah',
                'tahun_lulus' => '2026',
                'password_siswa' => Hash::make('siswa123'),
                'is_delete' => 0, 'kode_calon_siswa' => ''
            ],
            [
                'nisn_siswa' => '883726',
                'nama_siswa_tetap' => 'Yu Ji-Min',
                'tgl_lahir_siswa' => '2006-04-11',
                'tempat_lahir_siswa' => 'Seoul',
                'gender_siswa' => 'Perempuan',
                'goldar_siswa' => 'B',
                'no_hp_siswa' => '085733334444',
                'alamat_jalan_siswa' => 'Apartment Karita',
                'rt_siswa' => '01', 'rw_siswa' => '01',
                'kelurahan_siswa' => 4, 'kecamatan_siswa' => 'Seoul',
                'kota_kab_siswa' => 'Seoul', 'provinsi_siswa' => 'Seoul',
                'kode_pos_siswa' => '12345',
                'nik_siswa' => '3400123456780004',
                'nama_ortu_siswa' => 'Karin',
                'nik_ortu_siswa' => '3400123456789996',
                'peran_ortu_siswa' => 'Ibu',
                'tahun_lulus' => '2025',
                'password_siswa' => Hash::make('siswa123'),
                'is_delete' => 0, 'kode_calon_siswa' => ''
            ],
        ]);

        // --- 3. DATA BUKU (3 Data) ---
        // Seed kategori dulu agar id_ref_koleksi tidak error
        DB::table('ref_koleksi')->insertOrIgnore([
            ['id_ref_koleksi' => 1, 'deskripsi' => 'Buku Pelajaran', 'is_delete' => 0],
            ['id_ref_koleksi' => 2, 'deskripsi' => 'Novel', 'is_delete' => 0],
        ]);

        DB::table('mst_koleksi_buku')->insert([
            [
                'ISBN' => '978-602-03-1234-1',
                'judul_koleksi' => 'Matematika SMK X',
                'pengarang' => 'Dr. Abdurrahman',
                'penerbit' => 'Erlangga',
                'tahun' => '2022',
                'nb_koleksi' => 101,
                'tgl_masuk_koleksi' => '2022-01-15',
                'jumlah_ekslempar' => 50,
                'jumlah_halaman' => 240,
                'ukuran_buku' => '21x29 cm',
                'bibliografi' => '-',
                'indeks_awal_akhir' => 0,
                'keterangan_buku' => 'Buku wajib kelas X',
                'no_rak_buku' => 'A-01',
                'id_ref_koleksi' => 1,
                'is_delete' => 0
            ],
            [
                'ISBN' => '978-602-03-5678-2',
                'judul_koleksi' => 'Laskar Pelangi',
                'pengarang' => 'Andrea Hirata',
                'penerbit' => 'Bentang Pustaka',
                'tahun' => '2005',
                'nb_koleksi' => 202,
                'tgl_masuk_koleksi' => '2023-02-10',
                'jumlah_ekslempar' => 10,
                'jumlah_halaman' => 529,
                'ukuran_buku' => '13x20 cm',
                'bibliografi' => '-',
                'indeks_awal_akhir' => 0,
                'keterangan_buku' => 'Koleksi fiksi populer',
                'no_rak_buku' => 'F-12',
                'id_ref_koleksi' => 2,
                'is_delete' => 0
            ],
            [
                'ISBN' => '978-602-03-9999-3',
                'judul_koleksi' => 'Web Design Guide',
                'pengarang' => 'John Doe',
                'penerbit' => 'Informatika',
                'tahun' => '2023',
                'nb_koleksi' => 303,
                'tgl_masuk_koleksi' => '2024-01-01',
                'jumlah_ekslempar' => 5,
                'jumlah_halaman' => 150,
                'ukuran_buku' => '15x23 cm',
                'bibliografi' => 'Ada',
                'indeks_awal_akhir' => 1,
                'keterangan_buku' => 'Panduan praktik TKJ',
                'no_rak_buku' => 'T-05',
                'id_ref_koleksi' => 1,
                'is_delete' => 0
            ],
        ]);
    }
}
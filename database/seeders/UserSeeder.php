<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
// Import Model jika belum ada
use App\Models\Karyawan; 

class UserSeeder extends Seeder
{
    public function run()
    {
        // 1. Data Akun Pustakawan (Menggunakan Eloquent updateOrCreate)
        // Parameter pertama: Kolom unik untuk pengecekan
        // Parameter kedua: Data yang akan dimasukkan/diperbarui
        \App\Models\Karyawan::updateOrCreate(
            ['nip_karyawan' => '19900101'], 
            [
                'nama_karyawan' => 'Budi Pustakawan',
                'nama_lengkap_gelar' => 'Budi S.Kom',
                'golongan_karyawan' => 'III/A',
                'jabatan_fungsional' => 'Pustakawan Ahli',
                'tanggal_masuk' => '2020-01-01',
                'status_kepegawaian' => 'Tetap',
                'nik_karyawan' => '1234567890123456',
                'tempat_lahir_karyawan' => 'Jakarta',
                'gender_karyawan' => 'Laki-laki',
                'tgl_lahir_karyawan' => '1990-01-01',
                'alamat_karyawan' => 'Jl. Merdeka No. 10',
                'no_hp_karyawan' => '08123456789',
                'email_karyawan' => 'pustakawan@perpus.com',
                'password_karyawan' => Hash::make('password123'),
                'pend_terakhir_karyawan' => 'S1 Teknik Informatika',
                'prodi_karyawan' => 'Informatika',
                'sertifikat_pendidik' => '-',
                'link_foto_karyawan' => 'default.jpg',
                'is_delete' => 0
            ]
        );

        // 2. Data Akun Anggota (Menggunakan Query Builder updateOrInsert)
        // Cocok jika Anda tidak memiliki Model Siswa
        DB::table('mst_siswa')->updateOrInsert(
            ['id_siswa_tetap' => 1], // Pengecekan ID Unik
            [
                'kode_calon_siswa' => 'REG2024001',
                'nisn_siswa' => '883726',
                'nama_siswa_tetap' => 'Yu Ji-Min',
                'tgl_lahir_siswa' => '2008-04-11',
                'tempat_lahir_siswa' => 'Suwon',
                'gender_siswa' => 'Perempuan',
                'goldar_siswa' => 'B',
                'no_hp_siswa' => '08987654321',
                'alamat_jalan_siswa' => 'Jl. Kwangya No. 1',
                'rt_siswa' => '01',
                'rw_siswa' => '02',
                'kelurahan_siswa' => 101,
                'kecamatan_siswa' => 'Gangnam',
                'kota_kab_siswa' => 'Seoul',
                'provinsi_siswa' => 'Gyeonggi',
                'kode_pos_siswa' => '12345',
                'nik_siswa' => '3211234567890',
                'nama_ortu_siswa' => 'Orang Tua',
                'nik_ortu_siswa' => '3211234567891',
                'peran_ortu_siswa' => 'Ayah',
                'tahun_lulus' => '2026',
                'password_siswa' => Hash::make('siswa123'),
                'is_delete' => 0
            ]
        );
    }
}
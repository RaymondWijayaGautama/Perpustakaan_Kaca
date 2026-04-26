-- Dummy data untuk database si_smk_boda3.
-- Disesuaikan dari seeder lama project Perpustakaan Kaca ke struktur dump si_smk_boda_130426.sql.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";
START TRANSACTION;

INSERT INTO `ref_koleksi`
(`ID_REF_KOLEKSI`, `NO_KATEGORI_BUKU`, `DESKRIPSI_KATEGORI`, `IS_DELETE`) VALUES
(1, 'KB001', 'Buku Pelajaran', 0),
(2, 'KB002', 'Novel & Fiksi', 0),
(3, 'KB003', 'Teknologi Informasi', 0),
(4, 'KB004', 'Laporan PKL', 0),
(5, 'KB005', 'Sains & Alam', 0),
(6, 'KB006', 'Sejarah & Budaya', 0),
(7, 'KB007', 'Seni & Desain', 0),
(8, 'KB008', 'Biografi', 0),
(9, 'KB009', 'Komik & Manga', 0),
(10, 'KB010', 'Ensiklopedia', 0)
ON DUPLICATE KEY UPDATE
  `NO_KATEGORI_BUKU` = VALUES(`NO_KATEGORI_BUKU`),
  `DESKRIPSI_KATEGORI` = VALUES(`DESKRIPSI_KATEGORI`),
  `IS_DELETE` = VALUES(`IS_DELETE`);

INSERT INTO `mst_karyawan`
(`NIP_KARYAWAN`, `ID_UNIT`, `NAMA_KARYAWAN`, `NAMA_LENGKAP_GELAR`, `GOLONGAN_KARYAWAN`, `JABATAN_FUNGSIONAL`, `TANGGAL_MASUK`, `STATUS_KEPEGAWAIAN`, `NIK_KARYAWAN`, `TEMPAT_LAHIR_KARYAWAN`, `GENDER_KARYAWAN`, `TGL_LAHIR_KARYAWAN`, `ALAMAT_KARYAWAN`, `NO_HP_KARYAWAN`, `EMAIL_KARYAWAN`, `PASSWORD_KARYAWAN`, `PEND_TERAKHIR_KARYAWAN`, `PRODI_KARYAWAN`, `SERTIFIKAT_PENDIDIK`, `LINK_FOTO_KARYAWAN`, `IS_DELETE`) VALUES
('19850101201001', NULL, 'Budi Pustakawan', 'Budi S.Kom', 'III/A', 'Pustakawan', '2020-01-01 00:00:00', 'Tetap', '1234567890123456', 'Jakarta', 'Laki-laki', '1990-01-01 00:00:00', 'Jl. Merdeka No. 10', '08123456789', 'pustakawan@perpus.com', '$2y$10$sWim4iR0IEBu.KA4dDI1ZuEAiG9H85/Tzigrp32GrFuMFN6GkeHpu', 'S1 Teknik Informatika', 'Informatika', '-', 'default.jpg', 0),
('19880101201401', NULL, 'Siti Rahma', 'Siti Rahma S.Pd', 'III/B', 'Guru', '2014-07-01 00:00:00', 'Tetap', '3404010101880001', 'Yogyakarta', 'Perempuan', '1988-01-01 00:00:00', 'Jl. Kaliurang Km 8', '081200001001', 'siti.rahma@perpus.com', '$2y$10$im3fHOhiTVMyBfTbMI4Oz.3H9Kii7NwsdTfaRH7/k/e4N0FXLRHwS', 'S1 Pendidikan', 'Pendidikan Bahasa', '-', 'default.jpg', 0),
('19890202201502', NULL, 'Agus Prasetyo', 'Agus Prasetyo S.Kom', 'III/B', 'Guru', '2015-07-01 00:00:00', 'Tetap', '3404010202890002', 'Sleman', 'Laki-laki', '1989-02-02 00:00:00', 'Jl. Magelang Km 6', '081200001002', 'agus.prasetyo@perpus.com', '$2y$10$im3fHOhiTVMyBfTbMI4Oz.3H9Kii7NwsdTfaRH7/k/e4N0FXLRHwS', 'S1 Informatika', 'Teknik Informatika', '-', 'default.jpg', 0),
('19900303201603', NULL, 'Rina Wulandari', 'Rina Wulandari S.Pd', 'III/A', 'Guru', '2016-07-01 00:00:00', 'Tetap', '3404010303900003', 'Bantul', 'Perempuan', '1990-03-03 00:00:00', 'Jl. Bantul Km 5', '081200001003', 'rina.wulandari@perpus.com', '$2y$10$im3fHOhiTVMyBfTbMI4Oz.3H9Kii7NwsdTfaRH7/k/e4N0FXLRHwS', 'S1 Pendidikan', 'Pendidikan Matematika', '-', 'default.jpg', 0)
ON DUPLICATE KEY UPDATE
  `ID_UNIT` = VALUES(`ID_UNIT`),
  `NAMA_KARYAWAN` = VALUES(`NAMA_KARYAWAN`),
  `NAMA_LENGKAP_GELAR` = VALUES(`NAMA_LENGKAP_GELAR`),
  `GOLONGAN_KARYAWAN` = VALUES(`GOLONGAN_KARYAWAN`),
  `JABATAN_FUNGSIONAL` = VALUES(`JABATAN_FUNGSIONAL`),
  `TANGGAL_MASUK` = VALUES(`TANGGAL_MASUK`),
  `STATUS_KEPEGAWAIAN` = VALUES(`STATUS_KEPEGAWAIAN`),
  `NIK_KARYAWAN` = VALUES(`NIK_KARYAWAN`),
  `TEMPAT_LAHIR_KARYAWAN` = VALUES(`TEMPAT_LAHIR_KARYAWAN`),
  `GENDER_KARYAWAN` = VALUES(`GENDER_KARYAWAN`),
  `TGL_LAHIR_KARYAWAN` = VALUES(`TGL_LAHIR_KARYAWAN`),
  `ALAMAT_KARYAWAN` = VALUES(`ALAMAT_KARYAWAN`),
  `NO_HP_KARYAWAN` = VALUES(`NO_HP_KARYAWAN`),
  `EMAIL_KARYAWAN` = VALUES(`EMAIL_KARYAWAN`),
  `PASSWORD_KARYAWAN` = VALUES(`PASSWORD_KARYAWAN`),
  `PEND_TERAKHIR_KARYAWAN` = VALUES(`PEND_TERAKHIR_KARYAWAN`),
  `PRODI_KARYAWAN` = VALUES(`PRODI_KARYAWAN`),
  `SERTIFIKAT_PENDIDIK` = VALUES(`SERTIFIKAT_PENDIDIK`),
  `LINK_FOTO_KARYAWAN` = VALUES(`LINK_FOTO_KARYAWAN`),
  `IS_DELETE` = VALUES(`IS_DELETE`);

INSERT INTO `mst_siswa`
(`ID_SISWA_TETAP`, `ID_PENDAFTARAN`, `KODE_TA`, `KODE_CALON_SISWA`, `NISN_SISWA`, `NAMA_SISWA_TETAP`, `TGL_LAHIR_SISWA`, `TEMPAT_LAHIR_SISWA`, `GENDER_SISWA`, `GOLDAR_SISWA`, `NO_HP_SISWA`, `ALAMAT_JALAN_SISWA`, `RT_SISWA`, `RW_SISWA`, `KELURAHAN_SISWA`, `KECAMATAN_SISWA`, `KOTA_KAB_SISWA`, `PROVINSI_SISWA`, `KODE_POS_SISWA`, `NIK_SISWA`, `TAHUN_LULUS`, `PASSWORD_SISWA`, `NAMA_AYAH_SISWA`, `NAMA_IBU_SISWA`, `NAMA_WALI_SISWA`, `PEKERJAAN_AYAH_SISWA`, `PEKERJAAN_IBU_SISWA`, `PEKERJAAN_WALI_SISWA`, `IS_DELETE`) VALUES
(1, NULL, NULL, 'REG-2024-001', '0057242952', 'Elsy Laurens', '2007-01-01 00:00:00', 'Yogyakarta', 'Laki-laki', 'A', '0877000001', 'Dusun 1, Sleman', '01', '01', 'Caturtunggal', 'Depok', 'Sleman', 'DIY', '55281', '3404010101070001', '2028', '$2y$10$3tmlxYKwkZuu/LfTnTltS.C6parwQ3lNPI6FIJ0hcrllK6fW83Lsy', 'Wali Elsy', 'Ibu Elsy', NULL, 'Wiraswasta', 'Ibu Rumah Tangga', NULL, 0),
(2, NULL, NULL, 'REG-2024-002', '0064101551', 'Rifa Amalia', '2007-01-01 00:00:00', 'Yogyakarta', 'Perempuan', 'A', '0877000002', 'Dusun 2, Sleman', '01', '01', 'Caturtunggal', 'Depok', 'Sleman', 'DIY', '55281', '3404010101070002', '2028', '$2y$10$3tmlxYKwkZuu/LfTnTltS.C6parwQ3lNPI6FIJ0hcrllK6fW83Lsy', 'Wali Rifa', 'Ibu Rifa', NULL, 'Wiraswasta', 'Ibu Rumah Tangga', NULL, 0),
(3, NULL, NULL, 'REG-2024-003', '0055875579', 'Budi Santoso', '2007-01-01 00:00:00', 'Yogyakarta', 'Laki-laki', 'A', '0877000003', 'Dusun 3, Sleman', '01', '01', 'Caturtunggal', 'Depok', 'Sleman', 'DIY', '55281', '3404010101070003', '2028', '$2y$10$3tmlxYKwkZuu/LfTnTltS.C6parwQ3lNPI6FIJ0hcrllK6fW83Lsy', 'Wali Budi', 'Ibu Budi', NULL, 'Wiraswasta', 'Ibu Rumah Tangga', NULL, 0),
(4, NULL, NULL, 'REG-2024-004', '0057010721', 'Siti Aminah', '2007-01-01 00:00:00', 'Yogyakarta', 'Perempuan', 'A', '0877000004', 'Dusun 4, Sleman', '01', '01', 'Caturtunggal', 'Depok', 'Sleman', 'DIY', '55281', '3404010101070004', '2027', '$2y$10$3tmlxYKwkZuu/LfTnTltS.C6parwQ3lNPI6FIJ0hcrllK6fW83Lsy', 'Wali Siti', 'Ibu Siti', NULL, 'Wiraswasta', 'Ibu Rumah Tangga', NULL, 0),
(5, NULL, NULL, 'REG-2024-005', '0053630354', 'Andi Wijaya', '2007-01-01 00:00:00', 'Yogyakarta', 'Laki-laki', 'A', '0877000005', 'Dusun 5, Sleman', '01', '01', 'Caturtunggal', 'Depok', 'Sleman', 'DIY', '55281', '3404010101070005', '2027', '$2y$10$3tmlxYKwkZuu/LfTnTltS.C6parwQ3lNPI6FIJ0hcrllK6fW83Lsy', 'Wali Andi', 'Ibu Andi', NULL, 'Wiraswasta', 'Ibu Rumah Tangga', NULL, 0),
(6, NULL, NULL, 'REG-2024-006', '0045373919', 'Eka Putri', '2007-01-01 00:00:00', 'Yogyakarta', 'Perempuan', 'A', '0877000006', 'Dusun 6, Sleman', '01', '01', 'Caturtunggal', 'Depok', 'Sleman', 'DIY', '55281', '3404010101070006', '2026', '$2y$10$3tmlxYKwkZuu/LfTnTltS.C6parwQ3lNPI6FIJ0hcrllK6fW83Lsy', 'Wali Eka', 'Ibu Eka', NULL, 'Wiraswasta', 'Ibu Rumah Tangga', NULL, 0),
(7, NULL, NULL, 'REG-2024-007', '0052372577', 'Gede Bagus', '2007-01-01 00:00:00', 'Yogyakarta', 'Laki-laki', 'A', '0877000007', 'Dusun 7, Sleman', '01', '01', 'Caturtunggal', 'Depok', 'Sleman', 'DIY', '55281', '3404010101070007', '2026', '$2y$10$3tmlxYKwkZuu/LfTnTltS.C6parwQ3lNPI6FIJ0hcrllK6fW83Lsy', 'Wali Gede', 'Ibu Gede', NULL, 'Wiraswasta', 'Ibu Rumah Tangga', NULL, 0),
(8, NULL, NULL, 'REG-2024-008', '0058464857', 'Luh Putu', '2007-01-01 00:00:00', 'Yogyakarta', 'Perempuan', 'A', '0877000008', 'Dusun 8, Sleman', '01', '01', 'Caturtunggal', 'Depok', 'Sleman', 'DIY', '55281', '3404010101070008', '2027', '$2y$10$3tmlxYKwkZuu/LfTnTltS.C6parwQ3lNPI6FIJ0hcrllK6fW83Lsy', 'Wali Luh', 'Ibu Luh', NULL, 'Wiraswasta', 'Ibu Rumah Tangga', NULL, 0),
(9, NULL, NULL, 'REG-2024-009', '0043703466', 'Aditya Pratama', '2007-01-01 00:00:00', 'Yogyakarta', 'Laki-laki', 'A', '0877000009', 'Dusun 9, Sleman', '01', '01', 'Caturtunggal', 'Depok', 'Sleman', 'DIY', '55281', '3404010101070009', '2028', '$2y$10$3tmlxYKwkZuu/LfTnTltS.C6parwQ3lNPI6FIJ0hcrllK6fW83Lsy', 'Wali Aditya', 'Ibu Aditya', NULL, 'Wiraswasta', 'Ibu Rumah Tangga', NULL, 0),
(10, NULL, NULL, 'REG-2024-010', '0059055438', 'Rizky Ramadhan', '2007-01-01 00:00:00', 'Yogyakarta', 'Laki-laki', 'A', '0877000010', 'Dusun 10, Sleman', '01', '01', 'Caturtunggal', 'Depok', 'Sleman', 'DIY', '55281', '3404010101070010', '2026', '$2y$10$3tmlxYKwkZuu/LfTnTltS.C6parwQ3lNPI6FIJ0hcrllK6fW83Lsy', 'Wali Rizky', 'Ibu Rizky', NULL, 'Wiraswasta', 'Ibu Rumah Tangga', NULL, 0)
ON DUPLICATE KEY UPDATE
  `ID_PENDAFTARAN` = VALUES(`ID_PENDAFTARAN`),
  `KODE_TA` = VALUES(`KODE_TA`),
  `KODE_CALON_SISWA` = VALUES(`KODE_CALON_SISWA`),
  `NISN_SISWA` = VALUES(`NISN_SISWA`),
  `NAMA_SISWA_TETAP` = VALUES(`NAMA_SISWA_TETAP`),
  `TGL_LAHIR_SISWA` = VALUES(`TGL_LAHIR_SISWA`),
  `TEMPAT_LAHIR_SISWA` = VALUES(`TEMPAT_LAHIR_SISWA`),
  `GENDER_SISWA` = VALUES(`GENDER_SISWA`),
  `GOLDAR_SISWA` = VALUES(`GOLDAR_SISWA`),
  `NO_HP_SISWA` = VALUES(`NO_HP_SISWA`),
  `ALAMAT_JALAN_SISWA` = VALUES(`ALAMAT_JALAN_SISWA`),
  `RT_SISWA` = VALUES(`RT_SISWA`),
  `RW_SISWA` = VALUES(`RW_SISWA`),
  `KELURAHAN_SISWA` = VALUES(`KELURAHAN_SISWA`),
  `KECAMATAN_SISWA` = VALUES(`KECAMATAN_SISWA`),
  `KOTA_KAB_SISWA` = VALUES(`KOTA_KAB_SISWA`),
  `PROVINSI_SISWA` = VALUES(`PROVINSI_SISWA`),
  `KODE_POS_SISWA` = VALUES(`KODE_POS_SISWA`),
  `NIK_SISWA` = VALUES(`NIK_SISWA`),
  `TAHUN_LULUS` = VALUES(`TAHUN_LULUS`),
  `PASSWORD_SISWA` = VALUES(`PASSWORD_SISWA`),
  `NAMA_AYAH_SISWA` = VALUES(`NAMA_AYAH_SISWA`),
  `NAMA_IBU_SISWA` = VALUES(`NAMA_IBU_SISWA`),
  `NAMA_WALI_SISWA` = VALUES(`NAMA_WALI_SISWA`),
  `PEKERJAAN_AYAH_SISWA` = VALUES(`PEKERJAAN_AYAH_SISWA`),
  `PEKERJAAN_IBU_SISWA` = VALUES(`PEKERJAAN_IBU_SISWA`),
  `PEKERJAAN_WALI_SISWA` = VALUES(`PEKERJAAN_WALI_SISWA`),
  `IS_DELETE` = VALUES(`IS_DELETE`);

INSERT INTO `pendaf_pkl`
(`ID_PENDAF_PKL`, `KODE_TA`, `TGL_MULAI_PKL`, `TGL_SELESAI_PKL`, `STATUS_PKL`) VALUES
(1, NULL, '2024-01-08 00:00:00', '2024-04-08 00:00:00', 'Selesai'),
(2, NULL, '2024-01-08 00:00:00', '2024-04-08 00:00:00', 'Selesai'),
(3, NULL, '2024-01-08 00:00:00', '2024-04-08 00:00:00', 'Selesai'),
(4, NULL, '2024-01-08 00:00:00', '2024-04-08 00:00:00', 'Selesai'),
(5, NULL, '2024-01-08 00:00:00', '2024-04-08 00:00:00', 'Selesai'),
(6, NULL, '2024-01-08 00:00:00', '2024-04-08 00:00:00', 'Selesai'),
(7, NULL, '2024-01-08 00:00:00', '2024-04-08 00:00:00', 'Selesai'),
(8, NULL, '2024-01-08 00:00:00', '2024-04-08 00:00:00', 'Selesai'),
(9, NULL, '2024-01-08 00:00:00', '2024-04-08 00:00:00', 'Selesai'),
(10, NULL, '2024-01-08 00:00:00', '2024-04-08 00:00:00', 'Selesai')
ON DUPLICATE KEY UPDATE
  `KODE_TA` = VALUES(`KODE_TA`),
  `TGL_MULAI_PKL` = VALUES(`TGL_MULAI_PKL`),
  `TGL_SELESAI_PKL` = VALUES(`TGL_SELESAI_PKL`),
  `STATUS_PKL` = VALUES(`STATUS_PKL`);

INSERT INTO `mitra_pkl`
(`ID_MITRA_PKL`, `NAMA_MITRA_PKL`, `STATUS_MITRA_PKL`, `ALAMAT_MITRA_PKL`, `NO_TELP_MITRA_PKL`, `JARAK_TEMPAT_PKL`, `NO_MOU_PKL`) VALUES
(1, 'CV Kaca Digital', 'Aktif', 'Jl. Affandi No. 21, Sleman', '0274000001', '3 km', 'MOU/PKL/001/2024'),
(2, 'PT Media Edukasi', 'Aktif', 'Jl. Magelang Km 7, Sleman', '0274000002', '6 km', 'MOU/PKL/002/2024'),
(3, 'Dinas Arsip Kota', 'Aktif', 'Jl. Kenari No. 12, Yogyakarta', '0274000003', '8 km', 'MOU/PKL/003/2024')
ON DUPLICATE KEY UPDATE
  `NAMA_MITRA_PKL` = VALUES(`NAMA_MITRA_PKL`),
  `STATUS_MITRA_PKL` = VALUES(`STATUS_MITRA_PKL`),
  `ALAMAT_MITRA_PKL` = VALUES(`ALAMAT_MITRA_PKL`),
  `NO_TELP_MITRA_PKL` = VALUES(`NO_TELP_MITRA_PKL`),
  `JARAK_TEMPAT_PKL` = VALUES(`JARAK_TEMPAT_PKL`),
  `NO_MOU_PKL` = VALUES(`NO_MOU_PKL`);

INSERT INTO `pkl_siswa`
(`ID_PKL_SISWA`, `ID_PENDAF_PKL`, `ID_SISWA_TETAP`, `ID_MITRA_PKL`, `NIP_KARYAWAN`, `STATUS_PKL`, `NILAI_PKL`, `JUDUL_LAPORAN_PKL`, `LINK_LAPORAN_PKL`, `LINK_GAMBAR_MAP`) VALUES
(1, 1, 1, 1, '19880101201401', 'Selesai', 88, 'Sistem Informasi Perpustakaan KACA 1', 'laporan-pkl-001.pdf', NULL),
(2, 2, 2, 2, '19890202201502', 'Selesai', 86, 'Sistem Informasi Perpustakaan KACA 2', 'laporan-pkl-002.pdf', NULL),
(3, 3, 3, 3, '19900303201603', 'Selesai', 90, 'Sistem Informasi Perpustakaan KACA 3', 'laporan-pkl-003.pdf', NULL),
(4, 4, 4, 1, '19880101201401', 'Selesai', 87, 'Sistem Informasi Perpustakaan KACA 4', 'laporan-pkl-004.pdf', NULL),
(5, 5, 5, 2, '19890202201502', 'Selesai', 85, 'Sistem Informasi Perpustakaan KACA 5', 'laporan-pkl-005.pdf', NULL),
(6, 6, 6, 3, '19900303201603', 'Selesai', 89, 'Sistem Informasi Perpustakaan KACA 6', 'laporan-pkl-006.pdf', NULL),
(7, 7, 7, 1, '19880101201401', 'Selesai', 84, 'Sistem Informasi Perpustakaan KACA 7', 'laporan-pkl-007.pdf', NULL),
(8, 8, 8, 2, '19890202201502', 'Selesai', 91, 'Sistem Informasi Perpustakaan KACA 8', 'laporan-pkl-008.pdf', NULL),
(9, 9, 9, 3, '19900303201603', 'Selesai', 88, 'Sistem Informasi Perpustakaan KACA 9', 'laporan-pkl-009.pdf', NULL),
(10, 10, 10, 1, '19880101201401', 'Selesai', 86, 'Sistem Informasi Perpustakaan KACA 10', 'laporan-pkl-010.pdf', NULL)
ON DUPLICATE KEY UPDATE
  `ID_PENDAF_PKL` = VALUES(`ID_PENDAF_PKL`),
  `ID_SISWA_TETAP` = VALUES(`ID_SISWA_TETAP`),
  `ID_MITRA_PKL` = VALUES(`ID_MITRA_PKL`),
  `NIP_KARYAWAN` = VALUES(`NIP_KARYAWAN`),
  `STATUS_PKL` = VALUES(`STATUS_PKL`),
  `NILAI_PKL` = VALUES(`NILAI_PKL`),
  `JUDUL_LAPORAN_PKL` = VALUES(`JUDUL_LAPORAN_PKL`),
  `LINK_LAPORAN_PKL` = VALUES(`LINK_LAPORAN_PKL`),
  `LINK_GAMBAR_MAP` = VALUES(`LINK_GAMBAR_MAP`);

INSERT INTO `mst_koleksi_laporan`
(`ID_MST_LAPORAN`, `ID_PKL_SISWA`, `IS_DELETE`) VALUES
(1, 1, 0),
(2, 2, 0),
(3, 3, 0),
(4, 4, 0),
(5, 5, 0),
(6, 6, 0),
(7, 7, 0),
(8, 8, 0),
(9, 9, 0),
(10, 10, 0)
ON DUPLICATE KEY UPDATE
  `ID_PKL_SISWA` = VALUES(`ID_PKL_SISWA`),
  `IS_DELETE` = VALUES(`IS_DELETE`);

INSERT INTO `mst_koleksi_buku`
(`ISBN`, `ID_REF_KOLEKSI`, `JUDUL_KOLEKSI`, `PENGARANG`, `PENERBIT`, `TAHUN`, `NB_KOLEKSI`, `TGL_MASUK_KOLEKSI`, `JUMLAH_EKSEMPLAR`, `JUMLAH_HALAMAN`, `UKURAN_BUKU`, `BIBLIOGRAFI`, `INDEKS_AWAL_AKHIR`, `KETERANGAN_BUKU`, `NO_RAK_BUKU`, `IS_DELETE`) VALUES
('9786020000001', 3, 'Clean Code', 'Penulis Ahli 1', 'Penerbit Utama 1', '2019', 101, '2026-04-01 00:00:00', 5, 250, 'A5', '-', 0, 'Koleksi Umum', 'RAK-A', 0),
('9786020000002', 3, 'Refactoring', 'Penulis Ahli 2', 'Penerbit Utama 2', '2020', 102, '2026-04-02 00:00:00', 5, 250, 'A5', '-', 0, 'Koleksi Umum', 'RAK-B', 0),
('9786020000003', 3, 'Design Patterns', 'Penulis Ahli 3', 'Penerbit Utama 3', '2021', 103, '2026-04-03 00:00:00', 5, 250, 'A5', '-', 0, 'Koleksi Umum', 'RAK-C', 0),
('9786020000004', 3, 'Pragmatic Programmer', 'Penulis Ahli 4', 'Penerbit Utama 4', '2022', 104, '2026-04-04 00:00:00', 5, 250, 'A5', '-', 0, 'Koleksi Umum', 'RAK-D', 0),
('9786020000005', 3, 'Algoritma Dasar', 'Penulis Ahli 5', 'Penerbit Utama 5', '2023', 105, '2026-04-05 00:00:00', 5, 250, 'A5', '-', 0, 'Koleksi Umum', 'RAK-E', 0),
('9786020000006', 3, 'Deep Learning', 'Penulis Ahli 6', 'Penerbit Utama 6', '2024', 106, '2026-04-06 00:00:00', 5, 250, 'A5', '-', 0, 'Koleksi Umum', 'RAK-F', 0),
('9786020000007', 3, 'Docker Deep Dive', 'Penulis Ahli 7', 'Penerbit Utama 7', '2024', 107, '2026-04-07 00:00:00', 5, 250, 'A5', '-', 0, 'Koleksi Umum', 'RAK-G', 0),
('9786020000008', 3, 'Kubernetes Up', 'Penulis Ahli 8', 'Penerbit Utama 8', '2024', 108, '2026-04-08 00:00:00', 5, 250, 'A5', '-', 0, 'Koleksi Umum', 'RAK-H', 0),
('9786020000009', 3, 'Modern PHP', 'Penulis Ahli 9', 'Penerbit Utama 9', '2024', 109, '2026-04-09 00:00:00', 5, 250, 'A5', '-', 0, 'Koleksi Umum', 'RAK-I', 0),
('9786020000010', 3, 'JavaScript Good Parts', 'Penulis Ahli 10', 'Penerbit Utama 10', '2024', 110, '2026-04-10 00:00:00', 5, 250, 'A5', '-', 0, 'Koleksi Umum', 'RAK-J', 0),
('9786023777701', 3, 'Dasar UI UX', 'Rina Oktavia', 'Informatika', '2026', 501, '2026-04-10 00:00:00', 12, 210, '17x24 cm', 'Ada', 1, 'Buku baru inventaris April', 'T-08', 0),
('9786023777702', 1, 'Akuntansi Dasar SMK', 'Dewi Lestari', 'Erlangga', '2026', 502, '2026-04-12 00:00:00', 18, 250, '21x29 cm', '-', 0, 'Buku pelajaran baru semester genap', 'A-03', 0),
('9786023777703', 2, 'Cerita Nusantara', 'Ahmad Fauzi', 'Gramedia', '2025', 503, '2026-04-15 00:00:00', 9, 180, '14x20 cm', 'Ada', 1, 'Koleksi fiksi baru', 'F-15', 0),
('9796028000001', 4, 'Laporan PKL KACA 01', 'Elsy Laurens', 'SMK BODA Yogyakarta', '2024', 401, '2026-04-11 00:00:00', 1, 80, 'A4', 'Ada', 0, 'Arsip PKL', 'RAK-PKL', 0),
('9796028000002', 4, 'Laporan PKL KACA 02', 'Rifa Amalia', 'SMK BODA Yogyakarta', '2024', 402, '2026-04-11 00:00:00', 1, 80, 'A4', 'Ada', 0, 'Arsip PKL', 'RAK-PKL', 0),
('9796028000003', 4, 'Laporan PKL KACA 03', 'Budi Santoso', 'SMK BODA Yogyakarta', '2024', 403, '2026-04-11 00:00:00', 1, 80, 'A4', 'Ada', 0, 'Arsip PKL', 'RAK-PKL', 0),
('9796028000004', 4, 'Laporan PKL KACA 04', 'Siti Aminah', 'SMK BODA Yogyakarta', '2024', 404, '2026-04-11 00:00:00', 1, 80, 'A4', 'Ada', 0, 'Arsip PKL', 'RAK-PKL', 0),
('9796028000005', 4, 'Laporan PKL KACA 05', 'Andi Wijaya', 'SMK BODA Yogyakarta', '2024', 405, '2026-04-11 00:00:00', 1, 80, 'A4', 'Ada', 0, 'Arsip PKL', 'RAK-PKL', 0),
('9796028000006', 4, 'Laporan PKL KACA 06', 'Eka Putri', 'SMK BODA Yogyakarta', '2024', 406, '2026-04-11 00:00:00', 1, 80, 'A4', 'Ada', 0, 'Arsip PKL', 'RAK-PKL', 0),
('9796028000007', 4, 'Laporan PKL KACA 07', 'Gede Bagus', 'SMK BODA Yogyakarta', '2024', 407, '2026-04-11 00:00:00', 1, 80, 'A4', 'Ada', 0, 'Arsip PKL', 'RAK-PKL', 0),
('9796028000008', 4, 'Laporan PKL KACA 08', 'Luh Putu', 'SMK BODA Yogyakarta', '2024', 408, '2026-04-11 00:00:00', 1, 80, 'A4', 'Ada', 0, 'Arsip PKL', 'RAK-PKL', 0),
('9796028000009', 4, 'Laporan PKL KACA 09', 'Aditya Pratama', 'SMK BODA Yogyakarta', '2024', 409, '2026-04-11 00:00:00', 1, 80, 'A4', 'Ada', 0, 'Arsip PKL', 'RAK-PKL', 0),
('9796028000010', 4, 'Laporan PKL KACA 10', 'Rizky Ramadhan', 'SMK BODA Yogyakarta', '2024', 410, '2026-04-11 00:00:00', 1, 80, 'A4', 'Ada', 0, 'Arsip PKL', 'RAK-PKL', 0)
ON DUPLICATE KEY UPDATE
  `ID_REF_KOLEKSI` = VALUES(`ID_REF_KOLEKSI`),
  `JUDUL_KOLEKSI` = VALUES(`JUDUL_KOLEKSI`),
  `PENGARANG` = VALUES(`PENGARANG`),
  `PENERBIT` = VALUES(`PENERBIT`),
  `TAHUN` = VALUES(`TAHUN`),
  `NB_KOLEKSI` = VALUES(`NB_KOLEKSI`),
  `TGL_MASUK_KOLEKSI` = VALUES(`TGL_MASUK_KOLEKSI`),
  `JUMLAH_EKSEMPLAR` = VALUES(`JUMLAH_EKSEMPLAR`),
  `JUMLAH_HALAMAN` = VALUES(`JUMLAH_HALAMAN`),
  `UKURAN_BUKU` = VALUES(`UKURAN_BUKU`),
  `BIBLIOGRAFI` = VALUES(`BIBLIOGRAFI`),
  `INDEKS_AWAL_AKHIR` = VALUES(`INDEKS_AWAL_AKHIR`),
  `KETERANGAN_BUKU` = VALUES(`KETERANGAN_BUKU`),
  `NO_RAK_BUKU` = VALUES(`NO_RAK_BUKU`),
  `IS_DELETE` = VALUES(`IS_DELETE`);

INSERT INTO `cp_koleksi`
(`ID_CP_KOLEKSI`, `ISBN`, `ID_MST_LAPORAN`, `STATUS_BUKU`) VALUES
(101, '9786020000001', NULL, 'Dipinjam'),
(102, '9786020000002', NULL, 'Dipinjam'),
(103, '9786020000003', NULL, 'Dipinjam'),
(104, '9786020000004', NULL, 'Dipinjam'),
(105, '9786020000005', NULL, 'Dipinjam'),
(106, '9786020000006', NULL, 'Tersedia'),
(107, '9786020000007', NULL, 'Dipinjam'),
(108, '9786020000008', NULL, 'Tersedia'),
(109, '9786020000009', NULL, 'Dipinjam'),
(110, '9786020000010', NULL, 'Tersedia'),
(501, '9786023777701', NULL, 'Tersedia'),
(502, '9786023777702', NULL, 'Tersedia'),
(503, '9786023777703', NULL, 'Tersedia'),
(201, '9796028000001', 1, 'Tersedia'),
(202, '9796028000002', 2, 'Tersedia'),
(203, '9796028000003', 3, 'Tersedia'),
(204, '9796028000004', 4, 'Tersedia'),
(205, '9796028000005', 5, 'Tersedia'),
(206, '9796028000006', 6, 'Tersedia'),
(207, '9796028000007', 7, 'Tersedia'),
(208, '9796028000008', 8, 'Tersedia'),
(209, '9796028000009', 9, 'Tersedia'),
(210, '9796028000010', 10, 'Tersedia')
ON DUPLICATE KEY UPDATE
  `ISBN` = VALUES(`ISBN`),
  `ID_MST_LAPORAN` = VALUES(`ID_MST_LAPORAN`),
  `STATUS_BUKU` = VALUES(`STATUS_BUKU`);

INSERT INTO `tr_peminjaman`
(`ID_PEMINJAMAN`, `ID_SISWA_TETAP`, `ID_CP_KOLEKSI`, `NIP_KARYAWAN`, `TGL_PINJAM`, `TGL_HARUS_KEMBALI`, `TGL_KEMBALI`, `STATUS_PEMINJAMAN`, `KONDISI_BUKU`, `KETERANGAN_PEMINJAMAN`, `DENDA_PEMINJAMAN`) VALUES
(1001, 1, 101, '19850101201001', '2026-04-19 00:00:00', '2026-04-26 00:00:00', NULL, 'Dipinjam', 'Baik', 'Peminjaman Aktif', 0),
(1002, 2, 102, '19850101201001', '2026-04-19 00:00:00', '2026-04-26 00:00:00', NULL, 'Dipinjam', 'Baik', 'Peminjaman Aktif', 0),
(1003, 3, 103, '19850101201001', '2026-04-19 00:00:00', '2026-04-26 00:00:00', NULL, 'Dipinjam', 'Baik', 'Peminjaman Aktif', 0),
(1004, 4, 104, '19850101201001', '2026-04-19 00:00:00', '2026-04-26 00:00:00', NULL, 'Dipinjam', 'Baik', 'Peminjaman Aktif', 0),
(1005, 5, 105, '19850101201001', '2026-04-19 00:00:00', '2026-04-26 00:00:00', NULL, 'Dipinjam', 'Baik', 'Peminjaman Aktif', 0),
(1006, 1, 106, '19880101201401', '2026-04-03 00:00:00', '2026-04-10 00:00:00', '2026-04-09 00:00:00', 'Kembali', 'Baik', '[DUMMY_GURU_1]', 0),
(1007, 1, 107, '19890202201502', '2026-04-07 00:00:00', '2026-04-14 00:00:00', NULL, 'Dipinjam', 'Baik', '[DUMMY_GURU_2]', 0),
(1008, 1, 108, '19900303201603', '2026-04-11 00:00:00', '2026-04-18 00:00:00', '2026-04-16 00:00:00', 'Kembali', 'Baik', '[DUMMY_GURU_3]', 0),
(1009, 1, 109, '19880101201401', '2026-04-15 00:00:00', '2026-04-22 00:00:00', NULL, 'Dipinjam', 'Baik', '[DUMMY_GURU_4]', 0)
ON DUPLICATE KEY UPDATE
  `ID_SISWA_TETAP` = VALUES(`ID_SISWA_TETAP`),
  `ID_CP_KOLEKSI` = VALUES(`ID_CP_KOLEKSI`),
  `NIP_KARYAWAN` = VALUES(`NIP_KARYAWAN`),
  `TGL_PINJAM` = VALUES(`TGL_PINJAM`),
  `TGL_HARUS_KEMBALI` = VALUES(`TGL_HARUS_KEMBALI`),
  `TGL_KEMBALI` = VALUES(`TGL_KEMBALI`),
  `STATUS_PEMINJAMAN` = VALUES(`STATUS_PEMINJAMAN`),
  `KONDISI_BUKU` = VALUES(`KONDISI_BUKU`),
  `KETERANGAN_PEMINJAMAN` = VALUES(`KETERANGAN_PEMINJAMAN`),
  `DENDA_PEMINJAMAN` = VALUES(`DENDA_PEMINJAMAN`);

INSERT INTO `tr_kunjungan_perpus`
(`ID_KUNJUNGAN`, `ID_SISWA_TETAP`, `START_KUNJUNGAN`, `END_KUNJUNGAN`) VALUES
(1, 3, '2026-01-10 08:05:00', '2026-01-10 08:45:00'),
(2, 5, '2026-01-12 09:10:00', '2026-01-12 09:55:00'),
(3, 6, '2026-01-18 10:00:00', '2026-01-18 10:35:00'),
(4, 3, '2026-02-03 08:20:00', '2026-02-03 08:50:00'),
(5, 6, '2026-02-07 11:00:00', '2026-02-07 11:40:00'),
(6, 5, '2026-02-20 12:15:00', '2026-02-20 12:55:00'),
(7, 3, '2026-03-05 07:45:00', '2026-03-05 08:15:00'),
(8, 5, '2026-03-09 09:30:00', '2026-03-09 10:10:00'),
(9, 6, '2026-03-11 13:00:00', '2026-03-11 13:35:00'),
(10, 3, '2026-04-02 08:00:00', '2026-04-02 08:30:00'),
(11, 3, '2026-04-08 10:10:00', '2026-04-08 10:50:00'),
(12, 5, '2026-04-10 09:45:00', '2026-04-10 10:25:00'),
(13, 6, '2026-04-14 11:30:00', '2026-04-14 12:00:00'),
(14, 5, '2026-04-16 12:40:00', '2026-04-16 13:20:00')
ON DUPLICATE KEY UPDATE
  `ID_SISWA_TETAP` = VALUES(`ID_SISWA_TETAP`),
  `START_KUNJUNGAN` = VALUES(`START_KUNJUNGAN`),
  `END_KUNJUNGAN` = VALUES(`END_KUNJUNGAN`);

COMMIT;

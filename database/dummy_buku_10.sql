-- 10 data dummy untuk halaman Manajemen Buku.
-- Data only: tidak membuat/mengubah struktur tabel.

START TRANSACTION;

INSERT INTO `mst_koleksi_buku`
(`ISBN`, `ID_REF_KOLEKSI`, `JUDUL_KOLEKSI`, `PENGARANG`, `PENERBIT`, `TAHUN`, `NB_KOLEKSI`, `TGL_MASUK_KOLEKSI`, `JUMLAH_EKSEMPLAR`, `JUMLAH_HALAMAN`, `UKURAN_BUKU`, `BIBLIOGRAFI`, `INDEKS_AWAL_AKHIR`, `KETERANGAN_BUKU`, `NO_RAK_BUKU`, `IS_DELETE`) VALUES
('9786029000001', 1, 'Matematika SMK', 'Tim Guru BOPKRI', 'Erlangga', '2026', 701, '2026-04-25 00:00:00', 10, 220, 'A4', 'Ada', 1, 'Dummy manajemen buku', 'A-01', 0),
('9786029000002', 3, 'Basis Data Dasar', 'Andi Nugroho', 'Informatika', '2026', 702, '2026-04-25 00:00:00', 8, 180, 'A5', 'Ada', 1, 'Dummy manajemen buku', 'T-01', 0),
('9786029000003', 3, 'Jaringan Komputer', 'Rina Saputra', 'Informatika', '2025', 703, '2026-04-25 00:00:00', 7, 200, 'A5', 'Ada', 1, 'Dummy manajemen buku', 'T-02', 0),
('9786029000004', 3, 'Pemrograman Web', 'Budi Hartono', 'Deepublish', '2025', 704, '2026-04-25 00:00:00', 12, 240, 'A5', '-', 0, 'Dummy manajemen buku', 'T-03', 0),
('9786029000005', 1, 'Akuntansi SMK', 'Dewi Anggraini', 'Erlangga', '2026', 705, '2026-04-25 00:00:00', 15, 260, 'A4', '-', 0, 'Dummy manajemen buku', 'A-02', 0),
('9786029000006', 2, 'Novel Pelangi', 'Nadia Putri', 'Gramedia', '2024', 706, '2026-04-25 00:00:00', 6, 160, 'A5', '-', 0, 'Dummy manajemen buku', 'F-01', 0),
('9786029000007', 5, 'Fisika Terapan', 'Arif Santoso', 'Yudhistira', '2025', 707, '2026-04-25 00:00:00', 9, 210, 'A4', 'Ada', 1, 'Dummy manajemen buku', 'S-01', 0),
('9786029000008', 6, 'Sejarah Indonesia', 'Maya Lestari', 'Intan Pariwara', '2024', 708, '2026-04-25 00:00:00', 11, 190, 'A4', 'Ada', 1, 'Dummy manajemen buku', 'H-01', 0),
('9786029000009', 7, 'Desain Grafis', 'Yoga Pratama', 'Informatika', '2026', 709, '2026-04-25 00:00:00', 5, 175, 'A5', '-', 0, 'Dummy manajemen buku', 'D-01', 0),
('9786029000010', 10, 'Ensiklopedia Mini', 'Tim Redaksi', 'Balai Pustaka', '2023', 710, '2026-04-25 00:00:00', 4, 300, 'A4', 'Ada', 1, 'Dummy manajemen buku', 'E-01', 0)
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
(601, '9786029000001', NULL, 'Tersedia'),
(602, '9786029000002', NULL, 'Tersedia'),
(603, '9786029000003', NULL, 'Tersedia'),
(604, '9786029000004', NULL, 'Tersedia'),
(605, '9786029000005', NULL, 'Tersedia'),
(606, '9786029000006', NULL, 'Tersedia'),
(607, '9786029000007', NULL, 'Tersedia'),
(608, '9786029000008', NULL, 'Tersedia'),
(609, '9786029000009', NULL, 'Tersedia'),
(610, '9786029000010', NULL, 'Tersedia')
ON DUPLICATE KEY UPDATE
  `ISBN` = VALUES(`ISBN`),
  `ID_MST_LAPORAN` = VALUES(`ID_MST_LAPORAN`),
  `STATUS_BUKU` = VALUES(`STATUS_BUKU`);

COMMIT;

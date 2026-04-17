<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$dummyCount = Illuminate\Support\Facades\DB::table('tr_peminjaman')
    ->whereIn('keterangan_peminjaman', ['[DUMMY_GURU_1]','[DUMMY_GURU_2]','[DUMMY_GURU_3]','[DUMMY_GURU_4]'])
    ->count();

$summary = Illuminate\Support\Facades\DB::table('tr_peminjaman as p')
    ->join('mst_karyawan as g', 'p.nip_karyawan', '=', 'g.nip_karyawan')
    ->whereRaw('LOWER(g.jabatan_fungsional) = ?', ['guru'])
    ->where('p.status_peminjaman', '!=', 'Dihapus')
    ->whereYear('p.tgl_peminjaman', 2026)
    ->whereMonth('p.tgl_peminjaman', 4)
    ->selectRaw("COUNT(*) as total, SUM(CASE WHEN p.status_peminjaman = 'Dipinjam' THEN 1 ELSE 0 END) as dipinjam, SUM(CASE WHEN p.status_peminjaman = 'Kembali' THEN 1 ELSE 0 END) as kembali, COUNT(DISTINCT g.nip_karyawan) as guru")
    ->first();

$rows = Illuminate\Support\Facades\DB::table('tr_peminjaman as p')
    ->join('mst_karyawan as g', 'p.nip_karyawan', '=', 'g.nip_karyawan')
    ->join('cp_koleksi as cp', 'p.id_cp_koleksi', '=', 'cp.id_cp_koleksi')
    ->join('mst_koleksi_buku as b', 'cp.ISBN', '=', 'b.ISBN')
    ->whereIn('p.keterangan_peminjaman', ['[DUMMY_GURU_1]','[DUMMY_GURU_2]','[DUMMY_GURU_3]','[DUMMY_GURU_4]'])
    ->orderBy('p.id_peminjaman')
    ->get(['p.id_peminjaman','p.tgl_peminjaman','p.status_peminjaman','g.nama_karyawan','b.judul_koleksi']);

echo json_encode([
  'dummy_count' => $dummyCount,
  'summary_april_2026' => $summary,
  'rows' => $rows,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

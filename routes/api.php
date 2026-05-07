<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KoleksiBukuController;
use App\Http\Controllers\Api\MasterKoleksiController;
use App\Http\Controllers\Api\PeminjamanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KoleksiController;
use App\Http\Controllers\Api\LaporanPklController;
use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Pustakawan\BukuController;

// --- AUTH & USER ---
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// --- LOG SISTEM ---
Route::get('/logs/access', [LogController::class, 'access']);
Route::get('/logs/activity', [LogController::class, 'activity']);

// --- TRANSAKSI PEMINJAMAN & PENGEMBALIAN ---
Route::get('/peminjaman/cek-aktif', [PeminjamanController::class, 'cekAktif']);
Route::get('/peminjaman/overdue', [PeminjamanController::class, 'overdue']);
Route::get('/peminjaman', [PeminjamanController::class, 'index']);
Route::post('/peminjaman', [PeminjamanController::class, 'store']);
Route::put('/peminjaman/{id}', [PeminjamanController::class, 'update']);
Route::delete('/peminjaman/{id}', [PeminjamanController::class, 'destroy']);

Route::get('/pengembalian/history', [PeminjamanController::class, 'historyPengembalian']);
Route::get('/pengembalian', [DashboardController::class, 'getPengembalian']);
Route::post('/pengembalian/batch', [PeminjamanController::class, 'batchReturn']);
Route::post('/pengembalian/scan', [PeminjamanController::class, 'scanPengembalian']);
Route::post('/pengembalian/proses/{id}', [PeminjamanController::class, 'prosesPengembalian']);

// --- DASHBOARD & ANGGOTA ---
Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
Route::get('/anggota', [DashboardController::class, 'getAnggota']);
Route::get('/anggota/{identifier}', [DashboardController::class, 'getAnggotaByIdentifier']);

// --- MANAJEMEN BUKU (Koleksi Fisik) ---
Route::get('/buku/kategori', [MasterKoleksiController::class, 'options']);
Route::get('/buku', [KoleksiBukuController::class, 'index']);
Route::post('/buku', [KoleksiBukuController::class, 'store']);
Route::get('/buku/{isbn}/copies', [KoleksiBukuController::class, 'copies']);
Route::patch('/buku/copies/{idCpKoleksi}/status', [KoleksiBukuController::class, 'updateCopyStatus']);
Route::put('/buku/{isbn}', [KoleksiBukuController::class, 'update']);
Route::delete('/buku/{isbn}', [KoleksiBukuController::class, 'destroy']);
Route::post('/generate-barcode', [KoleksiBukuController::class, 'generateBarcode']);
Route::post('/buku/denda-kerusakan', [BukuController::class, 'simpanDendaKerusakan']);


// --- KATEGORI BUKU (KoleksiController) ---
// PERBAIKAN KUNCI: Mengubah /koleksi menjadi /kategori agar tidak bentrok
Route::get('/kategori', [KoleksiController::class, 'index']);
Route::post('/kategori', [KoleksiController::class, 'store']);
Route::put('/kategori/{id}', [KoleksiController::class, 'update']);
Route::delete('/kategori/{id}', [KoleksiController::class, 'destroy']);

// --- LAPORAN ---
Route::get('/laporan', [LaporanController::class, 'getLaporan']); 
Route::get('/laporan/peminjaman-bulanan', [LaporanController::class, 'statistikPeminjamanBulanan']);
Route::get('/laporan/peminjaman-guru', [LaporanController::class, 'laporanPeminjamanGuru']);
Route::get('/laporan/kunjungan-distribusi-kelas', [LaporanController::class, 'distribusiKunjunganKelas']);
Route::get('/laporan/kunjungan-distribusi-hari', [LaporanController::class, 'distribusiKunjunganHari']);
Route::get('/laporan/inventarisasi-buku-baru', [LaporanController::class, 'inventarisasiBukuBaru']);
Route::get('/laporan/siswa-terajin', [LaporanController::class, 'siswaTerajin']);
Route::get('/laporan/kunjungan-bulanan', [LaporanController::class, 'kunjunganBulanan']);
Route::get('/laporan/buku-terpopuler', [LaporanController::class, 'bukuTerpopuler']);
Route::get('/laporan/kategori-populer', [LaporanController::class, 'kategoriPopuler']);
Route::post('/laporan/tambah', [LaporanController::class, 'store']);
Route::put('/laporan/ubah/{isbn}', [LaporanController::class, 'update']);
Route::delete('/laporan/hapus/{isbn}', [LaporanController::class, 'destroy']);
Route::get('/laporan/download/{isbn}', [LaporanController::class, 'downloadLaporan']);

// --- LAPORAN PKL ---
Route::get('/laporan-pkl', [LaporanPklController::class, 'index']);
Route::get('/buku/laporan', [LaporanPklController::class, 'index']);

// --- PEMUSNAHAN BUKU ---
// Group Dashboard & Data
// Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
Route::get('/anggota', [DashboardController::class, 'getAnggota']);
Route::get('/buku', [KoleksiBukuController::class, 'index']);
Route::post('/buku', [KoleksiBukuController::class, 'store']);
Route::get('/buku/{isbn}/copies', [KoleksiBukuController::class, 'copies']);
Route::patch('/buku/copies/{idCpKoleksi}/status', [KoleksiBukuController::class, 'updateCopyStatus']);
Route::put('/buku/{isbn}', [KoleksiBukuController::class, 'update']);
Route::delete('/buku/{isbn}', [KoleksiBukuController::class, 'destroy']);
Route::get('/pengembalian', [DashboardController::class, 'getPengembalian']);
Route::get('/koleksi', [MasterKoleksiController::class, 'index']);
Route::post('/koleksi', [MasterKoleksiController::class, 'store']);
Route::put('/koleksi/{id}', [MasterKoleksiController::class, 'update']);
Route::delete('/koleksi/{id}', [MasterKoleksiController::class, 'destroy']);
Route::get('/anggota/{identifier}', [DashboardController::class, 'getAnggotaByIdentifier']);
// --- BAGIAN BARU: RUTE PEMUSNAHAN BUKU ---
// Pastikan fungsi-fungsi ini (getHistoryPemusnahan, storePemusnahan, dll) 
// sudah dibuat di DashboardController atau controller terkait.
Route::get('/pemusnahan', [DashboardController::class, 'getHistoryPemusnahan']);
Route::post('/pemusnahan', [DashboardController::class, 'storePemusnahan']);
Route::get('/buku-rusak', [DashboardController::class, 'getBukuRusak']);
Route::get('/buku-overdue', [DashboardController::class, 'getBukuOverdue']);
Route::patch('/pemusnahan/{id}', [DashboardController::class, 'updateStatusPemusnahan']);
Route::patch('/pemusnahan/{id}/konfirmasi', [DashboardController::class, 'confirmPemusnahan']);

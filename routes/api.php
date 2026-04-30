<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KoleksiBukuController;
use App\Http\Controllers\Api\MasterKoleksiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KoleksiController;
use App\Http\Controllers\Api\LaporanPklController;
use App\Http\Controllers\Pustakawan\BukuController;

Route::get('/peminjaman', [App\Http\Controllers\Api\PeminjamanController::class, 'index']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/generate-barcode', [KoleksiBukuController::class, 'generateBarcode']);
// Group Laporan
Route::get('/laporan', [LaporanController::class, 'getLaporan']); 
Route::get('/laporan/peminjaman-bulanan', [LaporanController::class, 'statistikPeminjamanBulanan']);
Route::get('/laporan/peminjaman-guru', [LaporanController::class, 'laporanPeminjamanGuru']);
Route::get('/laporan/kunjungan-distribusi-kelas', [LaporanController::class, 'distribusiKunjunganKelas']);
Route::get('/laporan/kunjungan-distribusi-hari', [LaporanController::class, 'distribusiKunjunganHari']);
Route::get('/laporan/inventarisasi-buku-baru', [LaporanController::class, 'inventarisasiBukuBaru']);
Route::delete('/laporan/hapus/{isbn}', [LaporanController::class, 'destroy']);
Route::put('/laporan/ubah/{isbn}', [LaporanController::class, 'update']);
Route::post('/laporan/tambah', [LaporanController::class, 'store']);
Route::get('/laporan-pkl', [LaporanPklController::class, 'index']);
Route::get('/buku/laporan', [LaporanPklController::class, 'index']);


Route::get('/laporan/siswa-terajin', [LaporanController::class, 'siswaTerajin']);
Route::get('/laporan/kunjungan-bulanan', [LaporanController::class, 'kunjunganBulanan']);
Route::get('/laporan/buku-terpopuler', [LaporanController::class, 'bukuTerpopuler']);
Route::get('/laporan/kategori-populer', [LaporanController::class, 'kategoriPopuler']);
// Group Dashboard & Data
Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
Route::get('/anggota', [DashboardController::class, 'getAnggota']);
Route::get('/buku', [KoleksiBukuController::class, 'index']);
Route::post('/buku', [KoleksiBukuController::class, 'store']);
Route::put('/buku/{isbn}', [KoleksiBukuController::class, 'update']);
Route::delete('/buku/{isbn}', [KoleksiBukuController::class, 'destroy']);
Route::get('/pengembalian', [DashboardController::class, 'getPengembalian']);
Route::get('/buku/kategori', [MasterKoleksiController::class, 'options']);
Route::get('/koleksi', [MasterKoleksiController::class, 'index']);
Route::post('/koleksi', [MasterKoleksiController::class, 'store']);
Route::put('/koleksi/{id}', [MasterKoleksiController::class, 'update']);
Route::delete('/koleksi/{id}', [MasterKoleksiController::class, 'destroy']);
Route::get('/anggota/{identifier}', [DashboardController::class, 'getAnggotaByIdentifier']);
Route::get('/peminjaman/cek-aktif', [App\Http\Controllers\Api\PeminjamanController::class, 'cekAktif']);
Route::post('/pengembalian/batch', [App\Http\Controllers\Api\PeminjamanController::class, 'batchReturn']);
Route::get('/pengembalian/history', [PengembalianController::class, 'history']);
// --- BAGIAN BARU: RUTE PEMUSNAHAN BUKU ---
// Pastikan fungsi-fungsi ini (getHistoryPemusnahan, storePemusnahan, dll) 
// sudah dibuat di DashboardController atau controller terkait.
Route::get('/pemusnahan', [DashboardController::class, 'getHistoryPemusnahan']);
Route::post('/pemusnahan', [DashboardController::class, 'storePemusnahan']);
Route::get('/buku-rusak', [DashboardController::class, 'getBukuRusak']);
Route::get('/buku-overdue', [DashboardController::class, 'getBukuOverdue']);
Route::patch('/pemusnahan/{id}', [DashboardController::class, 'updateStatusPemusnahan']);
Route::patch('/pemusnahan/{id}/konfirmasi', [DashboardController::class, 'confirmPemusnahan']);
// ------------------------------------------
// --- ROUTE UNTUK DENDA BUKU RUSAK (API) ---
Route::post('/buku/denda-kerusakan', [BukuController::class, 'simpanDendaKerusakan']);
// --- ROUTE TRANSAKSI PEMINJAMAN ---
Route::post('/peminjaman', [App\Http\Controllers\Api\PeminjamanController::class, 'store']);
Route::put('/peminjaman/{id}', [App\Http\Controllers\Api\PeminjamanController::class, 'update']);
Route::delete('/peminjaman/{id}', [App\Http\Controllers\Api\PeminjamanController::class, 'destroy']);

//kategori buku
Route::get('/koleksi', [KoleksiController::class, 'index']);
Route::post('/koleksi', [KoleksiController::class, 'store']);
Route::put('/koleksi/{id}', [KoleksiController::class, 'update']);
Route::delete('/koleksi/{id}', [KoleksiController::class, 'destroy']);

//ROUTE PEMUSNAHAN
// --- BAGIAN BARU: RUTE PEMUSNAHAN BUKU ---
Route::get('/pemusnahan', [DashboardController::class, 'getHistoryPemusnahan']);
Route::post('/pemusnahan', [DashboardController::class, 'storePemusnahan']);
Route::get('/buku-rusak', [DashboardController::class, 'getBukuRusak']);
Route::get('/buku-overdue', [DashboardController::class, 'getBukuOverdue']);
Route::patch('/pemusnahan/{id}', [DashboardController::class, 'updateStatusPemusnahan']);
Route::patch('/pemusnahan/{id}/konfirmasi', [DashboardController::class, 'confirmPemusnahan']);
// ------------------------------------------

// --- ROUTE PENGEMBALIAN BUKU ---
Route::post('/pengembalian/scan', [App\Http\Controllers\Api\PeminjamanController::class, 'scanPengembalian']);
Route::post('/pengembalian/proses/{id}', [App\Http\Controllers\Api\PeminjamanController::class, 'prosesPengembalian']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

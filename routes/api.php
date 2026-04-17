<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KoleksiController;
use App\Http\Controllers\Api\LaporanPklController;
use App\Http\Controllers\Pustakawan\BukuController;

Route::get('/peminjaman', [App\Http\Controllers\Api\PeminjamanController::class, 'index']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/generate-barcode', [KoleksiController::class, 'generate']);
// Group Laporan
Route::get('/laporan', [LaporanController::class, 'getLaporan']); 
Route::get('/laporan/peminjaman-bulanan', [LaporanController::class, 'statistikPeminjamanBulanan']);
Route::get('/laporan/kunjungan-distribusi-kelas', [LaporanController::class, 'distribusiKunjunganKelas']);
Route::get('/laporan/kunjungan-distribusi-hari', [LaporanController::class, 'distribusiKunjunganHari']);
Route::get('/laporan/inventarisasi-buku-baru', [LaporanController::class, 'inventarisasiBukuBaru']);
Route::delete('/laporan/hapus/{id}', [LaporanController::class, 'destroy']);
Route::post('/laporan/ubah/{id}', [LaporanController::class, 'update']);
Route::post('/laporan/tambah', [LaporanController::class, 'store']);
Route::get('/laporan-pkl', [LaporanPklController::class, 'index']);
Route::get('/buku/laporan', [LaporanPklController::class, 'index']);


// Group Dashboard & Data
Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
Route::get('/anggota', [DashboardController::class, 'getAnggota']);
Route::get('/buku', [DashboardController::class, 'getBuku']);
Route::get('/pengembalian', [DashboardController::class, 'getPengembalian']);
Route::get('/buku/kategori', [App\Http\Controllers\DashboardController::class, 'getKategoriBuku']);
Route::get('/anggota/{identifier}', [DashboardController::class, 'getAnggotaByIdentifier']);
Route::get('/peminjaman/cek-aktif', [App\Http\Controllers\Api\PeminjamanController::class, 'cekAktif']);
Route::post('/pengembalian/batch', [App\Http\Controllers\Api\PeminjamanController::class, 'batchReturn']);

// --- ROUTE UNTUK DENDA BUKU RUSAK (API) ---
Route::post('/buku/denda-kerusakan', [BukuController::class, 'simpanDendaKerusakan']);
// --- ROUTE TRANSAKSI PEMINJAMAN ---
Route::post('/peminjaman', [App\Http\Controllers\Api\PeminjamanController::class, 'store']);
Route::put('/peminjaman/{id}', [App\Http\Controllers\Api\PeminjamanController::class, 'update']);
Route::delete('/peminjaman/{id}', [App\Http\Controllers\Api\PeminjamanController::class, 'destroy']);

//ROUTE PEMUSNAHAN
Route::get('/pemusnahan', [DashboardController::class, 'getHistoryPemusnahan']);
Route::post('/pemusnahan', [DashboardController::class, 'storePemusnahan']);
Route::get('/buku-rusak', [DashboardController::class, 'getBukuRusak']);
Route::get('/buku-overdue', [DashboardController::class, 'getBukuOverdue']);
Route::patch('/pemusnahan/{id}', [DashboardController::class, 'updateStatusPemusnahan']);

// --- ROUTE PENGEMBALIAN BUKU ---
Route::post('/pengembalian/scan', [App\Http\Controllers\Api\PeminjamanController::class, 'scanPengembalian']);
Route::post('/pengembalian/proses/{id}', [App\Http\Controllers\Api\PeminjamanController::class, 'prosesPengembalian']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

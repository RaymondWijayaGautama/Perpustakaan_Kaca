<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KoleksiController;
use App\Http\Controllers\Api\PeminjamanController;
// Import controller pemusnahan jika Anda sudah membuatnya, 
// atau arahkan ke DashboardController jika fungsinya ada di sana.
// use App\Http\Controllers\Api\PemusnahanController; 

Route::get('/peminjaman', [PeminjamanController::class, 'index']);
Route::post('/peminjaman', [PeminjamanController::class, 'store']);

Route::post('/generate-barcode', [KoleksiController::class, 'generate']);
Route::get('/laporan', [LaporanController::class, 'getLaporan']); 
Route::get('/laporan/peminjaman-bulanan', [LaporanController::class, 'statistikPeminjamanBulanan']);
Route::delete('/laporan/hapus/{id}', [LaporanController::class, 'destroy']);
Route::post('/laporan/ubah/{id}', [LaporanController::class, 'update']);
Route::post('/laporan/tambah', [LaporanController::class, 'store']);

Route::post('/login', [AuthController::class, 'login']);
Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
Route::get('/anggota', [DashboardController::class, 'getAnggota']);
Route::get('/buku', [DashboardController::class, 'getBuku']);
Route::get('/buku/kategori', [DashboardController::class, 'getKategoriBuku']);
Route::put('/buku/{isbn}', [DashboardController::class, 'updateBuku']);
Route::delete('/buku/{isbn}', [DashboardController::class, 'destroyBuku']);
Route::get('/pengembalian', [DashboardController::class, 'getPengembalian']);

// --- BAGIAN BARU: RUTE PEMUSNAHAN BUKU ---
// Pastikan fungsi-fungsi ini (getHistoryPemusnahan, storePemusnahan, dll) 
// sudah dibuat di DashboardController atau controller terkait.
Route::get('/pemusnahan', [DashboardController::class, 'getHistoryPemusnahan']);
Route::post('/pemusnahan', [DashboardController::class, 'storePemusnahan']);
Route::get('/buku-rusak', [DashboardController::class, 'getBukuRusak']);
Route::get('/buku-overdue', [DashboardController::class, 'getBukuOverdue']);
Route::patch('/pemusnahan/{id}', [DashboardController::class, 'updateStatusPemusnahan']);
// ------------------------------------------

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KoleksiController;


Route::post('/generate-barcode', [KoleksiController::class, 'generate']);
Route::get('/laporan', [LaporanController::class, 'getLaporan']); 
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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DashboardController;
// Tambahkan Import Ini
use App\Http\Controllers\Pustakawan\BukuController;

Route::post('/login', [AuthController::class, 'login']);

// Group Laporan
Route::get('/laporan', [LaporanController::class, 'getLaporan']); 
Route::delete('/laporan/hapus/{id}', [LaporanController::class, 'destroy']);
Route::post('/laporan/ubah/{id}', [LaporanController::class, 'update']);
Route::post('/laporan/tambah', [LaporanController::class, 'store']);

// Group Dashboard & Data
Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
Route::get('/anggota', [DashboardController::class, 'getAnggota']);
Route::get('/buku', [DashboardController::class, 'getBuku']);

// --- ROUTE UNTUK DENDA BUKU RUSAK (API) ---
Route::post('/buku/denda-kerusakan', [BukuController::class, 'simpanDendaKerusakan']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
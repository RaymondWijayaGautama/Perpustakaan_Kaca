<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KoleksiController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Pustakawan\BukuController;


Route::get('/generate-barcode', [KoleksiController::class, 'index']);
Route::post('/generate-barcode', [KoleksiController::class, 'generate']);
Route::get('/laporan/siswa-terajin', [LaporanController::class, 'siswaTerajin']);
Route::get('/laporan/siswa-terajin/pdf', [LaporanController::class, 'exportPdfSiswaTerajin']);
Route::get('/pustakawan/buku/import', [BukuController::class, 'halamanImport'])->name('pustakawan.buku.halaman_import');
Route::post('/pustakawan/buku/import', [BukuController::class, 'importExcel'])->name('pustakawan.buku.import');
Route::get('/laporan/kunjungan-bulanan', [LaporanController::class, 'kunjunganBulanan']);
Route::get('/laporan/kunjungan-bulanan/pdf', [LaporanController::class, 'exportPdfKunjungan']);
Route::get('/pustakawan/buku/export', [BukuController::class, 'exportExcel'])->name('pustakawan.buku.export');
Route::get('/', function () {
    return view('welcome');
});
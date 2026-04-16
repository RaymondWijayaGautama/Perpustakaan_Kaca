<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KoleksiController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Pustakawan\BukuController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/generate-barcode', [KoleksiController::class, 'index']);
Route::post('/generate-barcode', [KoleksiController::class, 'generate']);

Route::get('/laporan/siswa-terajin', [LaporanController::class, 'siswaTerajin']);
Route::get('/laporan/siswa-terajin/pdf', [LaporanController::class, 'exportPdfSiswaTerajin']);

Route::get('/laporan/kunjungan-bulanan', [LaporanController::class, 'kunjunganBulanan']);
Route::get('/laporan/kunjungan-bulanan/pdf', [LaporanController::class, 'exportPdfKunjungan']);

Route::prefix('pustakawan/buku')->group(function () {

    Route::get('/import', [BukuController::class, 'halamanImport'])->name('buku.import.halaman');
    Route::post('/import', [BukuController::class, 'importExcel'])->name('buku.import');

    Route::get('/export', [BukuController::class, 'exportExcel'])->name('buku.export');

    Route::get('/tambah', [BukuController::class, 'create'])->name('buku.create');
    Route::post('/tambah', [BukuController::class, 'store'])->name('buku.store');
});


Route::prefix('koleksi')->group(function () {

    Route::get('/', [KoleksiController::class, 'index'])->name('koleksi.index');

    Route::get('/tambah', [KoleksiController::class, 'create'])->name('koleksi.create');
    Route::post('/tambah', [KoleksiController::class, 'store'])->name('koleksi.store');

    Route::get('/edit/{id}', [KoleksiController::class, 'edit'])->name('koleksi.edit');
    Route::put('/update/{id}', [KoleksiController::class, 'update'])->name('koleksi.update');

    Route::delete('/koleksi/delete/{id}', [KoleksiController::class, 'delete']);
});
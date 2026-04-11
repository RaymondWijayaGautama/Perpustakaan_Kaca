<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KoleksiController;
use App\Http\Controllers\Api\LaporanController;

Route::get('/generate-barcode', [KoleksiController::class, 'index']);
Route::post('/generate-barcode', [KoleksiController::class, 'generate']);
Route::get('/laporan/siswa-terajin', [LaporanController::class, 'siswaTerajin']);
Route::get('/laporan/siswa-terajin/pdf', [LaporanController::class, 'exportPdfSiswaTerajin']);

Route::get('/', function () {
    return view('welcome');
});
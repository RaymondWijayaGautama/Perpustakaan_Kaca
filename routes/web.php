<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanController;

Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        
        $siswa = DB::table('mst_siswa')->get();
        
        return $siswa;
        
    } catch (\Exception $e) {
        return "Gagal nyambung ke database nih. Error: " . $e->getMessage();
    }
});

Route::get('/laporan/siswa-terajin', [LaporanController::class, 'siswaTerajin']);
Route::get('/laporan/kunjungan-bulanan', [LaporanController::class, 'kunjunganBulanan']);
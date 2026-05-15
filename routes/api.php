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
use App\Http\Controllers\Pustakawan\PengembalianController;
use App\Http\Controllers\Api\BookingController;
// 1. Tampil Data
Route::get('/laporan', [LaporanController::class, 'getLaporan']);
Route::post('/laporan/tambah', [LaporanController::class, 'store']);
Route::put('/laporan/ubah/{isbn}', [LaporanController::class, 'update']);
Route::delete('/laporan/hapus/{isbn}', [LaporanController::class, 'destroy']);

// Booking Group
Route::get('/bookings', [BookingController::class, 'index']);      
Route::post('/bookings/store', [BookingController::class, 'store']);
Route::put('/bookings/cancel/{id}', [BookingController::class, 'cancel']);


// --- AUTH & USER ---
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // --- KUNJUNGAN PERPUS ---
    Route::post('/kunjungan/checkin', [\App\Http\Controllers\Api\KunjunganController::class, 'checkIn']);
    Route::post('/kunjungan/checkout', [\App\Http\Controllers\Api\KunjunganController::class, 'checkOut']);
    Route::get('/kunjungan/history', [\App\Http\Controllers\Api\KunjunganController::class, 'history']);
});

// --- LOG SISTEM ---
Route::get('/logs/access', [LogController::class, 'access']);
Route::get('/logs/activity', [LogController::class, 'activity']);
Route::get('/logs/roles', [LogController::class, 'roles']);
Route::get('/log/visitors', [LogController::class, 'visitor']);

// --- TRANSAKSI PEMINJAMAN & PENGEMBALIAN ---
Route::get('/peminjaman/cek-aktif', [PeminjamanController::class, 'cekAktif']);
Route::get('/peminjaman/overdue', [PeminjamanController::class, 'overdue']);
Route::get('/peminjaman/katalog-koleksi', [PeminjamanController::class, 'katalogKoleksi']);
Route::get('/peminjaman', [PeminjamanController::class, 'index']);
Route::post('/peminjaman', [PeminjamanController::class, 'store']);
Route::post('/peminjaman/perpanjang/{id}', [PeminjamanController::class, 'perpanjang']);
Route::put('/peminjaman/{id}', [PeminjamanController::class, 'update']);
Route::delete('/peminjaman/{id}', [PeminjamanController::class, 'destroy']);

Route::get('/pengembalian/history', [PeminjamanController::class, 'historyPengembalian']);
Route::get('/pengembalian', [DashboardController::class, 'getPengembalian']);
Route::get('/pengembalian/export', [PengembalianController::class, 'exportExcel']);
Route::put('/pengembalian/{id}', [PeminjamanController::class, 'updatePengembalian']);
Route::delete('/pengembalian/{id}', [PeminjamanController::class, 'destroyPengembalian']);
Route::post('/pengembalian/batch', [PeminjamanController::class, 'batchReturn']);
Route::post('/pengembalian/scan', [PeminjamanController::class, 'scanPengembalian']);
Route::post('/pengembalian/proses/{id}', [PeminjamanController::class, 'prosesPengembalian']);

// --- DENDA KERUSAKAN ---
Route::get('/denda-kerusakan/cari', [PeminjamanController::class, 'cariPeminjamanDenda']);
Route::post('/denda-kerusakan/simpan', [PeminjamanController::class, 'simpanDendaKerusakan']);

// --- DASHBOARD & ANGGOTA ---
Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
Route::get('/anggota', [DashboardController::class, 'getAnggota']);
Route::get('/anggota/{identifier}', [DashboardController::class, 'getAnggotaByIdentifier']);

// --- MANAJEMEN BUKU (Koleksi Fisik) ---
Route::get('/buku/kategori', [MasterKoleksiController::class, 'options']);
Route::get('/buku', [KoleksiBukuController::class, 'index']);
Route::post('/buku', [KoleksiBukuController::class, 'store']);
Route::get('/buku/{isbn}/copies', [KoleksiBukuController::class, 'copies']);
Route::post('/buku/{isbn}/copies', [KoleksiBukuController::class, 'storeCopy']);
Route::put('/buku/copies/{idCpKoleksi}', [KoleksiBukuController::class, 'updateCopy']);
Route::patch('/buku/copies/{idCpKoleksi}/status', [KoleksiBukuController::class, 'updateCopyStatus']);
Route::delete('/buku/copies/{idCpKoleksi}', [KoleksiBukuController::class, 'destroyCopy']);
Route::put('/buku/{isbn}', [KoleksiBukuController::class, 'update']);
Route::delete('/buku/{isbn}', [KoleksiBukuController::class, 'destroy']);
Route::post('/generate-barcode', [KoleksiBukuController::class, 'generateBarcode']);
// --- KATEGORI BUKU (KoleksiController) ---
// PERBAIKAN KUNCI: Mengubah /koleksi menjadi /kategori agar tidak bentrok
Route::get('/kategori', [KoleksiController::class, 'index']);
Route::post('/kategori', [KoleksiController::class, 'store']);
Route::put('/kategori/{id}', [KoleksiController::class, 'update']);
Route::delete('/kategori/{id}', [KoleksiController::class, 'destroy']);

// --- LAPORAN ---
Route::get('/laporan', [LaporanController::class, 'getLaporan']); 
Route::get('/laporan/peminjaman-bulanan', [LaporanController::class, 'statistikPeminjamanBulanan']);
Route::get('/laporan/export-pdf-peminjaman-bulanan', [LaporanController::class, 'exportPdfPeminjamanBulanan']);
Route::get('/laporan/peminjaman-guru', [LaporanController::class, 'laporanPeminjamanGuru']);
Route::get('/laporan/export-pdf-peminjaman-guru', [LaporanController::class, 'exportPdfPeminjamanGuru']);
Route::get('/laporan/kunjungan-distribusi-kelas', [LaporanController::class, 'distribusiKunjunganKelas']);
Route::get('/laporan/export-pdf-kunjungan-distribusi-kelas', [LaporanController::class, 'exportPdfDistribusiKunjunganKelas']);
Route::get('/laporan/kunjungan-distribusi-hari', [LaporanController::class, 'distribusiKunjunganHari']);
Route::get('/laporan/export-pdf-kunjungan-distribusi-hari', [LaporanController::class, 'exportPdfDistribusiKunjunganHari']);
Route::get('/laporan/inventarisasi-buku-baru', [LaporanController::class, 'inventarisasiBukuBaru']);
Route::get('/laporan/export-pdf-inventarisasi-buku-baru', [LaporanController::class, 'exportPdfInventarisasiBukuBaru']);
Route::get('/laporan/siswa-terajin', [LaporanController::class, 'siswaTerajin']);
Route::get('/laporan/kunjungan-bulanan', [LaporanController::class, 'kunjunganBulanan']);
Route::get('/laporan/buku-terpopuler', [LaporanController::class, 'bukuTerpopuler']);
Route::get('/laporan/kategori-populer', [LaporanController::class, 'kategoriPopuler']);
Route::post('/laporan/tambah', [LaporanController::class, 'store']);
Route::put('/laporan/ubah/{isbn}', [LaporanController::class, 'update']);
Route::delete('/laporan/hapus/{isbn}', [LaporanController::class, 'destroy']);
Route::get('/laporan/download/{isbn}', [LaporanController::class, 'downloadLaporan']);
Route::get('/laporan/statistik-kunjungan-bulanan', [LaporanController::class, 'StatistikKunjunganBulanan']);
Route::get('/laporan/export-pdf-statistik-kunjungan-bulanan', [\App\Http\Controllers\Api\LaporanController::class, 'exportPdfStatistikKunjunganBulanan']);
Route::get('/laporan/export-pdf-peminjaman-kelas', [LaporanController::class, 'exportPdfPeminjamanKelas']);
Route::get('/laporan/statistik-peminjaman-kelas', [LaporanController::class, 'statistikPeminjamanKelas']);
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
Route::post('/buku/{isbn}/copies', [KoleksiBukuController::class, 'storeCopy']);
Route::put('/buku/copies/{idCpKoleksi}', [KoleksiBukuController::class, 'updateCopy']);
Route::patch('/buku/copies/{idCpKoleksi}/status', [KoleksiBukuController::class, 'updateCopyStatus']);
Route::delete('/buku/copies/{idCpKoleksi}', [KoleksiBukuController::class, 'destroyCopy']);
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
Route::put('/pemusnahan/{id}', [DashboardController::class, 'updatePemusnahan']);
Route::patch('/pemusnahan/{id}', [DashboardController::class, 'updateStatusPemusnahan']);
Route::patch('/pemusnahan/{id}/konfirmasi', [DashboardController::class, 'confirmPemusnahan']);


<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Picqer\Barcode\BarcodeGeneratorHTML;

Route::get('/test-db', function () {
    try {
        // Test koneksi
        DB::connection()->getPdo();
        
        $siswa = DB::table('mst_siswa')->get();
        
        return $siswa;
        
    } catch (\Exception $e) {
        return "Gagal nyambung ke database nih. Error: " . $e->getMessage();
    }
});


Route::get('/generate-barcode', [App\Http\Controllers\KoleksiController::class, 'index']);
Route::post('/generate-barcode', [App\Http\Controllers\KoleksiController::class, 'generate']);
Route::get('/seed-buku', function () {

    try {
        //http://localhost:8000/seed-buku
        DB::table('ref_koleksi')->insertOrIgnore([
            'id_ref_koleksi' => 1,
            'is_delete' => 0,
            'deskripsi' => 'Buku Referensi IT'
        ]);
        DB::table('mst_koleksi_laporan')->insertOrIgnore([
            'id_mst_laporan' => 1,
            'is_delete' => 0
        ]);
        DB::table('mst_koleksi_buku')->insertOrIgnore([
            'ISBN' => '978-602-123',
            'judul_koleksi' => 'Buku apa aja',
            'pengarang' => 'Raymond',
            'penerbit' => 'Penerbit Kaca',
            'tahun' => '2026',
            'nb_koleksi' => 1,
            'tgl_masuk_koleksi' => '2026-04-09',
            'jumlah_ekslempar' => 10,
            'jumlah_halaman' => 150,
            'ukuran_buku' => 'A5',
            'bibliografi' => '-',
            'indeks_awal_akhir' => 0,
            'keterangan_buku' => 'Buku untuk testing Pustakawan',
            'no_rak_buku' => 'RAK-01',
            'is_delete' => 0,
            'id_ref_koleksi' => 1
        ]);

        return "<h2 style='color: green; text-align: center; margin-top: 50px;'>Data Buku Berhasil Diinput!</h2>";
    } catch (\Exception $e) {
        return "Gagal menyuntikkan data: " . $e->getMessage();
    }
});
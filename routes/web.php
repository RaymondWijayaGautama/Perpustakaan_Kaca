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


Route::get('/test-barcode', function () {

    $isbn = '978-602-8519-93-9'; 
    $kodeSistemUnik = $isbn . '-001'; 
    $generator = new BarcodeGeneratorHTML();
    $gambarBarcode = $generator->getBarcode($kodeSistemUnik, $generator::TYPE_CODE_128, 2, 50, 'black');
    return "
        <div style='text-align: center; margin-top: 50px; font-family: Arial;'>
            <h2>Preview Generate Barcode Pustakawan</h2>
            <div style='display: inline-block; padding: 20px; border: 1px solid #ccc;'>
                {$gambarBarcode}
                <p style='letter-spacing: 2px; margin-top: 10px;'><b>{$kodeSistemUnik}</b></p>
            </div>
            <p style='color: green;'>Status: Siap disimpan ke tabel <b>cp_koleksi</b></p>
        </div>
    ";
});

Route::get('/generate-barcode', [App\Http\Controllers\KoleksiController::class, 'index']);
Route::post('/generate-barcode', [App\Http\Controllers\KoleksiController::class, 'generate']);
Route::get('/seed-buku', function () {
    
    try {
        // 1. Suntik Data Kategori (Bypass error)
        DB::table('ref_koleksi')->insertOrIgnore([
            'id_ref_koleksi' => 1,
            'is_delete' => 0,
            'deskripsi' => 'Buku Referensi IT'
        ]);

        // 2. Suntik Data Laporan (Bypass error)
        DB::table('mst_koleksi_laporan')->insertOrIgnore([
            'id_mst_laporan' => 1,
            'is_delete' => 0
        ]);

        // 3. Suntik Data Master Buku-nya
        DB::table('mst_koleksi_buku')->insertOrIgnore([
            'ISBN' => '978-602-111',
            'judul_koleksi' => 'Buku Sakti Laravel',
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

        return "<h2 style='color: green; text-align: center; margin-top: 50px;'>Data Master Berhasil Disuntikkan! ✅</h2>";
    } catch (\Exception $e) {
        return "Gagal menyuntikkan data: " . $e->getMessage();
    }
});
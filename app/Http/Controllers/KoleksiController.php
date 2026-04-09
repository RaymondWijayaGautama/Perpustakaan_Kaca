<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CpKoleksi;
use Picqer\Barcode\BarcodeGeneratorHTML;

class KoleksiController extends Controller
{
    // 1. Menampilkan halaman form input untuk Pustakawan
    public function index()
    {
        return view('tambah_barcode'); 
    }

    // 2. Memproses inputan, menyimpan ke DB, dan men-generate Barcode
    public function generate(Request $request)
    {
        // Menyimpan data buku fisik baru ke tabel cp_koleksi
        $koleksiBaru = CpKoleksi::create([
            'status_buku' => 'Tersedia', // Default saat buku baru masuk
            'ISBN' => $request->isbn,
            'id_mst_laporan' => 1 // Anggap saja ID laporan 1 dulu sementara
        ]);

        // Membuat Kode Sistem Unik (Gabungan ISBN + ID unik dari tabel)
        // Formatnya akan menjadi misal: 978-602-1, 978-602-2, dst. (DIJAMIN UNIK)
        $kodeSistemUnik = $koleksiBaru->ISBN . '-' . $koleksiBaru->id_cp_koleksi;

        // Generate Gambar Barcode
        $generator = new BarcodeGeneratorHTML();
        $gambarBarcode = $generator->getBarcode($kodeSistemUnik, $generator::TYPE_CODE_128, 2, 50, 'black');

        // Tampilkan hasilnya ke layar Pustakawan
        return "
            <div style='text-align: center; margin-top: 50px; font-family: Arial;'>
                <h2 style='color: green;'>Sukses! Buku Fisik Berhasil Didaftarkan</h2>
                <div style='display: inline-block; padding: 20px; border: 1px solid #ccc; margin-top: 20px;'>
                    {$gambarBarcode}
                    <p style='letter-spacing: 2px; margin-top: 10px;'><b>{$kodeSistemUnik}</b></p>
                </div>
                <p>Data tersimpan di tabel <b>cp_koleksi</b> dengan ID: <b>{$koleksiBaru->id_cp_koleksi}</b></p>
                <br>
                <a href='/generate-barcode' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Input Buku Lainnya</a>
            </div>
        ";
    }
}
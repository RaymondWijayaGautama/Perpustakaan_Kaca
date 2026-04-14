<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CpKoleksi;
use Picqer\Barcode\BarcodeGeneratorHTML;

class KoleksiController extends Controller
{
    public function index()
    {
        return view('tambah_barcode'); 
    }

    public function generate(Request $request)
    {
        $koleksiBaru = CpKoleksi::create([
            'status_buku' => 'Tersedia', 
            'ISBN' => $request->isbn,
            'id_mst_laporan' => 1 
        ]);
        $generator = new BarcodeGeneratorHTML();
        $gambarBarcode = $generator->getBarcode($koleksiBaru->ISBN, $generator::TYPE_CODE_128, 2, 60, 'black');

        return "
        <div style='width: 100%; display: flex; flex-direction: column; align-items: center;'>
            <div style='width: 100%; padding: 10px; background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; border-radius: 8px; font-weight: bold; font-size: 14px; margin-bottom: 24px; text-align: center;'>
                Buku Berhasil Didaftarkan
            </div>
            
            <div style='display: flex; justify-content: center; width: 100%; margin-bottom: 12px; background: white; padding: 10px;'>
                {$gambarBarcode}
            </div>
            
            <p style='font-family: monospace; letter-spacing: 4px; font-weight: bold; font-size: 15px; color: #1a1a1a; margin-top: 0; margin-bottom: 20px;'>
                {$koleksiBaru->ISBN}
            </p>
            
            <div style='margin-top: 10px; border-top: 1px dashed #d1d5db; width: 100%; padding-top: 15px; font-size: 10px; color: #6b7280; text-transform: uppercase; font-weight: bold; letter-spacing: 1px; text-align: center;'>
                Tersimpan dengan ID Data: <span style='color: #1f2937;'>{$koleksiBaru->id_cp_koleksi}</span>
            </div>
        </div>
        ";
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CpKoleksi;
use Picqer\Barcode\BarcodeGeneratorHTML;

class KoleksiController extends Controller
{
<<<<<<< Updated upstream
    public function index()
    {
        return view('tambah_barcode'); 
    }
    public function generate(Request $request)
=======
public function generate(Request $request)
>>>>>>> Stashed changes
    {
        $koleksiBaru = CpKoleksi::create([
            'status_buku' => 'Tersedia', 
            'ISBN' => $request->isbn,
            'id_mst_laporan' => 1 
        ]);
        $kodeSistemUnik = $koleksiBaru->ISBN . '-' . $koleksiBaru->id_cp_koleksi;
<<<<<<< Updated upstream
        $generator = new BarcodeGeneratorHTML();
        $gambarBarcode = $generator->getBarcode($kodeSistemUnik, $generator::TYPE_CODE_128, 2, 50, 'black');

=======
        // Generate Barcode pakai Kode Unik tersebut
        $generator = new BarcodeGeneratorHTML();
        $gambarBarcode = $generator->getBarcode($kodeSistemUnik, $generator::TYPE_CODE_128, 2, 60, 'black');
>>>>>>> Stashed changes
        return "
        <style>
            @media print {
                /* Paksa browser nge-print warna background (hitam) barcodenya */
                .force-print-color, .force-print-color * {
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
            }
        </style>

        <div class='flex flex-col items-center justify-center w-full pb-2 force-print-color'>
            
            <div class='bg-green-50 text-green-700 px-4 py-2 rounded-lg text-sm font-bold mb-5 w-full text-center border border-green-200 shadow-sm print:hidden'>
                Buku Berhasil Didaftarkan
            </div>
            
            <div class='bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col items-center w-max'>
                <div class='mix-blend-multiply'>{$gambarBarcode}</div>
                <p class='tracking-[0.15em] font-mono text-[#1A1A1A] font-bold mt-3 text-sm'>{$kodeSistemUnik}</p>
            </div>
            
<<<<<<< Updated upstream
            <p class='text-[10px] text-gray-400 mt-5 uppercase tracking-wider font-bold print:hidden'>
                Tersimpan dengan ID Data: <span class='text-gray-600'>{$koleksiBaru->id_cp_koleksi}</span>
            </p>
=======
            <p style='font-family: monospace; letter-spacing: 4px; font-weight: bold; font-size: 15px; color: #1a1a1a; margin-top: 0; margin-bottom: 20px;'>
                {$kodeSistemUnik}
            </p>
            
            <div style='margin-top: 10px; border-top: 1px dashed #d1d5db; width: 100%; padding-top: 15px; font-size: 10px; color: #6b7280; text-transform: uppercase; font-weight: bold; letter-spacing: 1px; text-align: center;'>
                Tersimpan dengan ID Fisik: <span style='color: #1f2937;'>{$koleksiBaru->id_cp_koleksi}</span>
            </div>
>>>>>>> Stashed changes
        </div>
        ";
    }
}
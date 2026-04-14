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
        $kodeSistemUnik = $koleksiBaru->ISBN . '-' . $koleksiBaru->id_cp_koleksi;
        $generator = new BarcodeGeneratorHTML();
        $gambarBarcode = $generator->getBarcode($kodeSistemUnik, $generator::TYPE_CODE_128, 2, 50, 'black');

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
            
            <p class='text-[10px] text-gray-400 mt-5 uppercase tracking-wider font-bold print:hidden'>
                Tersimpan dengan ID Data: <span class='text-gray-600'>{$koleksiBaru->id_cp_koleksi}</span>
            </p>
        </div>
        ";
    }
}

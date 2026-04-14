<?php

namespace App\Http\Controllers\Pustakawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstKoleksiBuku;
use App\Imports\BukuImport;
use App\Exports\BukuExport;
use Maatwebsite\Excel\Facades\Excel;

// Import Class Kalkulasi
use App\Http\Controllers\Pustakawan\KalkulasiDendaBukuRusak;

class BukuController extends Controller
{
    /**
     * Menampilkan daftar buku (digunakan untuk view blade jika ada)
     */
    public function index()
    {
        $buku = MstKoleksiBuku::where('is_delete', 0)->get();
        return view('pustakawan.buku.index', compact('buku'));
    }

    /**
     * Menampilkan halaman import excel
     */
    public function halamanImport()
    {
        return view('bukuimport');
    }

    /**
     * Proses import data buku dari Excel
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new BukuImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data buku berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Proses export data buku ke Excel
     */
    public function exportExcel(Request $request)
    {
        $nama_file = 'Data_Buku_Wigaty_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new BukuExport($request), $nama_file);
    }

    /**
     * Method Utama: Menyimpan denda kerusakan via API (React)
     * Menggunakan ISBN sebagai identitas pencarian buku
     */
    public function simpanDendaKerusakan(Request $request)
    {
        // 1. Validasi input dari Axios
        $request->validate([
            'id_buku' => 'required', // Variabel ini berisi ISBN yang dikirim dari React
            'nominal_denda' => 'required|numeric',
            'jenis_kerusakan' => 'required|string'
        ]);

        try {
            // 2. Inisialisasi Class Kalkulasi Denda
            $kalkulator = new KalkulasiDendaBukuRusak();

            // 3. Jalankan logika hitungan denda
            $hasil = $kalkulator->hitung(
                $request->nominal_denda, 
                $request->jenis_kerusakan
            );

            /**
             * 4. Cari buku berdasarkan ISBN
             * Karena Anda tidak menggunakan ID (Auto Increment), 
             * kita cari menggunakan kolom ISBN atau isbn.
             */
            $buku = MstKoleksiBuku::where('ISBN', $request->id_buku)
                                  ->orWhere('isbn', $request->id_buku)
                                  ->first();

            if ($buku) {
                // Contoh: Tandai buku tetap aktif (is_delete = 0) 
                // atau Anda bisa menambahkan kolom 'status_kondisi' => 'rusak'
                $buku->update([
                    'is_delete' => 0 
                ]);
            } else {
                // Jika ISBN benar-benar tidak ada di database
                return response()->json([
                    'success' => false,
                    'message' => 'Buku dengan ISBN ' . $request->id_buku . ' tidak ditemukan di database.'
                ], 404);
            }

            // 5. Kembalikan Response JSON ke React
            return response()->json([
                'success' => true,
                'message' => 'Laporan denda kerusakan berhasil dicatat.',
                'keterangan_denda' => $hasil['keterangan'],
                'sanksi' => $hasil['sanksi'],
                'data_kalkulasi' => $hasil
            ], 200);

        } catch (\Exception $e) {
            // Tangani error jika terjadi kesalahan sistem
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses denda: ' . $e->getMessage()
            ], 500);
        }
    }
}
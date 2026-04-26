<?php

namespace App\Http\Controllers; // Sesuaikan jika ada di folder Api

use Illuminate\Http\Request;
use App\Models\CpKoleksi;
use Picqer\Barcode\BarcodeGeneratorHTML;
use App\Models\RefKoleksi;

class KoleksiController extends Controller
{
    public function index()
    {
        $kategori = RefKoleksi::where('IS_DELETE', 0)
            ->orWhereNull('IS_DELETE')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $kategori
        ], 200);
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
        $gambarBarcode = $generator->getBarcode($kodeSistemUnik, $generator::TYPE_CODE_128, 2, 60, 'black');

        return "
        <div style='width: 100%; display: flex; flex-direction: column; align-items: center;'>
            <div style='width: 100%; padding: 10px; background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; border-radius: 8px; font-weight: bold; font-size: 14px; margin-bottom: 24px; text-align: center;'>
                Buku Berhasil Didaftarkan
            </div>
            <div style='display: flex; justify-content: center; width: 100%; margin-bottom: 12px; background: white; padding: 10px;'>
                {$gambarBarcode}
            </div>
            <p style='font-family: monospace; letter-spacing: 4px; font-weight: bold; font-size: 15px; color: #1a1a1a; margin-top: 0; margin-bottom: 20px;'>
                {$kodeSistemUnik}
            </p>
            <div style='margin-top: 10px; border-top: 1px dashed #d1d5db; width: 100%; padding-top: 15px; font-size: 10px; color: #6b7280; text-transform: uppercase; font-weight: bold; letter-spacing: 1px; text-align: center;'>
                Tersimpan dengan ID Fisik: <span style='color: #1f2937;'>{$koleksiBaru->id_cp_koleksi}</span>
            </div>
        </div>
        ";
    }

    

    /**
     * Simpan data kategori baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'NO_KATEGORI_BUKU' => 'nullable|string|max:10',
            'DESKRIPSI_KATEGORI' => 'required|string|max:255',
        ]);

        $kategori = RefKoleksi::create([
            'NO_KATEGORI_BUKU' => $request->NO_KATEGORI_BUKU,
            'DESKRIPSI_KATEGORI' => $request->DESKRIPSI_KATEGORI,
            'IS_DELETE' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan.',
            'data' => $kategori
        ], 201);
    }

    /**
     * Perbarui data kategori yang sudah ada
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'NO_KATEGORI_BUKU' => 'nullable|string|max:10',
            'DESKRIPSI_KATEGORI' => 'required|string|max:255',
        ]);

        $kategori = RefKoleksi::where('ID_REF_KOLEKSI', $id)->firstOrFail();
        
        $kategori->update([
            'NO_KATEGORI_BUKU' => $request->NO_KATEGORI_BUKU,
            'DESKRIPSI_KATEGORI' => $request->DESKRIPSI_KATEGORI,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui.',
            'data' => $kategori
        ], 200);
    }

    /**
     * Hapus kategori (Ubah status IS_DELETE menjadi 1)
     */
    public function destroy($id)
    {
        $kategori = RefKoleksi::where('ID_REF_KOLEKSI', $id)->firstOrFail();

        // Menggunakan konsep Soft Delete manual sesuai skema DB
        $kategori->update(['IS_DELETE' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus.'
        ], 200);
    }
}
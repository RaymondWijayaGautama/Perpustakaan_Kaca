<?php

namespace App\Http\Controllers\Pustakawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MstKoleksiBuku;
use App\Imports\BukuImport;

class BukuController extends Controller
{
    public function index()
    {
        $buku = MstKoleksiBuku::where('is_delete', 0)->get();
        return view('pustakawan.buku.index', compact('buku'));
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            if (!class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
                return redirect()->back()->with('error', 'Paket import Excel belum tersedia di server.');
            }

            \Maatwebsite\Excel\Facades\Excel::import(new BukuImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data buku berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
    public function halamanImport()
    {
        return view('bukuimport');
    }

    public function exportExcel(Request $request)
    {
        $query = DB::table('mst_koleksi_buku as buku')
            ->leftJoin('ref_koleksi as kategori', 'buku.id_ref_koleksi', '=', 'kategori.id_ref_koleksi')
            ->where('buku.is_delete', 0)
            ->where('buku.id_ref_koleksi', '!=', 4)
            ->select(
                'buku.ISBN',
                'buku.judul_koleksi',
                'buku.pengarang',
                'buku.penerbit',
                'buku.tahun',
                'buku.no_rak_buku',
                'buku.jumlah_ekslempar',
                'kategori.deskripsi as kategori'
            );

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('buku.judul_koleksi', 'like', "%{$search}%")
                    ->orWhere('buku.pengarang', 'like', "%{$search}%")
                    ->orWhere('buku.ISBN', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('buku.id_ref_koleksi', $request->kategori);
        }

        $dataBuku = $query
            ->orderBy('buku.judul_koleksi')
            ->get();

        $namaFile = 'Data_Buku_KACA_' . date('Y-m-d_H-i-s') . '.xls';

        $html = view('pustakawan.buku.export_excel', [
            'dataBuku' => $dataBuku,
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$namaFile}\"",
        ]);
    }
}

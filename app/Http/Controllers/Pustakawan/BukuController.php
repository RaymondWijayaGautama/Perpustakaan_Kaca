<?php

namespace App\Http\Controllers\Pustakawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstKoleksiBuku;
use App\Imports\BukuImport;
use Maatwebsite\Excel\Facades\Excel;

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
            Excel::import(new BukuImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data buku berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
    public function halamanImport()
    {
        return view('bukuimport');
    }
}
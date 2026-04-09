<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstKoleksiLaporan;
use Illuminate\Support\Facades\Validator;

class LaporanController extends Controller
{
    // Fungsi untuk Task 15: Menambah Laporan
    public function store(Request $request)
    {
        // 1. Validasi sesuai spesifikasi tabel (Wajib isi & Max 10MB)
        $validator = Validator::make($request->all(), [
            'judul_laporan' => 'required|string|max:255',
            'penulis_laporan' => 'required|string|max:100',
            'tahun_laporan' => 'required|digits:4',
            'file_laporan' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10240 KB = 10 MB
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'pesan' => $validator->errors()], 400);
        }

        // 2. Proses Upload File ke dalam folder server (storage)
        $file = $request->file('file_laporan');
        // Buat nama file unik agar tidak bentrok
        $namaFile = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName()); 
        $path = $file->storeAs('public/laporan', $namaFile); // Tersimpan di folder storage/app/public/laporan

        // 3. Simpan data ke Database
        $laporan = MstKoleksiLaporan::create([
            'judul_laporan' => $request->judul_laporan,
            'penulis_laporan' => $request->penulis_laporan,
            'tahun_laporan' => $request->tahun_laporan,
            'file_path' => $namaFile,
            'is_delete' => 0
        ]);

        // 4. Kembalikan respon ke React JS
        return response()->json([
            'status' => 'success',
            'pesan' => 'Laporan berhasil ditambahkan!',
            'data' => $laporan
        ], 201);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstKoleksiLaporan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Siswa;


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

    public function update(Request $request, $id)
    {
        $laporan = MstKoleksiLaporan::find($id);

        if (!$laporan) {
            return response()->json(['status' => 'error', 'pesan' => 'Data laporan tidak ditemukan!'], 404);
        }
        $validator = Validator::make($request->all(), [
            'judul_laporan' => 'required|string|max:255',
            'penulis_laporan' => 'required|string|max:100',
            'tahun_laporan' => 'required|digits:4',
            'file_laporan' => 'nullable|file|mimes:pdf,doc,docx|max:10240', 
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'pesan' => $validator->errors()], 400);
        }
        if ($request->hasFile('file_laporan')) {
            if (Storage::exists('public/laporan/' . $laporan->file_path)) {
                Storage::delete('public/laporan/' . $laporan->file_path);
            }
            $file = $request->file('file_laporan');
            $namaFile = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName()); 
            $file->storeAs('public/laporan', $namaFile);

            $laporan->file_path = $namaFile;
        }

        $laporan->judul_laporan = $request->judul_laporan;
        $laporan->penulis_laporan = $request->penulis_laporan;
        $laporan->tahun_laporan = $request->tahun_laporan;
        $laporan->save(); 

        return response()->json([
            'status' => 'success',
            'pesan' => 'Data laporan berhasil diubah!',
            'data' => $laporan
        ], 200);
    }

    public function destroy($id)
    {
        $laporan = MstKoleksiLaporan::find($id);

        if (!$laporan) {
            return response()->json(['status' => 'error', 'pesan' => 'Data laporan tidak ditemukan!'], 404);
        }
        $dipakai = \App\Models\CpKoleksi::where('id_mst_laporan', $id)->exists();
        if ($dipakai) {
            return response()->json([
                'status' => 'error', 
                'pesan' => 'Gagal! Laporan tidak bisa dihapus karena sedang terhubung dengan buku fisik.'
            ], 400); 
        }

        if (Storage::exists('public/laporan/' . $laporan->file_path)) {
            Storage::delete('public/laporan/' . $laporan->file_path);
        }

        $laporan->is_delete = 1;
        $laporan->save();
        return response()->json([
            'status' => 'success',
            'pesan' => 'Data laporan berhasil dihapus!'
        ], 200);
    }

    public function siswaTerajin(){
        $siswaTerajin = Siswa::withCount('peminjaman')
            ->orderBy('peminjaman_count', 'desc')
            ->take(10)
            ->get();

            return view('laporan.siswa_terajin', compact('siswaTerajin'));
    }

    public function kunjunganBulanan(){
        $laporanKunjungan = \app\Models\Kunjungan::select(
            DB::raw('MONTHNAME(start_kunjungan) as bulan'),
            DB::raw('MONTH(start_kunjungan) as urutan_bulan'),
            DB::raw('COUNT(*) as jumlah_kunjungan')
        )
        ->groupBy('bulan', 'urutan_bulan')
        ->orderBy('urutan_bulan', 'asc')
        ->get();

        return view('laporan.kunjungan_bulanan', compact('laporanKunjungan'));
    }
}
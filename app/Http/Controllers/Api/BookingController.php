<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
   public function store(Request $request)
{
    try {
        // 1. Cari siswa pakai NISN (Pastikan huruf gede semua sesuai gambar DB lo)
        $siswa = DB::table('mst_siswa')
            ->where('NISN_SISWA', $request->id_siswa_tetap) 
            ->first();

        if (!$siswa) {
            return response()->json([
                'message' => 'NISN ' . $request->id_siswa_tetap . ' nggak terdaftar di database!'
            ], 404);
        }

        // 2. Ambil ID aslinya (Pake huruf gede ID_SISWA_TETAP sesuai gambar DB)
        $realId = $siswa->ID_SISWA_TETAP;

        // 3. Simpan ke tr_booking
        // Cek juga nama kolom di tabel tr_booking lo, harusnya gede semua kalau konsisten
        DB::table('tr_booking')->insert([
            'id_cp_koleksi'  => $request->id_cp_koleksi,
            'id_siswa_tetap' => $realId,
            'tgl_booking'    => now(),
            'status_booking' => 'Aktif',
            'created_at'     => now(),
            'updated_at'     => now(),
            'expired_at'     => now()->addDays(2),
        ]);

        return response()->json([
            'message' => 'Booking sukses buat ' . $siswa->NAMA_SISWA_TETAP
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Gagal total!',
            'error_detail' => $e->getMessage() 
        ], 500);
    }
}

public function cancel($id)
{
    try {
        $booking = DB::table('tr_booking')->where('id_booking', $id)->first();

        if (!$booking) {
            return response()->json(['message' => 'Data tidak ada'], 404);
        }

        // 1. Update status (Ini yang bikin di gambar lo jadi merah/dibatalkan)
        DB::table('tr_booking')->where('id_booking', $id)->update([
            'status_booking' => 'Dibatalkan',
            'updated_at' => now(),
        ]);
        
        // 2. Simpan Log (Penyebab Error 500 ada di sini)
        try {
            DB::table('log_activity')->insert([
                'aktivitas' => 'Membatalkan booking ID: ' . $id,
                // Gunakan fallback 'Sistem' jika ID_SISWA_TETAP tidak terbaca
                'user' => $booking->ID_SISWA_TETAP ?? 'Sistem', 
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Jika log gagal, biarkan saja, jangan bikin aplikasi crash
        }

        return response()->json(['message' => 'Booking berhasil dibatalkan']);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Gagal total',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function index(Request $request)
    {
        $role = $request->query('role', 'pustakawan');
        $idPemustaka = $request->query('id_siswa_tetap');

        $query = DB::table('tr_booking as booking')
            ->join('mst_siswa as siswa', 'booking.id_siswa_tetap', '=', 'siswa.id_siswa_tetap')
            ->join('cp_koleksi as copy', 'booking.id_cp_koleksi', '=', 'copy.id_cp_koleksi')
            ->join('mst_koleksi_buku as buku', 'copy.ISBN', '=', 'buku.ISBN')
            ->select(
                'booking.id_booking',
                'booking.tgl_booking',
                'booking.status_booking',
                'siswa.nama_siswa_tetap',
                'siswa.id_siswa_tetap',
                'buku.judul_koleksi'
            );

        if ($request->status_booking) {
            $query->where('booking.status_booking', $request->status_booking);
        }

        if ($role === 'pemustaka') {
            $query->where('booking.id_siswa_tetap', $idPemustaka);
        }

        $booking = $query->orderBy('booking.tgl_booking', 'desc')->paginate(10);
        return response()->json($booking);
    }
}
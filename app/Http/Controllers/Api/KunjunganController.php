<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrKunjunganPerpu;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KunjunganController extends Controller
{
    /**
     * Catat kedatangan pengunjung hari ini.
     * Dibatasi hanya bisa sekali dalam sehari sesuai zona waktu Asia/Jakarta.
     */
    public function checkIn(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Tentukan ID Siswa atau NIP Karyawan berdasarkan jenis user
        $idSiswa = null;
        $nipKaryawan = null;

        if (isset($user->ID_SISWA_TETAP)) {
            $idSiswa = $user->ID_SISWA_TETAP;
        } elseif (isset($user->NIP_KARYAWAN)) {
            $nipKaryawan = $user->NIP_KARYAWAN;
        } else {
            return response()->json(['message' => 'Tipe akun tidak valid untuk check-in.'], 400);
        }

        // Pastikan pengecekan hari ini menggunakan zona waktu yang sama (WIB)
        $tz = 'Asia/Jakarta';
        $today = Carbon::now($tz)->startOfDay();

        // Cek apakah sudah pernah ada record kunjungan yang DIMULAI pada hari ini
        $alreadyCheckedIn = TrKunjunganPerpu::whereDate('START_KUNJUNGAN', $today->toDateString())
            ->where(function($query) use ($idSiswa, $nipKaryawan) {
                if ($idSiswa) {
                    $query->where('ID_SISWA_TETAP', $idSiswa);
                } else {
                    $query->where('NIP_KARYAWAN', $nipKaryawan);
                }
            })->exists();

        if ($alreadyCheckedIn) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-in hari ini. Sesuai aturan, kunjungan hanya dapat dicatat sekali dalam sehari.'
            ], 422);
        }

        try {
            // Catat kunjungan dengan waktu sekarang (WIB)
            $kunjungan = TrKunjunganPerpu::create([
                'ID_SISWA_TETAP' => $idSiswa,
                'NIP_KARYAWAN' => $nipKaryawan,
                'START_KUNJUNGAN' => Carbon::now($tz)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-in kunjungan berhasil dicatat.',
                'data' => $kunjungan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat kunjungan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Catat kepulangan pengunjung hari ini (checkout).
     */
    public function checkOut(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $idSiswa = $user->ID_SISWA_TETAP ?? null;
        $nipKaryawan = $user->NIP_KARYAWAN ?? null;
        
        $tz = 'Asia/Jakarta';
        $today = Carbon::now($tz)->toDateString();

        // Cari record check-in hari ini (WIB) yang belum di check-out
        $kunjungan = TrKunjunganPerpu::whereDate('START_KUNJUNGAN', $today)
            ->where(function($query) use ($idSiswa, $nipKaryawan) {
                if ($idSiswa) {
                    $query->where('ID_SISWA_TETAP', $idSiswa);
                } else {
                    $query->where('NIP_KARYAWAN', $nipKaryawan);
                }
            })->first();

        if (!$kunjungan) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan check-in hari ini (WIB).'
            ], 422);
        }

        if ($kunjungan->END_KUNJUNGAN) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-out hari ini.'
            ], 422);
        }

        $kunjungan->update([
            'END_KUNJUNGAN' => Carbon::now($tz)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out berhasil. Terima kasih atas kunjungan Anda!',
            'data' => $kunjungan
        ]);
    }

    /**
     * Ambil riwayat kunjungan pengguna yang login.
     */
    public function history(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $query = TrKunjunganPerpu::orderBy('START_KUNJUNGAN', 'desc');

        if (isset($user->ID_SISWA_TETAP)) {
            $query->where('ID_SISWA_TETAP', $user->ID_SISWA_TETAP);
        } elseif (isset($user->NIP_KARYAWAN)) {
            $query->where('NIP_KARYAWAN', $user->NIP_KARYAWAN);
        }

        $history = $query->take(10)->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }
}

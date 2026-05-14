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
     */
    public function checkIn(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Tentukan ID Siswa atau NIP Karyawan berdasarkan jenis user
        $idSiswa = $user->ID_SISWA_TETAP ?? null;
        $nipKaryawan = $user->NIP_KARYAWAN ?? null;

        if (!$idSiswa && !$nipKaryawan) {
            return response()->json(['message' => 'Tipe akun tidak valid untuk check-in.'], 400);
        }

        $today = Carbon::today();

        // Cek apakah sudah pernah check-in hari ini
        $alreadyCheckedIn = TrKunjunganPerpu::whereDate('START_KUNJUNGAN', $today)
            ->where(function($query) use ($idSiswa, $nipKaryawan) {
                if ($idSiswa) {
                    $query->where('ID_SISWA_TETAP', $idSiswa);
                } elseif ($nipKaryawan) {
                    $query->where('NIP_KARYAWAN', $nipKaryawan);
                }
            })->exists();

        if ($alreadyCheckedIn) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-in kunjungan hari ini.'
            ], 422); // 422 Unprocessable Entity
        }

        try {
            // Hotfix: Ensure tr_kunjungan_perpus has AUTO_INCREMENT if missing (sama seperti access_log)
            DB::statement('ALTER TABLE tr_kunjungan_perpus MODIFY ID_KUNJUNGAN INT NOT NULL AUTO_INCREMENT');
        } catch (\Exception $e) {
            // ignore
        }

        // PERBAIKAN: Hanya daftarkan key yang ada nilainya ke dalam array insert
        $dataInsert = [
            'START_KUNJUNGAN' => Carbon::now()
        ];

        if ($idSiswa) {
            $dataInsert['ID_SISWA_TETAP'] = $idSiswa;
        } elseif ($nipKaryawan) {
            $dataInsert['NIP_KARYAWAN'] = $nipKaryawan;
        }

        // Catat kunjungan
        $kunjungan = TrKunjunganPerpu::create($dataInsert);

        return response()->json([
            'success' => true,
            'message' => 'Check-in kunjungan berhasil dicatat.',
            'data' => $kunjungan
        ]);
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
        $today = Carbon::today();

        // Cari record check-in hari ini yang belum di check-out
        $kunjungan = TrKunjunganPerpu::whereDate('START_KUNJUNGAN', $today)
            ->where(function($query) use ($idSiswa, $nipKaryawan) {
                if ($idSiswa) {
                    $query->where('ID_SISWA_TETAP', $idSiswa);
                } elseif ($nipKaryawan) {
                    $query->where('NIP_KARYAWAN', $nipKaryawan);
                }
            })->first();

        if (!$kunjungan) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan check-in hari ini.'
            ], 422);
        }

        if ($kunjungan->END_KUNJUNGAN) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-out hari ini.'
            ], 422);
        }

        $kunjungan->update([
            'END_KUNJUNGAN' => Carbon::now()
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
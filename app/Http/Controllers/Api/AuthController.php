<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http; // Untuk cek ke Google
use App\Models\Karyawan; 
use App\Models\Siswa;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Verifikasi reCAPTCHA ke Google API
        /*
        $recaptchaResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $request->input('g-recaptcha-response'),
        ]);

        if (!$recaptchaResponse->json()['success']) {
            return response()->json(['message' => 'Verifikasi reCAPTCHA gagal.'], 422);
        }
            */

        $identifier = $request->identifier; // Bisa Email atau NISN
        $password = $request->password;
        $role = $request->role; // Terdeteksi otomatis dari React

        if ($role === 'karyawan') {
            // Cari di tabel mst_karyawan
            $user = DB::table('mst_karyawan')
                        ->where('email_karyawan', $identifier)
                        ->first();
            
            if ($user && Hash::check($password, $user->password_karyawan)) {
                return response()->json([
                    'status' => 'success',
                    'user' => $user,
                    'token' => 'boda_token_' . bin2hex(random_bytes(10)) 
                ]);
            }
        } else {
            // Cari di tabel mst_siswa
            $user = DB::table('mst_siswa')
                        ->where('nisn_siswa', $identifier)
                        ->first();
            
            if ($user && Hash::check($password, $user->password_siswa)) {
                return response()->json([
                    'status' => 'success',
                    'user' => $user,
                    'token' => 'boda_token_' . bin2hex(random_bytes(10))
                ]);
            }
        }

        return response()->json(['message' => 'Identitas atau Password salah.'], 401);
    }
}
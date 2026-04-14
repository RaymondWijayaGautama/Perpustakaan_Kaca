<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter; // Wajib ada
use Illuminate\Support\Str;                 // Wajib ada untuk Str::lower()

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Verifikasi reCAPTCHA
        $recaptchaResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $request->input('g-recaptcha-response'),
        ]);

        if (!$recaptchaResponse->json()['success']) {
            return response()->json(['message' => 'Verifikasi reCAPTCHA gagal.'], 422);
        }

        $identifier = $request->identifier;
        $password = $request->password;
        $role = $request->role; 
        $maxAttempts = 5;

        // Membuat kunci unik berdasarkan identifier dan IP user
        $throttleKey = Str::lower($identifier) . '|' . $request->ip();

        // 2. Cek apakah user sudah melampaui batas percobaan
        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'message' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . $seconds . ' detik.'
            ], 429);
        }

        if ($role === 'karyawan') {
            $user = DB::table('mst_karyawan')
                        ->where('nip_karyawan', $identifier)
                        ->where('is_delete', 0)
                        ->first();
            
            if (!$user) {
                return $this->handleFail($throttleKey, $maxAttempts, 'NIP tidak terdaftar.', 404);
            }

            if (!Hash::check($password, $user->password_karyawan)) {
                return $this->handleFail($throttleKey, $maxAttempts, 'Kata sandi salah.', 401);
            }

        } else {
            $user = DB::table('mst_siswa')
                        ->where('nisn_siswa', $identifier)
                        ->where('is_delete', 0)
                        ->first();
            
            if (!$user) {
                return $this->handleFail($throttleKey, $maxAttempts, 'NISN tidak terdaftar.', 404);
            }

            if (!Hash::check($password, $user->password_siswa)) {
                return $this->handleFail($throttleKey, $maxAttempts, 'Kata sandi salah.', 401);
            }
        }

        // 3. Jika login berhasil, hapus catatan percobaan (reset limiter)
        RateLimiter::clear($throttleKey);

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => 'boda_token_' . bin2hex(random_bytes(10)) 
        ]);
    }

    /**
     * Helper untuk mencatat kegagalan dan mengembalikan response sisa percobaan.
     */
    private function handleFail($key, $max, $message, $statusCode)
    {
        RateLimiter::hit($key, 60); // Tambah hit, kunci selama 60 detik jika habis
        $remaining = RateLimiter::remaining($key, $max);

        return response()->json([
            'message' => $message,
            'attempts_left' => $remaining,
            'info' => "Sisa percobaan: $remaining kali."
        ], $statusCode);
    }
}
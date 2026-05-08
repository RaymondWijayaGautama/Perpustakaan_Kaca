<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\MstKaryawan;
use App\Models\MstSiswa;

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

        $identifier = $request->identifier; // NIP atau NISN
        $password = $request->password;
        $role = Str::lower($request->role); 
        $maxAttempts = 5;

        $throttleKey = Str::lower($identifier) . '|' . $request->ip();

        // 2. Rate Limiting
        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'message' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . $seconds . ' detik.'
            ], 429);
        }

        $user = null;

        if ($role === 'karyawan') {
            // Cari berdasarkan NIP_KARYAWAN (Primary Key)
            $user = MstKaryawan::where('NIP_KARYAWAN', $identifier)
                        ->where('IS_DELETE', 0)
                        ->first();
            
            if (!$user) {
                return $this->handleFail($throttleKey, $maxAttempts, 'NIP tidak terdaftar.', 404);
            }

            // Cek password menggunakan kolom PASSWORD_KARYAWAN
            if (!Hash::check($password, $user->PASSWORD_KARYAWAN)) {
                return $this->handleFail($throttleKey, $maxAttempts, 'Kata sandi salah.', 401);
            }

            $token = $this->makeClientToken($identifier, 'karyawan');

        } else {
            // Cari berdasarkan NISN_SISWA
            $user = MstSiswa::where('NISN_SISWA', $identifier)
                        ->where('IS_DELETE', 0)
                        ->first();
            
            if (!$user) {
                return $this->handleFail($throttleKey, $maxAttempts, 'NISN tidak terdaftar.', 404);
            }

            // Cek password menggunakan kolom PASSWORD_SISWA
            if (!Hash::check($password, $user->PASSWORD_SISWA)) {
                return $this->handleFail($throttleKey, $maxAttempts, 'Kata sandi salah.', 401);
            }

            $token = $this->makeClientToken($identifier, 'siswa');
        }

        // Login Berhasil
        RateLimiter::clear($throttleKey);

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'role' => $role === 'karyawan' ? $user->JABATAN_FUNGSIONAL : 'Siswa',
            'user' => $this->withFrontendAliases($user)
        ]);
    }

    private function handleFail($key, $max, $message, $statusCode)
    {
        RateLimiter::hit($key, 60);
        $remaining = RateLimiter::remaining($key, $max);

        return response()->json([
            'message' => $message,
            'attempts_left' => $remaining,
            'info' => "Sisa percobaan: $remaining kali."
        ], $statusCode);
    }

    private function makeClientToken(string $identifier, string $role): string
    {
        return hash('sha256', $role . '|' . $identifier . '|' . Str::random(40) . '|' . now()->timestamp);
    }

    private function withFrontendAliases($user): array
    {
        $data = $user->toArray();

        foreach ($data as $key => $value) {
            $data[strtolower($key)] = $value;
        }

        return $data;
    }
}

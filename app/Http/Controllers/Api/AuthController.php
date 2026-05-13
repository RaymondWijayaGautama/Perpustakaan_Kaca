<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\MstKaryawan;
use App\Models\MstSiswa;
use App\Support\AuditLogger;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $identifier = (string) $request->input('identifier', '');
        $password = $request->password;
        $role = Str::lower((string) $request->input('role', ''));
        $maxAttempts = 5;

        // 1. Verifikasi reCAPTCHA
        if (env('RECAPTCHA_SECRET_KEY')) {
            $recaptchaResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => env('RECAPTCHA_SECRET_KEY'),
                'response' => $request->input('g-recaptcha-response'),
            ]);

            if (!$recaptchaResponse->json('success')) {
                $this->logLoginAttempt($request, 'LOGIN_GAGAL', 'Verifikasi reCAPTCHA gagal.', 422, $identifier, $role);

                return response()->json(['message' => 'Verifikasi reCAPTCHA gagal.'], 422);
            }
        }

        $throttleKey = Str::lower($identifier) . '|' . $request->ip();

        // 2. Rate Limiting
        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->logLoginAttempt($request, 'LOGIN_DIBATASI', 'Terlalu banyak percobaan login.', 429, $identifier, $role);

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
                return $this->handleFail($throttleKey, $maxAttempts, 'NIP tidak terdaftar.', 404, $request, $identifier, $role);
            }

            // Cek password menggunakan kolom PASSWORD_KARYAWAN
            if (!Hash::check($password, $user->PASSWORD_KARYAWAN)) {
                return $this->handleFail($throttleKey, $maxAttempts, 'Kata sandi salah.', 401, $request, $identifier, $role);
            }

            $token = $user->createToken('karyawan_token')->plainTextToken;

        } else {
            // Cari berdasarkan NISN_SISWA
            $user = MstSiswa::where('NISN_SISWA', $identifier)
                        ->where('IS_DELETE', 0)
                        ->first();
            
            if (!$user) {
                return $this->handleFail($throttleKey, $maxAttempts, 'NISN tidak terdaftar.', 404, $request, $identifier, $role);
            }

            // Cek password menggunakan kolom PASSWORD_SISWA
            if (!Hash::check($password, $user->PASSWORD_SISWA)) {
                return $this->handleFail($throttleKey, $maxAttempts, 'Kata sandi salah.', 401, $request, $identifier, $role);
            }

            $token = $user->createToken('siswa_token')->plainTextToken;
        }

        // Login Berhasil
        RateLimiter::clear($throttleKey);

        $roleLabel = $role === 'karyawan' ? ($user->JABATAN_FUNGSIONAL ?: 'Karyawan') : 'Siswa';
        try {
            // Hotfix: Ensure access_log table has auto_increment set for ID_ACCESS_LOG
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE access_log MODIFY ID_ACCESS_LOG INT NOT NULL AUTO_INCREMENT');
        } catch (\Exception $e) {
            // Ignore if it fails or already exists
        }

        $accessLogId = DB::table('access_log')->insertGetId([
            'START_LOGIN' => now(),
            'END_LOGIN' => null,
            'USERNAME' => AuditLogger::truncate($identifier, 25),
            'ROLE' => AuditLogger::accessRole($roleLabel),
        ], 'ID_ACCESS_LOG');

        AuditLogger::logActivity([
            'access_log_id' => $accessLogId,
            'actor_username' => $identifier,
            'actor_role' => $roleLabel,
            'activity_name' => 'LOGIN_BERHASIL',
            'related_data' => 'api/login',
            'description' => AuditLogger::truncate("Berhasil LOGIN: POST /api/login | Status: 200 | Detail: username={$identifier}, role={$roleLabel}", 255),
        ]);

        $userData = $user->toArray();
        $userData['ACCESS_LOG_ID'] = $accessLogId;
        $userData['ROLE_LABEL'] = $roleLabel;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'role' => $roleLabel,
            'access_log_id' => $accessLogId,
            'user' => $userData
        ]);
    }

    public function logout(Request $request)
    {
        $actor = AuditLogger::actorFromRequest($request);

        if ($actor['access_log_id']) {
            DB::table('access_log')
                ->where('ID_ACCESS_LOG', $actor['access_log_id'])
                ->whereNull('END_LOGIN')
                ->update(['END_LOGIN' => now()]);
        }

        AuditLogger::logActivity([
            'access_log_id' => $actor['access_log_id'],
            'actor_username' => $actor['username'],
            'actor_role' => $actor['role'],
            'activity_name' => 'LOGOUT',
            'related_data' => 'api/logout',
            'description' => AuditLogger::truncate("Berhasil LOGOUT: POST /api/logout | Status: 200 | Detail: username={$actor['username']}, role={$actor['role']}", 255),
        ]);

        return response()->json(['message' => 'Logout berhasil dicatat.']);
    }

    private function handleFail($key, $max, $message, $statusCode, Request $request, ?string $identifier, ?string $role)
    {
        RateLimiter::hit($key, 60);
        $remaining = RateLimiter::remaining($key, $max);
        $this->logLoginAttempt($request, 'LOGIN_GAGAL', $message, $statusCode, $identifier, $role);

        return response()->json([
            'message' => $message,
            'attempts_left' => $remaining,
            'info' => "Sisa percobaan: $remaining kali."
        ], $statusCode);
    }

    private function logLoginAttempt(Request $request, string $activityName, string $message, int $statusCode, ?string $identifier, ?string $role): void
    {
        $roleLabel = AuditLogger::roleLabel($role);
        $username = $identifier !== '' ? $identifier : 'UNKNOWN';

        AuditLogger::logActivity([
            'access_log_id' => null,
            'actor_username' => $username,
            'actor_role' => $roleLabel,
            'activity_name' => $activityName,
            'related_data' => 'api/login',
            'description' => AuditLogger::truncate("Gagal LOGIN: POST /api/login | Status: {$statusCode} | Detail: username={$username}, role={$roleLabel}, alasan={$message}", 255),
        ]);
    }
}

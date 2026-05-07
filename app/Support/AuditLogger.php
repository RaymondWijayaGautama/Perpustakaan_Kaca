<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuditLogger
{
    public static function truncate(?string $value, int $length): ?string
    {
        if ($value === null) {
            return null;
        }

        return Str::limit($value, $length, '');
    }

    public static function roleLabel(?string $role): string
    {
        $role = trim((string) $role);

        if ($role === '') {
            return 'Unknown';
        }

        return self::truncate($role, 100);
    }

    public static function accessRole(?string $role): string
    {
        $role = strtolower(trim((string) $role));

        if ($role === 'pustakawan') {
            return 'Pustakawan';
        }

        if ($role === 'admin') {
            return 'Admin';
        }

        if ($role === 'siswa') {
            return 'Siswa';
        }

        if ($role === 'karyawan' || $role === 'guru') {
            return 'Karyawan';
        }

        return self::truncate($role !== '' ? ucfirst($role) : 'Unknown', 10);
    }

    public static function actorFromRequest(Request $request): array
    {
        return [
            'username' => self::truncate(
                $request->header('X-Actor-Username')
                    ?: $request->input('actor_username')
                    ?: $request->input('editor_nip_karyawan')
                    ?: $request->input('nip_karyawan')
                    ?: $request->input('id_siswa_tetap')
                    ?: $request->input('identifier')
                    ?: 'SYSTEM',
                25
            ),
            'role' => self::roleLabel(
                $request->header('X-Actor-Role')
                    ?: $request->input('actor_role')
                    ?: 'Unknown'
            ),
            'access_log_id' => self::validAccessLogId($request->header('X-Access-Log-Id') ?: $request->input('access_log_id')),
        ];
    }

    public static function validAccessLogId(mixed $value): ?int
    {
        if (!$value || !is_numeric($value)) {
            return null;
        }

        $id = (int) $value;

        try {
            return DB::table('access_log')->where('ID_ACCESS_LOG', $id)->exists() ? $id : null;
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }
    }

    public static function activityName(string $method, string $path, int $statusCode): string
    {
        $method = strtoupper($method);

        if ($statusCode >= 400) {
            return 'AKSI_GAGAL';
        }

        if (str_contains($path, 'login')) {
            return 'LOGIN';
        }

        if (str_contains($path, 'logout')) {
            return 'LOGOUT';
        }

        return match ($method) {
            'GET' => 'MELIHAT_DATA',
            'POST' => 'MENAMBAH_DATA',
            'PUT', 'PATCH' => 'MENGUBAH_DATA',
            'DELETE' => 'MENGHAPUS_DATA',
            default => 'AKSES_API',
        };
    }

    public static function relatedData(Request $request): string
    {
        $path = $request->path();
        $query = $request->query();

        if ($query !== []) {
            $path .= '?' . http_build_query($query);
        }

        return self::truncate($path, 100);
    }

    public static function description(Request $request, int $statusCode, ?string $customAction = null): string
    {
        $method = strtoupper($request->method());
        $path = '/' . ltrim($request->path(), '/');
        $result = $statusCode >= 400 ? 'Gagal' : 'Berhasil';
        $action = $customAction ?: self::activityName($method, $request->path(), $statusCode);
        $details = self::safeDetails($request);
        $detailText = $details === '' ? '' : " | Detail: {$details}";

        return self::truncate("{$result} {$action}: {$method} {$path} | Status: {$statusCode}{$detailText}", 255);
    }

    public static function safeDetails(Request $request): string
    {
        $allowedKeys = [
            'isbn',
            'ISBN',
            'id',
            'id_peminjaman',
            'id_siswa_tetap',
            'identifier',
            'search',
            'status',
            'kategori',
            'judul_koleksi',
            'kode_kategori',
        ];

        $pairs = [];

        foreach ($allowedKeys as $key) {
            $value = $request->input($key, $request->query($key));

            if ($value === null || is_array($value)) {
                continue;
            }

            $pairs[] = "{$key}=" . self::truncate((string) $value, 35);
        }

        return self::truncate(implode(', ', $pairs), 120) ?? '';
    }

    public static function logActivity(array $data): void
    {
        try {
            DB::table('activity_log')->insert([
                'ID_ACCESS_LOG' => $data['access_log_id'] ?? null,
                'EVENT_TIME' => now(),
                'ACTOR_USERNAME' => self::truncate($data['actor_username'] ?? 'SYSTEM', 25),
                'ACTOR_ROLE' => self::truncate($data['actor_role'] ?? 'Unknown', 100),
                'ACTIVITY_NAME' => self::truncate($data['activity_name'] ?? 'AKSES_API', 100),
                'RELATED_DATA' => self::truncate($data['related_data'] ?? '-', 100),
                'ACTIVITY_DESCRIPTION' => self::truncate($data['description'] ?? '-', 255),
            ]);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
